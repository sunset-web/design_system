export const ElementBlock = {
	props: ["ITEM"],
	data() {
		return {
			ifDate: this.ITEM.ACTIVE_TO ? true : false,
		};
	},
	computed: {
		date() {
			if (this.ITEM.ACTIVE_TO) {
				return this.ITEM.ACTIVE_TO.split(" ")[0];
			} else {
				return "false";
			}
		},
	},
	/*html */
	template: `
    <div class="discount-promo-card discount-promo_slide">
			<div class="promo-red">
				<div class="promo-red_title">{{$Bitrix.Loc.getMessage('SALE_SALE')}}</div>
				<div class="promo-red_text" v-if="ifDate">{{$Bitrix.Loc.getMessage('SALE_DATE', {'#DATE#': this.date})}}</div>
			</div>
			<div class="discount-promo_img">
				<img :src="ITEM.PREVIEW_PICTURE" :alt="ITEM.PREVIEW_PICTURE.ALT" :title="ITEM.PREVIEW_PICTURE.TITLE">
			</div>
			<div class="discount-promo_info">
				<div class="discount-promo_info_wrapper">
					<div class="discount-promo_title">{{this.ITEM.NAME}}</div>
					<div class="discount-promo_text" v-html="ITEM.PREVIEW_TEXT"></div>
				</div>
				<a class="discount-promo_btn as-button white" :href="ITEM.DETAIL_PAGE_URL">{{$Bitrix.Loc.getMessage('SALE_MORE')}}</a>
			</div>
		</div>
  `,
};
