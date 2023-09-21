import { BitrixVue } from "ui.vue3";
import { createPinia } from "ui.vue3.pinia";
import { Item } from "./components/Item";
import { DroplistHeader } from "local.droplist-header";
const store = createPinia();
/**
 * клонирующий от дефолтного компонент уведомлений
 * изменяется шаблон и внутренний компонент
 * пробрасываются параметры
 */
const DroplistHeaderNotificationsClone = BitrixVue.cloneComponent(DroplistHeader, {
	components: {
		Item,
	},
	/*html*/
	template: `
	<div class="header-blue__notifications header-blue__droplist--js">
				<a v-if="link" :href="link" class="flex ai-center jc-c header-blue__notifications-btn header-blue__droplist-btn--js">
					<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M19.5795 15.1893C19.4932 15.0911 19.4083 14.9929 19.3251 14.8981C18.1802 13.5914 17.4876 12.8027 17.4876 9.10348C17.4876 7.1883 17.002 5.61687 16.045 4.4383C15.3394 3.56763 14.3855 2.90714 13.1282 2.41902C13.112 2.41053 13.0976 2.39938 13.0855 2.38612C12.6333 0.957098 11.3958 0 10.0001 0C8.60443 0 7.36745 0.957098 6.91523 2.38464C6.90316 2.39742 6.88892 2.40821 6.87308 2.41656C3.9391 3.55634 2.51322 5.74308 2.51322 9.10201C2.51322 12.8027 1.82161 13.5914 0.675703 14.8967C0.59244 14.9914 0.507615 15.0877 0.42123 15.1879C0.198085 15.4418 0.0567046 15.7508 0.0138202 16.0782C-0.0290643 16.4056 0.0283424 16.7377 0.179246 17.0353C0.50033 17.6737 1.18465 18.07 1.96576 18.07H18.0402C18.8177 18.07 19.4973 17.6742 19.8195 17.0387C19.971 16.7411 20.029 16.4086 19.9864 16.0809C19.9439 15.7531 19.8027 15.4437 19.5795 15.1893ZM10.0001 22C10.7521 21.9994 11.4899 21.8068 12.1353 21.4426C12.7807 21.0783 13.3095 20.556 13.6658 19.9311C13.6826 19.9012 13.6909 19.8676 13.6899 19.8338C13.6888 19.7999 13.6786 19.7669 13.66 19.7378C13.6415 19.7088 13.6153 19.6848 13.584 19.6682C13.5528 19.6516 13.5175 19.6428 13.4816 19.6429H6.51973C6.48379 19.6427 6.44842 19.6514 6.41708 19.668C6.38574 19.6846 6.35949 19.7086 6.34088 19.7376C6.32228 19.7667 6.31196 19.7997 6.31093 19.8336C6.3099 19.8675 6.31819 19.9011 6.33499 19.9311C6.69122 20.556 7.22 21.0782 7.86527 21.4424C8.51053 21.8067 9.24823 21.9993 10.0001 22Z" fill="currentColor" />
					</svg>
					<!-- модификатор _active для показа количества уведомлений -->
					<div v-if="countItems" class="header__counter header__counter_more header__counter_active" :class="{header__counter_active_more: countItems>10}">{{countItems}}</div>
				</a>
				<span v-else class="flex ai-center jc-c header-blue__notifications-btn header-blue__droplist-btn--js">
					<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M19.5795 15.1893C19.4932 15.0911 19.4083 14.9929 19.3251 14.8981C18.1802 13.5914 17.4876 12.8027 17.4876 9.10348C17.4876 7.1883 17.002 5.61687 16.045 4.4383C15.3394 3.56763 14.3855 2.90714 13.1282 2.41902C13.112 2.41053 13.0976 2.39938 13.0855 2.38612C12.6333 0.957098 11.3958 0 10.0001 0C8.60443 0 7.36745 0.957098 6.91523 2.38464C6.90316 2.39742 6.88892 2.40821 6.87308 2.41656C3.9391 3.55634 2.51322 5.74308 2.51322 9.10201C2.51322 12.8027 1.82161 13.5914 0.675703 14.8967C0.59244 14.9914 0.507615 15.0877 0.42123 15.1879C0.198085 15.4418 0.0567046 15.7508 0.0138202 16.0782C-0.0290643 16.4056 0.0283424 16.7377 0.179246 17.0353C0.50033 17.6737 1.18465 18.07 1.96576 18.07H18.0402C18.8177 18.07 19.4973 17.6742 19.8195 17.0387C19.971 16.7411 20.029 16.4086 19.9864 16.0809C19.9439 15.7531 19.8027 15.4437 19.5795 15.1893ZM10.0001 22C10.7521 21.9994 11.4899 21.8068 12.1353 21.4426C12.7807 21.0783 13.3095 20.556 13.6658 19.9311C13.6826 19.9012 13.6909 19.8676 13.6899 19.8338C13.6888 19.7999 13.6786 19.7669 13.66 19.7378C13.6415 19.7088 13.6153 19.6848 13.584 19.6682C13.5528 19.6516 13.5175 19.6428 13.4816 19.6429H6.51973C6.48379 19.6427 6.44842 19.6514 6.41708 19.668C6.38574 19.6846 6.35949 19.7086 6.34088 19.7376C6.32228 19.7667 6.31196 19.7997 6.31093 19.8336C6.3099 19.8675 6.31819 19.9011 6.33499 19.9311C6.69122 20.556 7.22 21.0782 7.86527 21.4424C8.51053 21.8067 9.24823 21.9993 10.0001 22Z" fill="currentColor" />
					</svg>
					<!-- модификатор _active для показа количества уведомлений -->
					<div v-if="countItems" class="header__counter header__counter_more header__counter_active" :class="{header__counter_active_more: countItems>10}">{{countItems}}</div>
				</span>
			
				<div class="header__modal">
					<ul v-if="this.items.length">
						<Item v-for="item in items" :item="item" :key="item.id"></Item>
					</ul>
					<div v-else class="no-messages">{{$Bitrix.Loc.getMessage('EMPTY_NOTIFICATIONS')}}</div>
					<a v-if="link" :href="link">{{$Bitrix.Loc.getMessage('LINK_NOTIFICATIONS')}}</a>
				</div>
			</div>
	`,
});

export class DroplistHeaderNotifications {
	#application;

	constructor(rootNode) {

		this.rootNode = document.querySelector(rootNode);

	}

	init(arParams) {

		this.#application = BitrixVue.createApp({
			name: "DroplistHeaderNotifications",
			props: ['arParams'],
			components: {
				DroplistHeaderNotificationsClone
			},
			/* html */
			template: `
					<DroplistHeaderNotificationsClone :arParams="arParams"></DroplistHeaderNotificationsClone>
			  `,
		}, {
			arParams: arParams,
		});
		this.#application.use(store).mount(this.rootNode);
		// this.#application.mount(this.rootNode);
	}
}