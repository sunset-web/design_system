import { BitrixVue } from "ui.vue3";
import { mapStores } from "ui.vue3.pinia";
import { dropListHeaderStore } from "../../droplist-header/src/droplist-headerStore";
/**
 * дефолтный компонент реализующий логику получения элементов
 * запись в хранилище
 * обработка и передача на вывод
 */
export const HlList = BitrixVue.mutableComponent('HlList', {
	name: 'HlList',
	props: ['arParams'],
	emits: ["pagerFunc"],
	data() {
		return {
			Total: 0,
			RenderPagination: 0,
			showBtn: true,
			CountPageDef: this.arParams.COUNT_PAGE,
			items: [],
		};
	},
	computed: {
		...mapStores(dropListHeaderStore),
	},
	methods: {
		GetList(currentPage, count = this.CountPageDef) {
			// запрос на получение списка
			BX.ajax
				.runComponentAction("itin:hl.news.list", "getList", {
					mode: "class",
					data: {
						hl_id: this.arParams.HL_ID,
						current_page: currentPage,
						count_page: count,
					},
				})
				.then((response) => {

					this.items = response.data.result;

					removePreloader($(".notifications-news__list"));
				});
		},
		// переключение страницы
		ChangePage(pageN) {
			setPreloader($(".notifications-news__list"));
			this.GetList(pageN);
		},
		// переключение кол-ва
		CountPage(res) {
			setPreloader($(".notifications-news__list"));
			this.CountPageDef = res.data.count;
			this.GetList(res.data.pageN, res.data.count);
		},
		// изменение просмотренности элемента
		updateView(res) {

			let result = (async () => {
				return await BX.ajax
					.runComponentAction("itin:hl.news.list", "update", {
						mode: "class",
						data: {
							hl_id: this.arParams.HL_ID,
							ids: [res.data.id],
						},
					})
					.then((response) => {
						if (response.data.result) {
							// Получаем непрочитанные сообщения из хранилища
							let units = this.dropListHeaderStoreStore.getUnit(this.arParams.HL_ID);
							// Если нет, скрываем кнопку
							if (!units.list.length) {
								this.showBtn = false;
							}
							return response.data.result;
						}
					}).catch(() => {
						return false;
					});
			})();

			return result;

		},
		// изменение всех на прочитанные
		updateAll() {
			BX.ajax
				.runComponentAction("itin:hl.news.list", "update", {
					mode: "class",
					data: {
						hl_id: this.arParams.HL_ID,
					},
				})
				.then((response) => {
					if (response.data.result) {
						this.showBtn = false;
						$('.notifications-news__list li').removeClass('checked');
					}
				});
		},
	},
	mounted() {
		// Получаем непрочитанные сообщения из хранилища
		let units = this.dropListHeaderStoreStore.getUnit(this.arParams.HL_ID);
		// Если нет, скрываем кнопку
		if (!units.list.length) {
			this.showBtn = false;
		}

		this.$Bitrix.eventEmitter.subscribe("countpage", this.CountPage);
		this.$Bitrix.eventEmitter.subscribe("updateview", this.updateView);
		// получаем гет параметры
		const urlParams = new URL(window.location.href);
		const pagen = urlParams.searchParams.get('PAGEN_1');
		const count = urlParams.searchParams.get('count');
		if (count) this.CountPageDef = count;

		// запрос на получение списка
		BX.ajax
			.runComponentAction("itin:hl.news.list", "getList", {
				mode: "class",
				data: {
					hl_id: this.arParams.HL_ID,
					current_page: pagen,
					count_page: this.CountPageDef,
				},
			})
			.then((response) => {

				Object.assign(this.items, response.data.result);

				this.Total = response.data.count;
			});

	},
});