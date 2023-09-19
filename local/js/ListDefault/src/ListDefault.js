import { BitrixVue } from "ui.vue3";
import { ElementBlock } from "./ElementBlock";
import { BaseEvent } from "main.core.events";
//
export const ListDefault = BitrixVue.mutableComponent("ListDefault", {
	name: "ListDefault",
	components: {
		ElementBlock,
		Pagenavigation: BitrixVue.defineAsyncComponent(
			"oft.Pagenavigation",
			"Pagenavigation"
		),
		SearchForm: BitrixVue.defineAsyncComponent("oft.SearchForm", "SearchForm"),
		FiltersForm: BitrixVue.defineAsyncComponent(
			"oft.FiltersForm",
			"FiltersForm"
		),
	},
	data() {
		return {
			ArResult: BX.SalesListResult,
			ArParams: BX.SalesListParams,
			AjaxTemplatePath: BX.ComponentAjaxTemplatePath,
			ITEMS: [],
			FILTERS: [],
			CNT: 0,
			RenderPagination: 0,
			SearchInput: "",
			NeedProps: false,
			StartFilter: {},
		};
	},
	mounted() {
		// получение списка
		BX.ajax({
			url: this.AjaxTemplatePath,
			data: {
				QUERY: "getlist",
				IBLOCK_ID: this.ArParams.IBLOCK_ID,
				PAGE_NUMBER: 1,
				PAGE_ELEMENT_COUNT: this.ArParams.NEWS_COUNT,
				NEED_PROPS: this.NeedProps,
				...this.StartFilter,
			},
			method: "POST",
			dataType: "json",
			timeout: 30,
			async: true,
			processData: true, // нужно ли сразу обрабатывать данные?
			scriptsRunFirst: true, // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
			emulateOnload: true, // нужно ли эмулировать событие window.onload для загруженных скриптов
			start: true, // отправить ли запрос сразу или он будет запущен вручную
			cache: false, // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
			onsuccess: (response) => {
				this.ITEMS = response.ITEMS;
				this.CNT = response.CNT;
			},
			onfailure: (response) => {
				console.error(response);
			},
		});
		// получение фильтров
		BX.ajax({
			url: this.AjaxTemplatePath,
			data: {
				QUERY: "filters",
				IBLOCK_ID: this.ArParams.IBLOCK_ID,
				CODES: this.ArParams.FILTER_PROPERTY_CODE,
			},
			method: "POST",
			dataType: "json",
			timeout: 30,
			async: true,
			processData: true, // нужно ли сразу обрабатывать данные?
			scriptsRunFirst: true, // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
			emulateOnload: true, // нужно ли эмулировать событие window.onload для загруженных скриптов
			start: true, // отправить ли запрос сразу или он будет запущен вручную
			cache: false, // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
			onsuccess: (response) => {
				this.FILTERS = response;
			},
			onfailure: (response) => {
				console.error(response);
			},
		});

		this.$Bitrix.eventEmitter.subscribe("filter", this.Filtered);
		this.$Bitrix.eventEmitter.subscribe("clearFilter", this.ClearFilter);
	},
	beforeUnmount() {
		this.$Bitrix.eventEmitter.unsubscribe("filter", this.Filtered);
	},
	methods: {
		// общий ajax
		GetList(params, func) {
			const arrFilter = [];
			for (const filter in this.FILTERS) {
				if (this.FILTERS[filter]["QURRENT_VAL"])
					arrFilter["=PROPERTY_" + this.FILTERS[filter].ID] =
						this.FILTERS[filter]["QURRENT_VAL"];
			}
			if (this.StartFilter) {
				params = { ...params, ...this.StartFilter };
			}
			params.FILTERS = arrFilter;
			params.SEARCH = this.SearchInput;
			params.IBLOCK_ID = this.ArParams.IBLOCK_ID;
			params.PAGE_ELEMENT_COUNT = this.ArParams.NEWS_COUNT;
			params.QUERY = "getlist";
			params.NEED_PROPS = this.NeedProps;
			BX.ajax({
				url: this.AjaxTemplatePath,
				data: params,
				method: "POST",
				dataType: "json",
				timeout: 30,
				async: true,
				processData: true, // нужно ли сразу обрабатывать данные?
				scriptsRunFirst: true, // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
				emulateOnload: true, // нужно ли эмулировать событие window.onload для загруженных скриптов
				start: true, // отправить ли запрос сразу или он будет запущен вручную
				cache: false, // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
				onsuccess: (response) => {
					func(response);
				},
				onfailure: (response) => {
					console.error(response);
				},
			});
		},
		// переключение страницы
		ChangePage(pageN) {
			this.GetList(
				{
					PAGE_NUMBER: pageN,
				},
				(res) => {
					this.ITEMS = res.ITEMS;
				}
			);
		},
		// показать еще
		AddNext(pageN) {
			this.GetList(
				{
					PAGE_NUMBER: pageN,
				},
				(res) => {
					this.ITEMS.push(...res.ITEMS);
				}
			);
		},
		// фильтрация
		Filtered(event) {
			let data = event.getData();
			this.FILTERS[data.name]["QURRENT_VAL"] = data.values;
			let Filtered = () => {
				this.GetList(
					{
						PAGE_NUMBER: 1,
					},
					(res) => {
						this.ITEMS = res.ITEMS;
						this.CNT = res.CNT;
						this.RenderPagination++;
					}
				);
			};
			debounce(Filtered, 400)();
		},
		SearchInputEmit(e) {
			this.SearchInput = e;
		},
		ClearFilter() {
			for (const key in this.FILTERS) {
				if (Object.hasOwnProperty.call(this.FILTERS, key)) {
					const element = this.FILTERS[key];
					this.FILTERS[key]["QURRENT_VAL"] = "";
				}
			}

			this.GetList(
				{
					PAGE_NUMBER: 1,
				},
				(res) => {
					this.ITEMS = res.ITEMS;
					this.CNT = res.CNT;
					this.RenderPagination++;
				}
			);
		},
	},
	watch: {
		SearchInput() {
			this.GetList(
				{
					PAGE_NUMBER: 1,
				},
				(res) => {
					this.ITEMS = res.ITEMS;
					this.CNT = res.CNT;
					this.RenderPagination++;
				}
			);
		},
	},
	/* html */
	template: `
			<SearchForm @searching="SearchInputEmit" :filtersOn="FILTERS.length != 0"></SearchForm>
			<FiltersForm :FILTERS="FILTERS"></FiltersForm>
			<ElementBlock v-for="item in ITEMS" :ITEM="item" :key="item.ID" ></ElementBlock>
			<div v-if="ITEMS==null">{{$Bitrix.Loc.getMessage('SALE_NOT_FOUND')}}</div>
			<Pagenavigation @pagerFunc="ChangePage" @AddNext="AddNext" :CNT="CNT" :PageCNT="ArParams.NEWS_COUNT" :key="RenderPagination"></Pagenavigation>
			`,
});
