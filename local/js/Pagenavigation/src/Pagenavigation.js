import { BitrixVue } from "ui.vue3";
import { CountSelect } from "./components/CountSelect";

export const Pagenavigation = BitrixVue.mutableComponent("Pagenavigation", {
	name: "Pagenavigation",
	emits: ["pagerfunc"],
	props: ["Total", "PageCNT", "PageCNTDef"],
	components: {
		CountSelect
	},
	data() {
		return {
			pageNumber: 1,
			pagenQuery: 'PAGEN_1',
		};
	},
	methods: {
		// Переключение на предыдущую страницу
		pagerPrev() {
			if (this.pageNumber > 1) {
				this.pageNumber--;

				let urlParams = new URL(window.location.href);
				if (this.pageNumber == 1) {
					urlParams.searchParams.delete(this.pagenQuery);
				} else {
					urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
				}
				history.pushState(null, null, urlParams);

				this.$emit("pagerfunc", this.pageNumber);
			}
		},
		// Переключение на следующую страницу
		pagerNext() {
			if (this.pageNumber < this.pagerCount) {
				this.pageNumber++;

				let urlParams = new URL(window.location.href);
				urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
				history.pushState(null, null, urlParams);

				this.$emit("pagerfunc", this.pageNumber);
			}
		},
		// Переключение на первую страницу
		pagerFirst() {
			if (this.pageNumber != 1) {
				this.pageNumber = 1;

				let urlParams = new URL(window.location.href);
				urlParams.searchParams.delete(this.pagenQuery);
				history.pushState(null, null, urlParams);

				this.$emit("pagerfunc", this.pageNumber);
			}
		},
		// Переключение на последнюю страницу
		pagerLast() {
			if (this.pageNumber != this.pagerCount) {
				this.pageNumber = this.pagerCount;

				let urlParams = new URL(window.location.href);
				urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
				history.pushState(null, null, urlParams);

				this.$emit("pagerfunc", this.pageNumber);
			}
		},
	},
	computed: {
		// Первая страница
		isFirst() {
			return 1 == this.pageNumber;
		},
		// Последняя страница
		isLast() {
			return this.pagerCount == this.pageNumber;
		},
		// Предыдущая страница от текущей
		isPrevFirst() {
			return 1 == this.pageNumber - 1;
		},
		// Следующая страница от текущей
		isNextLast() {
			return this.pagerCount == this.pageNumber + 1;
		},
		// Кол-ва элементов на стр (из скольки)
		offsetCount() {
			return this.isLast ? Number(this.Total) : Number(this.pageNumber) * Number(this.PageCNT);
		},
		// Кол элементов на стр (сколько)
		offset() {
			return this.isFirst ? 1 : Number(this.pageNumber) * Number(this.PageCNT) - Number(this.PageCNT) + 1;
		},
		// рассчет стр
		pagerCount() {
			return (
				Math.floor(this.Total / this.PageCNT) + (this.Total % this.PageCNT ? 1 : 0)
			);
		},
	},
	mounted() {
		let urlParams = new URL(window.location.href);
		this.pageNumber = urlParams.searchParams.get('PAGEN_1') ? Number(urlParams.searchParams.get('PAGEN_1')) : this.pageNumber;
	},
	//
	/*html*/
	template: `
	<div class="bottom-panel flex jc-sb ai-center small">
				<p>{{$Bitrix.Loc.getMessage('PAGE_RESULT-TITLE',{'#OFFSET#': this.offset,'#OFFSET_COUNT#': this.offsetCount,'#TOTAL#': this.Total})}}</p>
	
				<div class="pagination-buttons-group flex ai-center"  v-if="pagerCount>1 && PageCNT < Total">
					<a class="page-button"  @click.stop="pagerPrev"
					:disabled="1 == pageNumber" href="javascript: void(0)">
						<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.25 0.5L0.75 5L5.25 9.5" stroke="#1B254A" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
					<a class="page" :class="{ current: isFirst }"  @click.stop="pagerFirst" href="javascript: void(0)" :disabled="isFirst">1</a>
					<template v-if="!isPrevFirst && !isFirst">
						<a class="page" href="javascript:void(0)" @click.stop="pagerPrev">{{this.pageNumber-1}}</a>
					</template>
					<template v-if="1 != this.pageNumber && this.pageNumber != this.pagerCount">
						<a class="page current" href="javascript:void(0)">{{this.pageNumber}}</a>
					</template>
					<template v-if="!isNextLast && !isLast">
						<a class="page" href="javascript:void(0)" @click.stop="pagerNext">{{this.pageNumber+1}}</a>
					</template>
					<a class="page" :class="{ current: isLast }" href="javascript:void(0)" @click.stop="pagerLast">{{this.pagerCount}}</a>
					<a class="page-button" @click.stop="pagerNext" href="javascript: void(0)" :disabled="pagerCount == pageNumber">
						<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M0.75 9.5L5.25 5L0.75 0.5" stroke="#1B254A" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</a>
				</div>
	
				<CountSelect :count="this.PageCNTDef" :countDef="this.PageCNT" :current="this.pageNumber"></CountSelect>
			</div>
  `,
});
