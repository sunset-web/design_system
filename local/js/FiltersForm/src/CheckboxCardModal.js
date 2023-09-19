import { BitrixVue } from "ui.vue3";
import { BaseEvent } from "main.core.events";

export const CheckboxCardModal = {
	name: "CheckboxCardModal",
	props: ["value", "name"],
	data() {
		return {
			checked: [],
			showMoreCount: 2,
		};
	},
	mounted() {
		this.$Bitrix.eventEmitter.subscribe("clearFilter", this.ClearFilter);
	},
	methods: {
		filtered() {
			console.log(this.name);
			this.$Bitrix.eventEmitter.emit("filter", {
				name: this.name,
				values: this.checked,
			});
		},
		showMore(e) {
			console.log($(e).hasClass("open"));
			if ($(e).hasClass("open")) {
				$(e).removeClass("open");
				let hBlock = $(e)
					.parents(".show-more-filters-wrap--js")
					.find(".show-more-filters-block--js")
					.attr("data-show-height-block");
				$(e)
					.parents(".show-more-filters-wrap--js")
					.find(".show-more-filters-block--js")
					.css("max-height", hBlock + "px");
			} else {
				$(e).addClass("open");
				let hBlock = $(e)
					.parents(".show-more-filters-wrap--js")
					.find(".show-more-filters-block--js")
					.attr("data-height-block");
				$(e.target)
					.parents(".show-more-filters-wrap--js")
					.find(".show-more-filters-block--js")
					.css("max-height", hBlock + "px");
			}
		},
		ClearFilter() {
			this.checked = [];
		},
	},
	watch: {
		checked() {
			this.filtered();
		},
	},
	/*html*/
	template: `
		<div class="filter show-more-filters-wrap--js" :data-show-more-filters="showMoreCount">
			<div class="title">{{value.NAME}}</div>
			<div class="filter_wrapper show-more-filters-block--js">
				<div class="checkbox_wrapper show-more-filters-elem--js" v-for="variable of value.LIST">
					<label class="custom-checkbox option-label">
						<input type="checkbox" :value="variable.ID" v-model="checked">
						<span class="checkbox_content">{{variable.NAME}}</span>
					</label>
				</div>
			</div>
			<button @click="showMore" v-if="value.LIST.length > showMoreCount" class="arrow-down show-more-filters-btn show-more-filters-btn--js">Показать ещё</button>
		</div>
  `,
};
