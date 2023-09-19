/* eslint-disable */
this.BX = this.BX || {};
(function (exports,ui_vue3) {
	'use strict';

	var Pagenavigation = ui_vue3.BitrixVue.mutableComponent("Pagenavigation", {
	  name: "Pagenavigation",
	  emits: ["pagerFunc", "addNext"],
	  props: ["CNT", "PageCNT"],
	  mounted: function mounted() {},
	  data: function data() {
	    return {
	      pageNumber: 1
	    };
	  },
	  methods: {
	    pagerPrev: function pagerPrev() {
	      if (this.pageNumber > 1) {
	        this.pageNumber--;
	        this.$emit("pagerFunc", this.pageNumber);
	      }
	    },
	    pagerNext: function pagerNext() {
	      if (this.pageNumber < this.pagerCount) {
	        this.pageNumber++;
	        this.$emit("pagerFunc", this.pageNumber);
	      }
	    },
	    pagerFirst: function pagerFirst() {
	      if (this.pageNumber != 1) {
	        this.pageNumber = 1;
	        this.$emit("pagerFunc", this.pageNumber);
	      }
	    },
	    pagerLast: function pagerLast() {
	      if (this.pageNumber != this.pagerCount) {
	        this.pageNumber = this.pagerCount;
	        this.$emit("pagerFunc", this.pageNumber);
	      }
	    },
	    addNext: function addNext() {
	      if (this.pageNumber != this.pagerCount) {
	        this.pageNumber++;
	        this.$emit("addNext", this.pageNumber);
	      }
	    }
	  },
	  computed: {
	    isFirst: function isFirst() {
	      return 1 == this.pageNumber;
	    },
	    isLast: function isLast() {
	      return this.pagerCount == this.pageNumber;
	    },
	    pagerCount: function pagerCount() {
	      console.log(this.PageCNT);
	      this.pageNumber = 1;
	      return Math.floor(this.CNT / this.PageCNT) + (this.CNT % this.PageCNT ? 1 : 0);
	    }
	  },
	  //
	  /*html*/
	  template: "\n\t<div class=\"show-more-cards_control show-more_cards-control--js\" v-if=\"pagerCount>1\">\n\t\t<a\n\t\t@click=\"addNext\"\n\t\thref=\"javascript: void(0)\"\n\t\tclass=\"as-button m-0-a arrow-down discount-promo_more\"\n\t\t:class=\"{vh: this.pagerCount == this.pageNumber}\"\n\t\t>\n\t\t\t{{$Bitrix.Loc.getMessage('SEE_MORE')}}\n\t\t</a>\n\t\t<div class=\"show-more-cards_pages\">\n\t\t\t<a\n\t\t\t@click=\"pagerPrev\"\n\t\t\tclass=\"show-more-cards_pages-prev\"\n\t\t\t:disabled=\"1 == pageNumber\"\n\t\t\thref=\"javascript: void(0)\">\n\t\t\t\t<svg width=\"9\" height=\"14\" viewBox=\"0 0 9 14\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t<path d=\"M7.6333 0.600098L1.2333 7.0001L7.6333 13.4001\" stroke=\"#808080\" stroke-linecap=\"round\" />\n\t\t\t\t</svg>\n\t\t\t</a>\n\t\t\t<ul>\n\t\t\t\t<li :class=\"{ active: isFirst }\">\n\t\t\t\t\t<a\n\t\t\t\t\t@click=\"pagerFirst\"\n\t\t\t\t\t:disabled=\"isFirst\"\n\t\t\t\t\thref=\"javascript: void(0)\">\n\t\t\t\t\t\t1\n\t\t\t\t\t</a>\n\t\t\t\t</li>\n\t\t\t\t<li><span>...</span></li>\n\t\t\t\t<template v-if=\"1 != this.pageNumber && this.pageNumber != this.pagerCount\">\n\t\t\t\t\t<li class=\"active\"><span>{{this.pageNumber}}</span></li>\n\t\t\t\t\t<li><span>...</span></li>\n\t\t\t\t</template>\n\n\t\t\t\t<li :class=\"{ active: isLast }\">\n\t\t\t\t\t<a\n\t\t\t\t\t@click=\"pagerLast\"\n\t\t\t\t\t:disabled=\"isLast\"\n\t\t\t\t\thref=\"javascript: void(0)\">\n\t\t\t\t\t\t{{this.pagerCount}}\n\t\t\t\t\t</a>\n\t\t\t\t</li>\n\t\t\t</ul>\n\t\t\t<a\n\t\t\t@click=\"pagerNext\"\n\t\t\t:disabled=\"pagerCount == pageNumber\"\n\t\t\tclass=\"show-more-cards_pages-next\"\n\t\t\thref=\"javascript: void(0)\">\n\t\t\t\t<svg width=\"9\" height=\"14\" viewBox=\"0 0 9 14\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t<path d=\"M1.3667 13.3999L7.7667 6.9999L1.3667 0.599902\" stroke=\"#808080\" stroke-linecap=\"round\" />\n\t\t\t\t</svg>\n\t\t\t</a>\n\t\t</div>\n\t</div>\n  "
	});

	exports.Pagenavigation = Pagenavigation;

}((this.BX.Oft = this.BX.Oft || {}),BX.Vue3));
//# sourceMappingURL=Pagenavigation.bundle.js.map
