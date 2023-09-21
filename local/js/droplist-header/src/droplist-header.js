import { BitrixVue } from "ui.vue3";
import { mapActions } from "ui.vue3.pinia";
import { Item } from "./components/Item";
import { dropListHeaderStore } from "./droplist-headerStore";
/**
 * дефолтный компонент реализующий логику получения элементов
 * запись в хранилище
 * обработка и передача на вывод
 */
export const DroplistHeader = BitrixVue.mutableComponent('DroplistHeader', {
	name: 'DroplistHeader',
	components: {
		Item,
	},
	props: ['arParams'],
	data() {
		return {

			link: this.arParams.LINK_PAGE,
			items: [],
			itemsLen: [],
		};
	},
	computed: {
		countItems() {
			// Кол-во элементов для отображения
			return this.itemsLen.length > 10 ? '10+' : Number(this.itemsLen.length);
		},
	},
	methods: {
		// Берем лишь нужное кол-во на вывод
		arrSlice(arr) {
			arr.splice(this.arParams.COUNT, arr.length);
		},
		// Добавление элемента
		addItem(response) {

			// Собираем элемент
			let objAdd = {
				ID: String(response.id),
				...response.fields,
			};

			// Проверяем HL-блок
			if (this.arParams.HL_ID == response.hl_id) {

				this.items.unshift(objAdd);
				this.itemsLen.unshift(objAdd);

				this.arrSlice(this.items);

				this.constructUnit(response.hl_id, this.itemsLen);

			}
		},
		updateItem(response) {
			// Проверяем флаг просмотра
			if (response.view == 'true' && this.arParams.HL_ID == response.hl_id) {

				Object.assign(this.items, this.itemsLen);
				// Удаляем элементы
				this.items.map((item, index) => {
					if (item.ID == response.id) {
						this.items.splice(index, 1);
						this.itemsLen.splice(index, 1);
					}
				});

				this.arrSlice(this.items);

				this.constructUnit(response.hl_id, this.itemsLen);

			}

		},
		// Получение списка методов хранилища
		...mapActions(dropListHeaderStore, {
			constructUnit: "constructUnit",
			getUnit: "getUnit",
		}),
	},
	mounted() {
		// запрос на получение списка
		BX.ajax
			.runComponentAction("itin:hl.news.list", "getList", {
				mode: "class",
				data: {
					hl_id: this.arParams.HL_ID,
				},
			})
			.then((response) => {

				Object.assign(this.itemsLen, response.data.result);
				Object.assign(this.items, response.data.result);

				this.arrSlice(this.items);
				// запись в хранилище
				this.constructUnit(this.arParams.HL_ID, this.itemsLen);
			});
		// Подписка на событие
		BX.PULL.start();
		BX.addCustomEvent("onPullEvent", (module_id, command, params) => {
			if (module_id == "droplistheader") {
				if (command == "check") {
					this.addItem(params.response);
				}
				if (command == "update") {
					this.updateItem(params.response);
				}
			}
		});

	},
});