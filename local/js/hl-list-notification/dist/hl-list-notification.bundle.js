this.BX = this.BX || {};
(function (exports,ui_vue3,local_Pagenavigation) {
   'use strict';

   var Item = {
     props: ["item"],
     computed: {
       colorView: function colorView() {
         return this.item.UF_VIEW == 'false' ? false : true;
       }
     },
     methods: {
       // Отправка события и изменение флага просмотра
       updateView: function updateView() {
         var _this = this;
         if (!this.colorView) {
           this.$Bitrix.eventEmitter.emitAsync('updateview', {
             id: this.item.ID
           }).then(function (result) {
             if (result[0]) {
               _this.item.UF_VIEW = 'true';
             }
           });
         }
       }
     },
     /* html */
     template: "\n   <li ref=\"elem\" :class=\"{checked: !colorView}\" @mouseover=\"updateView\">\n      <div class=\"date\">{{item.UF_DATE}}</div>\n      <div>{{item.UF_TEXT}}</div>\n   </li>\n   "
   };

   function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
   function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }

   /**
    * мутирующий от дефолтного компонент список уведомлений
    * изменяется шаблон и внутренний компонент
    * пробрасываются параметры
    */
   ui_vue3.BitrixVue.mutateComponent('HlList', {
     components: {
       Item: Item,
       Pagenavigation: local_Pagenavigation.Pagenavigation
     },
     /*html*/
     template: "\n\t<template v-if=\"items.length\">\n\t\t<template v-if=\"showBtn\">\n\t\t\t<button @click.stop=\"updateAll\" class=\"notifications-news__check-button\">{{$Bitrix.Loc.getMessage('BTN_VIEW_ALL')}}</button>\n\t\t</template>\n\t\t<ul class=\"notifications-news__list\">\n\t\t\t<Item v-for=\"item in items\" :item=\"item\" :key=\"item.id\"></Item>\n\t\t</ul>\n\t\t<!-- \u043F\u0430\u0433\u0438\u043D\u0430\u0446\u0438\u044F \u043F\u043E\u044F\u0432\u043B\u044F\u0435\u0442\u0441\u044F \u043F\u0440\u0438 >= 10 \u044D\u043B\u0435\u043C\u0435\u043D\u0442\u043E\u0432 -->\n\t\t<Pagenavigation @pagerfunc=\"ChangePage\" :Total=\"Total\" :PageCNTDef=\"arParams.COUNT_PAGE\" :PageCNT=\"this.CountPageDef\" :key=\"RenderPagination\"></Pagenavigation>\n\t</template>\n   <div  v-else class=\"no-messages\">{{$Bitrix.Loc.getMessage('ITEMS_NOT_FOUND')}}</div>\n\t"
   });
   var _application = /*#__PURE__*/new WeakMap();
   var HlListNotification = /*#__PURE__*/function () {
     function HlListNotification(rootNode) {
       babelHelpers.classCallCheck(this, HlListNotification);
       _classPrivateFieldInitSpec(this, _application, {
         writable: true,
         value: void 0
       });
       this.rootNode = document.querySelector(rootNode);
     }
     babelHelpers.createClass(HlListNotification, [{
       key: "init",
       value: function init(arParams) {
         babelHelpers.classPrivateFieldSet(this, _application, ui_vue3.BitrixVue.createApp({
           name: "HlListNotification",
           data: function data() {
             return {
               arParams: arParams
             };
           },
           components: {
             HlListMutate: ui_vue3.BitrixVue.defineAsyncComponent("local.hl-list", "HlList")
           },
           /* html */
           template: "\n\t\t\t\t\t<HlListMutate :arParams=\"arParams\"></HlListMutate>\n\t\t\t  "
         }));
         babelHelpers.classPrivateFieldGet(this, _application).mount(this.rootNode);
       }
     }]);
     return HlListNotification;
   }();

   exports.HlListNotification = HlListNotification;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3,BX.Oft));
//# sourceMappingURL=hl-list-notification.bundle.js.map
