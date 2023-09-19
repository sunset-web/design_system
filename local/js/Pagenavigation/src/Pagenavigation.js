import { BitrixVue } from "ui.vue3";

export const Pagenavigation = BitrixVue.mutableComponent("Pagenavigation", {
	name: "Pagenavigation",
	emits: ["pagerFunc", "addNext"],
	props: ["CNT", "PageCNT"],
	mounted() {},
	data() {
		return {
			pageNumber: 1,
		};
	},
	methods: {
		pagerPrev() {
			if (this.pageNumber > 1) {
				this.pageNumber--;
				this.$emit("pagerFunc", this.pageNumber);
			}
		},
		pagerNext() {
			if (this.pageNumber < this.pagerCount) {
				this.pageNumber++;
				this.$emit("pagerFunc", this.pageNumber);
			}
		},
		pagerFirst() {
			if (this.pageNumber != 1) {
				this.pageNumber = 1;
				this.$emit("pagerFunc", this.pageNumber);
			}
		},
		pagerLast() {
			if (this.pageNumber != this.pagerCount) {
				this.pageNumber = this.pagerCount;
				this.$emit("pagerFunc", this.pageNumber);
			}
		},
		addNext() {
			if (this.pageNumber != this.pagerCount) {
				this.pageNumber++;
				this.$emit("addNext", this.pageNumber);
			}
		},
	},
	computed: {
		isFirst() {
			return 1 == this.pageNumber;
		},
		isLast() {
			return this.pagerCount == this.pageNumber;
		},
		pagerCount() {
			console.log(this.PageCNT);
			this.pageNumber = 1;
			return (
				Math.floor(this.CNT / this.PageCNT) + (this.CNT % this.PageCNT ? 1 : 0)
			);
		},
	},
	//
	/*html*/
	template: `
	<div class="show-more-cards_control show-more_cards-control--js" v-if="pagerCount>1">
		<a
		@click="addNext"
		href="javascript: void(0)"
		class="as-button m-0-a arrow-down discount-promo_more"
		:class="{vh: this.pagerCount == this.pageNumber}"
		>
			{{$Bitrix.Loc.getMessage('SEE_MORE')}}
		</a>
		<div class="show-more-cards_pages">
			<a
			@click="pagerPrev"
			class="show-more-cards_pages-prev"
			:disabled="1 == pageNumber"
			href="javascript: void(0)">
				<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.6333 0.600098L1.2333 7.0001L7.6333 13.4001" stroke="#808080" stroke-linecap="round" />
				</svg>
			</a>
			<ul>
				<li :class="{ active: isFirst }">
					<a
					@click="pagerFirst"
					:disabled="isFirst"
					href="javascript: void(0)">
						1
					</a>
				</li>
				<li><span>...</span></li>
				<template v-if="1 != this.pageNumber && this.pageNumber != this.pagerCount">
					<li class="active"><span>{{this.pageNumber}}</span></li>
					<li><span>...</span></li>
				</template>

				<li :class="{ active: isLast }">
					<a
					@click="pagerLast"
					:disabled="isLast"
					href="javascript: void(0)">
						{{this.pagerCount}}
					</a>
				</li>
			</ul>
			<a
			@click="pagerNext"
			:disabled="pagerCount == pageNumber"
			class="show-more-cards_pages-next"
			href="javascript: void(0)">
				<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.3667 13.3999L7.7667 6.9999L1.3667 0.599902" stroke="#808080" stroke-linecap="round" />
				</svg>
			</a>
		</div>
	</div>
  `,
});
