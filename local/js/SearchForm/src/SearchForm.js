import { BitrixVue } from "ui.vue3";
export const SearchForm = BitrixVue.mutableComponent("SearchForm", {
	name: "SearchForm",
	props: ["val", "filtersOn"],
	mounted() {
		this.SearchInput == this.val;
	},
	data() {
		return {
			SearchInput: this.val ? this.val : "",
		};
	},
	watch: {
		SearchInput() {
			let search = () => {
				this.$emit("searching", this.SearchInput);
			};
			debounce(search, 300)();
		},
	},
	/*html*/
	template: `
    <div class="search_wrapper">
      <search role="search" class="search--js" :class="{'active':SearchInput}">
        <form action="javascript: void(0)" method="get" class="search-form--js">
          <input type="search" v-model="SearchInput" :placeholder="$Bitrix.Loc.getMessage('SALE_SEARCH_PLACEHOLDER')" class="search-input--js">
          <button type="button" class="clear" @click="SearchInput=''">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.59094 7L13.0441 2.54687C13.2554 2.3359 13.3743 2.04961 13.3745 1.75099C13.3748 1.45237 13.2564 1.16587 13.0455 0.95453C12.8345 0.743185 12.5482 0.624305 12.2496 0.624041C11.951 0.623778 11.6645 0.742152 11.4531 0.953123L7 5.40625L2.54687 0.953123C2.33553 0.741779 2.04888 0.623047 1.75 0.623047C1.45111 0.623047 1.16447 0.741779 0.953123 0.953123C0.741779 1.16447 0.623047 1.45111 0.623047 1.75C0.623047 2.04888 0.741779 2.33553 0.953123 2.54687L5.40625 7L0.953123 11.4531C0.741779 11.6645 0.623047 11.9511 0.623047 12.25C0.623047 12.5489 0.741779 12.8355 0.953123 13.0469C1.16447 13.2582 1.45111 13.3769 1.75 13.3769C2.04888 13.3769 2.33553 13.2582 2.54687 13.0469L7 8.59375L11.4531 13.0469C11.6645 13.2582 11.9511 13.3769 12.25 13.3769C12.5489 13.3769 12.8355 13.2582 13.0469 13.0469C13.2582 12.8355 13.3769 12.5489 13.3769 12.25C13.3769 11.9511 13.2582 11.6645 13.0469 11.4531L8.59094 7Z" />
            </svg>
          </button>
        </form>
      </search>
      <button v-if="filtersOn" class="filters_btn open-modal--js" data-modal-to-open="filters">
      </button>
    </div>
  `,
});
