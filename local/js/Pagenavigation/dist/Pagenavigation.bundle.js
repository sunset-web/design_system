this.BX = this.BX || {};
(function (exports,ui_vue3) {
   'use strict';

   var CountSelect = {
     props: ["count", "countDef", "current"],
     data: function data() {
       return {
         eachArr: [1, 2, 3],
         countActive: this.count
       };
     },
     computed: {
       pageCount: function pageCount() {
         return this.countDef == this.count ? this.count : this.countDef;
       }
     },
     methods: {
       // Переключение кол-ва
       clickCounter: function clickCounter(e) {
         this.countActive = e.target.dataset.count;
         var urlParams = new URL(window.location.href);
         urlParams.searchParams.set('count', this.countActive);
         history.pushState(null, null, urlParams);
         this.$Bitrix.eventEmitter.emit('countpage', {
           pageN: this.current,
           count: this.countActive
         });
       }
     },
     /* html */
     template: "\n   <div class=\"show flex ai-center\">\n      <p>{{$Bitrix.Loc.getMessage('COUNT_SELECT-TITLE')}}</p>\n      <div class=\"__select __select-small __select-count-js\" data-state=\"\">\n         <div class=\"__select__title __select__title-small __select-count-title-js\" data-default=\"Option 0\">{{pageCount}}</div>\n         <div class=\"__select__content __select__content-small\">\n            <template v-for=\"(item, index) in eachArr\" :item=\"item\" :key=\"index\">\n               <input :id=\"'singleSelect-n'+item\" class=\"__select__input\" type=\"radio\" name=\"singleSelect\" />\n               <label :for=\"'singleSelect-n'+item\"\n                  class=\"__select__label __select__label-small __select-count-label-js\" @click=\"clickCounter\" :data-count=\"count*item\">{{count*item}}</label>\n            </template>\n\n         </div>\n      </div>\n   </div>\n   "
   };

   var Pagenavigation = ui_vue3.BitrixVue.mutableComponent("Pagenavigation", {
     name: "Pagenavigation",
     emits: ["pagerfunc"],
     props: ["Total", "PageCNT", "PageCNTDef"],
     components: {
       CountSelect: CountSelect
     },
     data: function data() {
       return {
         pageNumber: 1,
         pagenQuery: 'PAGEN_1'
       };
     },
     methods: {
       // Переключение на предыдущую страницу
       pagerPrev: function pagerPrev() {
         if (this.pageNumber > 1) {
           this.pageNumber--;
           var urlParams = new URL(window.location.href);
           if (this.pageNumber == 1) {
             urlParams.searchParams["delete"](this.pagenQuery);
           } else {
             urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
           }
           history.pushState(null, null, urlParams);
           this.$emit("pagerfunc", this.pageNumber);
         }
       },
       // Переключение на следующую страницу
       pagerNext: function pagerNext() {
         if (this.pageNumber < this.pagerCount) {
           this.pageNumber++;
           var urlParams = new URL(window.location.href);
           urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
           history.pushState(null, null, urlParams);
           this.$emit("pagerfunc", this.pageNumber);
         }
       },
       // Переключение на первую страницу
       pagerFirst: function pagerFirst() {
         if (this.pageNumber != 1) {
           this.pageNumber = 1;
           var urlParams = new URL(window.location.href);
           urlParams.searchParams["delete"](this.pagenQuery);
           history.pushState(null, null, urlParams);
           this.$emit("pagerfunc", this.pageNumber);
         }
       },
       // Переключение на последнюю страницу
       pagerLast: function pagerLast() {
         if (this.pageNumber != this.pagerCount) {
           this.pageNumber = this.pagerCount;
           var urlParams = new URL(window.location.href);
           urlParams.searchParams.set(this.pagenQuery, this.pageNumber);
           history.pushState(null, null, urlParams);
           this.$emit("pagerfunc", this.pageNumber);
         }
       }
     },
     computed: {
       // Первая страница
       isFirst: function isFirst() {
         return 1 == this.pageNumber;
       },
       // Последняя страница
       isLast: function isLast() {
         return this.pagerCount == this.pageNumber;
       },
       // Предыдущая страница от текущей
       isPrevFirst: function isPrevFirst() {
         return 1 == this.pageNumber - 1;
       },
       // Следующая страница от текущей
       isNextLast: function isNextLast() {
         return this.pagerCount == this.pageNumber + 1;
       },
       // Кол-ва элементов на стр (из скольки)
       offsetCount: function offsetCount() {
         return this.isLast ? Number(this.Total) : Number(this.pageNumber) * Number(this.PageCNT);
       },
       // Кол элементов на стр (сколько)
       offset: function offset() {
         return this.isFirst ? 1 : Number(this.pageNumber) * Number(this.PageCNT) - Number(this.PageCNT) + 1;
       },
       // рассчет стр
       pagerCount: function pagerCount() {
         return Math.floor(this.Total / this.PageCNT) + (this.Total % this.PageCNT ? 1 : 0);
       }
     },
     mounted: function mounted() {
       var urlParams = new URL(window.location.href);
       this.pageNumber = urlParams.searchParams.get('PAGEN_1') ? Number(urlParams.searchParams.get('PAGEN_1')) : this.pageNumber;
     },
     //
     /*html*/
     template: "\n\t<div class=\"bottom-panel flex jc-sb ai-center small\">\n\t\t\t\t<p>{{$Bitrix.Loc.getMessage('PAGE_RESULT-TITLE',{'#OFFSET#': this.offset,'#OFFSET_COUNT#': this.offsetCount,'#TOTAL#': this.Total})}}</p>\n\t\n\t\t\t\t<div class=\"pagination-buttons-group flex ai-center\"  v-if=\"pagerCount>1 && PageCNT < Total\">\n\t\t\t\t\t<a class=\"page-button\"  @click.stop=\"pagerPrev\"\n\t\t\t\t\t:disabled=\"1 == pageNumber\" href=\"javascript: void(0)\">\n\t\t\t\t\t\t<svg width=\"6\" height=\"10\" viewBox=\"0 0 6 10\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t\t\t<path d=\"M5.25 0.5L0.75 5L5.25 9.5\" stroke=\"#1B254A\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n\t\t\t\t\t\t</svg>\n\t\t\t\t\t</a>\n\t\t\t\t\t<a class=\"page\" :class=\"{ current: isFirst }\"  @click.stop=\"pagerFirst\" href=\"javascript: void(0)\" :disabled=\"isFirst\">1</a>\n\t\t\t\t\t<template v-if=\"!isPrevFirst && !isFirst\">\n\t\t\t\t\t\t<a class=\"page\" href=\"javascript:void(0)\" @click.stop=\"pagerPrev\">{{this.pageNumber-1}}</a>\n\t\t\t\t\t</template>\n\t\t\t\t\t<template v-if=\"1 != this.pageNumber && this.pageNumber != this.pagerCount\">\n\t\t\t\t\t\t<a class=\"page current\" href=\"javascript:void(0)\">{{this.pageNumber}}</a>\n\t\t\t\t\t</template>\n\t\t\t\t\t<template v-if=\"!isNextLast && !isLast\">\n\t\t\t\t\t\t<a class=\"page\" href=\"javascript:void(0)\" @click.stop=\"pagerNext\">{{this.pageNumber+1}}</a>\n\t\t\t\t\t</template>\n\t\t\t\t\t<a class=\"page\" :class=\"{ current: isLast }\" href=\"javascript:void(0)\" @click.stop=\"pagerLast\">{{this.pagerCount}}</a>\n\t\t\t\t\t<a class=\"page-button\" @click.stop=\"pagerNext\" href=\"javascript: void(0)\" :disabled=\"pagerCount == pageNumber\">\n\t\t\t\t\t\t<svg width=\"6\" height=\"10\" viewBox=\"0 0 6 10\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t\t\t<path d=\"M0.75 9.5L5.25 5L0.75 0.5\" stroke=\"#1B254A\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n\t\t\t\t\t\t</svg>\n\t\t\t\t\t</a>\n\t\t\t\t</div>\n\t\n\t\t\t\t<CountSelect :count=\"this.PageCNTDef\" :countDef=\"this.PageCNT\" :current=\"this.pageNumber\"></CountSelect>\n\t\t\t</div>\n  "
   });

   exports.Pagenavigation = Pagenavigation;

}((this.BX.Oft = this.BX.Oft || {}),BX.Vue3));
//# sourceMappingURL=Pagenavigation.bundle.js.map
