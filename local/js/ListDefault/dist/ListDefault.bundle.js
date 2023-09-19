/* eslint-disable */
this.BX = this.BX || {};
(function (exports,ui_vue3,main_core_events) {
	'use strict';

	var ElementBlock = {
	  props: ["ITEM"],
	  data: function data() {
	    return {
	      ifDate: this.ITEM.ACTIVE_TO ? true : false
	    };
	  },
	  computed: {
	    date: function date() {
	      if (this.ITEM.ACTIVE_TO) {
	        return this.ITEM.ACTIVE_TO.split(" ")[0];
	      } else {
	        return "false";
	      }
	    }
	  },
	  /*html */
	  template: "\n    <div class=\"discount-promo-card discount-promo_slide\">\n\t\t\t<div class=\"promo-red\">\n\t\t\t\t<div class=\"promo-red_title\">{{$Bitrix.Loc.getMessage('SALE_SALE')}}</div>\n\t\t\t\t<div class=\"promo-red_text\" v-if=\"ifDate\">{{$Bitrix.Loc.getMessage('SALE_DATE', {'#DATE#': this.date})}}</div>\n\t\t\t</div>\n\t\t\t<div class=\"discount-promo_img\">\n\t\t\t\t<img :src=\"ITEM.PREVIEW_PICTURE\" :alt=\"ITEM.PREVIEW_PICTURE.ALT\" :title=\"ITEM.PREVIEW_PICTURE.TITLE\">\n\t\t\t</div>\n\t\t\t<div class=\"discount-promo_info\">\n\t\t\t\t<div class=\"discount-promo_info_wrapper\">\n\t\t\t\t\t<div class=\"discount-promo_title\">{{this.ITEM.NAME}}</div>\n\t\t\t\t\t<div class=\"discount-promo_text\" v-html=\"ITEM.PREVIEW_TEXT\"></div>\n\t\t\t\t</div>\n\t\t\t\t<a class=\"discount-promo_btn as-button white\" :href=\"ITEM.DETAIL_PAGE_URL\">{{$Bitrix.Loc.getMessage('SALE_MORE')}}</a>\n\t\t\t</div>\n\t\t</div>\n  "
	};

	function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
	function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { babelHelpers.defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
	//
	var ListDefault = ui_vue3.BitrixVue.mutableComponent("ListDefault", {
	  name: "ListDefault",
	  components: {
	    ElementBlock: ElementBlock,
	    Pagenavigation: ui_vue3.BitrixVue.defineAsyncComponent("oft.Pagenavigation", "Pagenavigation"),
	    SearchForm: ui_vue3.BitrixVue.defineAsyncComponent("oft.SearchForm", "SearchForm"),
	    FiltersForm: ui_vue3.BitrixVue.defineAsyncComponent("oft.FiltersForm", "FiltersForm")
	  },
	  data: function data() {
	    return {
	      ArResult: BX.SalesListResult,
	      ArParams: BX.SalesListParams,
	      AjaxTemplatePath: BX.ComponentAjaxTemplatePath,
	      ITEMS: [],
	      FILTERS: [],
	      CNT: 0,
	      RenderPagination: 0,
	      SearchInput: "",
	      NeedProps: false,
	      StartFilter: {}
	    };
	  },
	  mounted: function mounted() {
	    var _this = this;
	    // получение списка
	    BX.ajax({
	      url: this.AjaxTemplatePath,
	      data: _objectSpread({
	        QUERY: "getlist",
	        IBLOCK_ID: this.ArParams.IBLOCK_ID,
	        PAGE_NUMBER: 1,
	        PAGE_ELEMENT_COUNT: this.ArParams.NEWS_COUNT,
	        NEED_PROPS: this.NeedProps
	      }, this.StartFilter),
	      method: "POST",
	      dataType: "json",
	      timeout: 30,
	      async: true,
	      processData: true,
	      // нужно ли сразу обрабатывать данные?
	      scriptsRunFirst: true,
	      // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
	      emulateOnload: true,
	      // нужно ли эмулировать событие window.onload для загруженных скриптов
	      start: true,
	      // отправить ли запрос сразу или он будет запущен вручную
	      cache: false,
	      // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
	      onsuccess: function onsuccess(response) {
	        _this.ITEMS = response.ITEMS;
	        _this.CNT = response.CNT;
	      },
	      onfailure: function onfailure(response) {
	        console.error(response);
	      }
	    });
	    // получение фильтров
	    BX.ajax({
	      url: this.AjaxTemplatePath,
	      data: {
	        QUERY: "filters",
	        IBLOCK_ID: this.ArParams.IBLOCK_ID,
	        CODES: this.ArParams.FILTER_PROPERTY_CODE
	      },
	      method: "POST",
	      dataType: "json",
	      timeout: 30,
	      async: true,
	      processData: true,
	      // нужно ли сразу обрабатывать данные?
	      scriptsRunFirst: true,
	      // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
	      emulateOnload: true,
	      // нужно ли эмулировать событие window.onload для загруженных скриптов
	      start: true,
	      // отправить ли запрос сразу или он будет запущен вручную
	      cache: false,
	      // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
	      onsuccess: function onsuccess(response) {
	        _this.FILTERS = response;
	      },
	      onfailure: function onfailure(response) {
	        console.error(response);
	      }
	    });
	    this.$Bitrix.eventEmitter.subscribe("filter", this.Filtered);
	    this.$Bitrix.eventEmitter.subscribe("clearFilter", this.ClearFilter);
	  },
	  beforeUnmount: function beforeUnmount() {
	    this.$Bitrix.eventEmitter.unsubscribe("filter", this.Filtered);
	  },
	  methods: {
	    // общий ajax
	    GetList: function GetList(params, func) {
	      var arrFilter = [];
	      for (var filter in this.FILTERS) {
	        if (this.FILTERS[filter]["QURRENT_VAL"]) arrFilter["=PROPERTY_" + this.FILTERS[filter].ID] = this.FILTERS[filter]["QURRENT_VAL"];
	      }
	      if (this.StartFilter) {
	        params = _objectSpread(_objectSpread({}, params), this.StartFilter);
	      }
	      params.FILTERS = arrFilter;
	      params.SEARCH = this.SearchInput;
	      params.IBLOCK_ID = this.ArParams.IBLOCK_ID;
	      params.PAGE_ELEMENT_COUNT = this.ArParams.NEWS_COUNT;
	      params.QUERY = "getlist";
	      params.NEED_PROPS = this.NeedProps;
	      BX.ajax({
	        url: this.AjaxTemplatePath,
	        data: params,
	        method: "POST",
	        dataType: "json",
	        timeout: 30,
	        async: true,
	        processData: true,
	        // нужно ли сразу обрабатывать данные?
	        scriptsRunFirst: true,
	        // нужно ли выполнять все найденные скрипты перед тем, как отдавать сожеримое обработчику или только те, в тэге которых присутствует атрибут bxrunfirst
	        emulateOnload: true,
	        // нужно ли эмулировать событие window.onload для загруженных скриптов
	        start: true,
	        // отправить ли запрос сразу или он будет запущен вручную
	        cache: false,
	        // в случае значения false к параметру URL будет добавляться случайный кусок, чтобы избежать браузерного кэширования
	        onsuccess: function onsuccess(response) {
	          func(response);
	        },
	        onfailure: function onfailure(response) {
	          console.error(response);
	        }
	      });
	    },
	    // переключение страницы
	    ChangePage: function ChangePage(pageN) {
	      var _this2 = this;
	      this.GetList({
	        PAGE_NUMBER: pageN
	      }, function (res) {
	        _this2.ITEMS = res.ITEMS;
	      });
	    },
	    // показать еще
	    AddNext: function AddNext(pageN) {
	      var _this3 = this;
	      this.GetList({
	        PAGE_NUMBER: pageN
	      }, function (res) {
	        var _this3$ITEMS;
	        (_this3$ITEMS = _this3.ITEMS).push.apply(_this3$ITEMS, babelHelpers.toConsumableArray(res.ITEMS));
	      });
	    },
	    // фильтрация
	    Filtered: function Filtered(event) {
	      var _this4 = this;
	      var data = event.getData();
	      this.FILTERS[data.name]["QURRENT_VAL"] = data.values;
	      var Filtered = function Filtered() {
	        _this4.GetList({
	          PAGE_NUMBER: 1
	        }, function (res) {
	          _this4.ITEMS = res.ITEMS;
	          _this4.CNT = res.CNT;
	          _this4.RenderPagination++;
	        });
	      };
	      debounce(Filtered, 400)();
	    },
	    SearchInputEmit: function SearchInputEmit(e) {
	      this.SearchInput = e;
	    },
	    ClearFilter: function ClearFilter() {
	      var _this5 = this;
	      for (var key in this.FILTERS) {
	        if (Object.hasOwnProperty.call(this.FILTERS, key)) {
	          var element = this.FILTERS[key];
	          this.FILTERS[key]["QURRENT_VAL"] = "";
	        }
	      }
	      this.GetList({
	        PAGE_NUMBER: 1
	      }, function (res) {
	        _this5.ITEMS = res.ITEMS;
	        _this5.CNT = res.CNT;
	        _this5.RenderPagination++;
	      });
	    }
	  },
	  watch: {
	    SearchInput: function SearchInput() {
	      var _this6 = this;
	      this.GetList({
	        PAGE_NUMBER: 1
	      }, function (res) {
	        _this6.ITEMS = res.ITEMS;
	        _this6.CNT = res.CNT;
	        _this6.RenderPagination++;
	      });
	    }
	  },
	  /* html */
	  template: "\n\t\t\t<SearchForm @searching=\"SearchInputEmit\" :filtersOn=\"FILTERS.length != 0\"></SearchForm>\n\t\t\t<FiltersForm :FILTERS=\"FILTERS\"></FiltersForm>\n\t\t\t<ElementBlock v-for=\"item in ITEMS\" :ITEM=\"item\" :key=\"item.ID\" ></ElementBlock>\n\t\t\t<div v-if=\"ITEMS==null\">{{$Bitrix.Loc.getMessage('SALE_NOT_FOUND')}}</div>\n\t\t\t<Pagenavigation @pagerFunc=\"ChangePage\" @AddNext=\"AddNext\" :CNT=\"CNT\" :PageCNT=\"ArParams.NEWS_COUNT\" :key=\"RenderPagination\"></Pagenavigation>\n\t\t\t"
	});

	exports.ListDefault = ListDefault;

}((this.BX.Oft = this.BX.Oft || {}),BX.Vue3,BX.Event));
//# sourceMappingURL=ListDefault.bundle.js.map
