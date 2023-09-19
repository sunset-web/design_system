import { BitrixVue } from "ui.vue3";
import { CheckboxCard } from "./CheckboxCard";
import { CheckboxCardModal } from "./CheckboxCardModal";
export const FiltersForm = BitrixVue.mutableComponent("FiltersForm", {
	name: "FiltersForm",
	props: ["FILTERS", "FilterClick"],
	components: {
		CheckboxCard,
		CheckboxCardModal,
	},
	data() {
		return {
			checked: [],
		};
	},
	mounted() {
		//Вычисление максимальной высоты и высоты зависимости от кол-ва пунктов в момент открытия модального окна
		if ($(".filters-modal.modal--js").length) {
			$(".filters-modal.modal--js").css("display", "block");
			console.log("op");
			$(".show-more-filters-wrap--js").each(function () {
				if (
					$(this).find(".show-more-filters-elem--js").length >
					$(this).attr("data-show-more-filters")
				) {
					$(this)
						.find(".show-more-filters-block--js")
						.attr(
							"data-height-block",
							$(this).find(".show-more-filters-block--js").innerHeight()
						);
					let hShowBlock = 0;

					for (let i = 0; i < $(this).attr("data-show-more-filters"); i++) {
						let wElem = $(this)
							.find(".show-more-filters-elem--js")
							.eq(i)
							.innerHeight();
						hShowBlock += wElem;
					}

					$(this)
						.find(".show-more-filters-block--js")
						.attr("data-show-height-block", hShowBlock);
					$(this)
						.find(".show-more-filters-block--js")
						.css("max-height", hShowBlock);
					$(this).find(".show-more-filters-btn--js").css("display", "block");
				}
			});
			$(".filters-modal.modal--js").css("display", "none");
		}
	},
	methods: {
		clear() {
			this.$Bitrix.eventEmitter.emit("clearFilter", {});
		},
	},
	/*html*/
	template: `

	<template v-if="FILTERS.length != 0">


		<div class="filters_wrapper">
			<CheckboxCard v-for="(value, name) in FILTERS" :key="name" :value="value" :name="name"></CheckboxCard>
    </div>

		<div class="modal filters-modal modal--js" data-modal="filters">
			<div class="modal-wrap-scroll">
				<button class="close-btn close-modal--js">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path d="M13.4868 11.8939L13.3807 12L13.4869 12.1061L17.9428 16.5592C17.9428 16.5592 17.9428 16.5592 17.9428 16.5592C18.126 16.7424 18.2289 16.9909 18.2289 17.25C18.2289 17.5091 18.126 17.7576 17.9428 17.9408C17.7595 18.124 17.5111 18.2269 17.2519 18.2269C16.9928 18.2269 16.7444 18.124 16.5611 17.9408L12.108 13.4877L12.002 13.3816L11.8959 13.4877L7.44276 17.9408C7.25955 18.124 7.01106 18.2269 6.75195 18.2269C6.49285 18.2269 6.24436 18.124 6.06114 17.9408C5.87793 17.7576 5.775 17.5091 5.775 17.25C5.775 16.9909 5.87793 16.7424 6.06114 16.5592L10.5143 12.1061L10.6203 12L10.5143 11.8939L6.06114 7.44081C5.87793 7.25759 5.775 7.0091 5.775 6.75C5.775 6.49089 5.87793 6.2424 6.06114 6.05919C6.24436 5.87598 6.49285 5.77305 6.75195 5.77305C7.01105 5.77305 7.25955 5.87598 7.44276 6.05919L11.8959 10.5123L12.002 10.6184L12.108 10.5123L16.561 6.05928C16.7442 5.87642 16.9926 5.77381 17.2514 5.77404C17.5102 5.77427 17.7584 5.87731 17.9413 6.0605L18.0468 5.95519L17.9413 6.0605C18.1241 6.24369 18.2267 6.49202 18.2265 6.75086C18.2263 7.0097 18.1232 7.25785 17.94 7.44071L17.9399 7.44081L13.4868 11.8939Z" stroke="white" stroke-width="0.3" />
					</svg>
				</button>
				<h2 class="mb-24-fixed tal not-scroll--js">Фильтры</h2>

				<div class="filters-modal_wrapper mb-24-fixed scroll-part">
						<CheckboxCardModal v-for="(value, name) in FILTERS" :key="name" :value="value" :name="name"></CheckboxCardModal>
				</div>
				<div class="filters-modal_btns not-scroll--js">
					<a href="javascript:void(0)" class="as-button m-0-a green btn_apply--js close-modal--js">Применить</a>
					<a @click="clear" href="javascript:void(0)" class="m-0-a btn_reset btn_reset--js">Сбросить все фильтры</a>
				</div>
			</div>
		</div>
	</template>

  `,
});
