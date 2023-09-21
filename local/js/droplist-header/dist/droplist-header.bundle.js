this.BX = this.BX || {};
(function (exports,ui_vue3,ui_vue3_pinia) {
   'use strict';

   var Item = {
     props: ["item"],
     /* html */
     template: "\n   <li ref=\"elem\" :data-id=\"item.id\">\n      <div class=\"date\">{{item.UF_DATE}}</div>\n      <div>{{item.UF_TEXT}}</div>\n   </li>\n   "
   };

   var dropListHeaderStore = ui_vue3_pinia.defineStore("dropListHeaderStore", {
     state: function state() {
       return {
         units: []
       };
     },
     actions: {
       // Проверка наличия элемента
       getUnit: function getUnit(id) {
         return this.units.find(function (unit) {
           return unit.id === id;
         });
       },
       constructUnit: function constructUnit(id, list) {
         // Получение значений
         var arrInStore = this.getUnit(id);
         if (arrInStore) {
           arrInStore.list = list;
         } else {
           this.units.push({
             id: id,
             list: list
           });
         }
       }
     }
   });

   function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
   function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { babelHelpers.defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
   /**
    * дефолтный компонент реализующий логику получения элементов
    * запись в хранилище
    * обработка и передача на вывод
    */
   var DroplistHeader = ui_vue3.BitrixVue.mutableComponent('DroplistHeader', {
     name: 'DroplistHeader',
     components: {
       Item: Item
     },
     props: ['arParams'],
     data: function data() {
       return {
         link: this.arParams.LINK_PAGE,
         items: [],
         itemsLen: []
       };
     },
     computed: {
       countItems: function countItems() {
         // Кол-во элементов для отображения
         return this.itemsLen.length > 10 ? '10+' : Number(this.itemsLen.length);
       }
     },
     methods: _objectSpread({
       // Берем лишь нужное кол-во на вывод
       arrSlice: function arrSlice(arr) {
         arr.splice(this.arParams.COUNT, arr.length);
       },
       // Добавление элемента
       addItem: function addItem(response) {
         // Собираем элемент
         var objAdd = _objectSpread({
           ID: String(response.id)
         }, response.fields);

         // Проверяем HL-блок
         if (this.arParams.HL_ID == response.hl_id) {
           this.items.unshift(objAdd);
           this.itemsLen.unshift(objAdd);
           this.arrSlice(this.items);
           this.constructUnit(response.hl_id, this.itemsLen);
         }
       },
       updateItem: function updateItem(response) {
         var _this = this;
         // Проверяем флаг просмотра
         if (response.view == 'true' && this.arParams.HL_ID == response.hl_id) {
           Object.assign(this.items, this.itemsLen);
           // Удаляем элементы
           this.items.map(function (item, index) {
             if (item.ID == response.id) {
               _this.items.splice(index, 1);
               _this.itemsLen.splice(index, 1);
             }
           });
           this.arrSlice(this.items);
           this.constructUnit(response.hl_id, this.itemsLen);
         }
       }
     }, ui_vue3_pinia.mapActions(dropListHeaderStore, {
       constructUnit: "constructUnit",
       getUnit: "getUnit"
     })),
     mounted: function mounted() {
       var _this2 = this;
       // запрос на получение списка
       BX.ajax.runComponentAction("itin:hl.news.list", "getList", {
         mode: "class",
         data: {
           hl_id: this.arParams.HL_ID
         }
       }).then(function (response) {
         Object.assign(_this2.itemsLen, response.data.result);
         Object.assign(_this2.items, response.data.result);
         _this2.arrSlice(_this2.items);
         // запись в хранилище
         _this2.constructUnit(_this2.arParams.HL_ID, _this2.itemsLen);
       });
       // Подписка на событие
       BX.PULL.start();
       BX.addCustomEvent("onPullEvent", function (module_id, command, params) {
         if (module_id == "droplistheader") {
           if (command == "check") {
             _this2.addItem(params.response);
           }
           if (command == "update") {
             _this2.updateItem(params.response);
           }
         }
       });
     }
   });

   exports.DroplistHeader = DroplistHeader;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3,BX.Vue3.Pinia));
//# sourceMappingURL=droplist-header.bundle.js.map
