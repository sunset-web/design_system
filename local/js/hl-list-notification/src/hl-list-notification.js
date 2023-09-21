import { BitrixVue } from "ui.vue3";
import { Item } from "./components/Item";
import { Pagenavigation } from "local.Pagenavigation";

/**
 * мутирующий от дефолтного компонент список уведомлений
 * изменяется шаблон и внутренний компонент
 * пробрасываются параметры
 */
BitrixVue.mutateComponent('HlList', {
	components: {
		Item,
		Pagenavigation,
	},
	/*html*/
	template: `
	<template v-if="items.length">
		<template v-if="showBtn">
			<button @click.stop="updateAll" class="notifications-news__check-button">{{$Bitrix.Loc.getMessage('BTN_VIEW_ALL')}}</button>
		</template>
		<ul class="notifications-news__list">
			<Item v-for="item in items" :item="item" :key="item.id"></Item>
		</ul>
		<!-- пагинация появляется при >= 10 элементов -->
		<Pagenavigation @pagerfunc="ChangePage" :Total="Total" :PageCNTDef="arParams.COUNT_PAGE" :PageCNT="this.CountPageDef" :key="RenderPagination"></Pagenavigation>
	</template>
   <div  v-else class="no-messages">{{$Bitrix.Loc.getMessage('ITEMS_NOT_FOUND')}}</div>
	`,
});

export class HlListNotification {
	#application;

	constructor(rootNode) {

		this.rootNode = document.querySelector(rootNode);

	}

	init(arParams) {

		this.#application = BitrixVue.createApp({
			name: "HlListNotification",
			data() {
				return {
					arParams: arParams,
				}
			},
			components: {
				HlListMutate: BitrixVue.defineAsyncComponent(
					"local.hl-list",
					"HlList"
				),
			},
			/* html */
			template: `
					<HlListMutate :arParams="arParams"></HlListMutate>
			  `,
		});
		this.#application.mount(this.rootNode);
	}
}