/* eslint-disable */
this.BX = this.BX || {};
(function (exports,ui_vue3,main_core_events) {
	'use strict';

	var CheckboxCard = {
	  name: "CheckboxCard",
	  props: ["value", "name"],
	  data: function data() {
	    return {
	      checked: []
	    };
	  },
	  methods: {
	    filtered: function filtered() {
	      this.$Bitrix.eventEmitter.emit("filter", {
	        name: this.name,
	        values: this.checked
	      });
	    }
	  },
	  watch: {
	    checked: function checked() {
	      this.filtered();
	    }
	  },
	  /*html*/
	  template: "\n        <div class=\"select select--js\">\n          <div class=\"select-title select-title--js\">\n            <input type=\"text\" :placeholder=\"value.NAME\" :name=\"name\" :id=\"name\" autocomplete=\"off\">\n            <label :for=\"name\">{{value.NAME}}</label>\n          </div>\n          <div class=\"options options--js\">\n            <div class=\"options_wrapper\">\n              <div class=\"checkbox_wrapper\" v-for=\"variable of value.LIST\">\n                <label class=\"custom-checkbox option-label\">\n                  <input type=\"checkbox\" :value=\"variable.ID\" v-model=\"checked\">\n                  <span class=\"checkbox_content\">{{variable.NAME}}</span>\n                </label>\n              </div>\n            </div>\n          </div>\n        </div>\n  "
	};

	var CheckboxCardModal = {
	  name: "CheckboxCardModal",
	  props: ["value", "name"],
	  data: function data() {
	    return {
	      checked: [],
	      showMoreCount: 2
	    };
	  },
	  mounted: function mounted() {
	    this.$Bitrix.eventEmitter.subscribe("clearFilter", this.ClearFilter);
	  },
	  methods: {
	    filtered: function filtered() {
	      console.log(this.name);
	      this.$Bitrix.eventEmitter.emit("filter", {
	        name: this.name,
	        values: this.checked
	      });
	    },
	    showMore: function showMore(e) {
	      console.log($(e).hasClass("open"));
	      if ($(e).hasClass("open")) {
	        $(e).removeClass("open");
	        var hBlock = $(e).parents(".show-more-filters-wrap--js").find(".show-more-filters-block--js").attr("data-show-height-block");
	        $(e).parents(".show-more-filters-wrap--js").find(".show-more-filters-block--js").css("max-height", hBlock + "px");
	      } else {
	        $(e).addClass("open");
	        var _hBlock = $(e).parents(".show-more-filters-wrap--js").find(".show-more-filters-block--js").attr("data-height-block");
	        $(e.target).parents(".show-more-filters-wrap--js").find(".show-more-filters-block--js").css("max-height", _hBlock + "px");
	      }
	    },
	    ClearFilter: function ClearFilter() {
	      this.checked = [];
	    }
	  },
	  watch: {
	    checked: function checked() {
	      this.filtered();
	    }
	  },
	  /*html*/
	  template: "\n\t\t<div class=\"filter show-more-filters-wrap--js\" :data-show-more-filters=\"showMoreCount\">\n\t\t\t<div class=\"title\">{{value.NAME}}</div>\n\t\t\t<div class=\"filter_wrapper show-more-filters-block--js\">\n\t\t\t\t<div class=\"checkbox_wrapper show-more-filters-elem--js\" v-for=\"variable of value.LIST\">\n\t\t\t\t\t<label class=\"custom-checkbox option-label\">\n\t\t\t\t\t\t<input type=\"checkbox\" :value=\"variable.ID\" v-model=\"checked\">\n\t\t\t\t\t\t<span class=\"checkbox_content\">{{variable.NAME}}</span>\n\t\t\t\t\t</label>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t<button @click=\"showMore\" v-if=\"value.LIST.length > showMoreCount\" class=\"arrow-down show-more-filters-btn show-more-filters-btn--js\">\u041F\u043E\u043A\u0430\u0437\u0430\u0442\u044C \u0435\u0449\u0451</button>\n\t\t</div>\n  "
	};

	var FiltersForm = ui_vue3.BitrixVue.mutableComponent("FiltersForm", {
	  name: "FiltersForm",
	  props: ["FILTERS", "FilterClick"],
	  components: {
	    CheckboxCard: CheckboxCard,
	    CheckboxCardModal: CheckboxCardModal
	  },
	  data: function data() {
	    return {
	      checked: []
	    };
	  },
	  mounted: function mounted() {
	    //Вычисление максимальной высоты и высоты зависимости от кол-ва пунктов в момент открытия модального окна
	    if ($(".filters-modal.modal--js").length) {
	      $(".filters-modal.modal--js").css("display", "block");
	      console.log("op");
	      $(".show-more-filters-wrap--js").each(function () {
	        if ($(this).find(".show-more-filters-elem--js").length > $(this).attr("data-show-more-filters")) {
	          $(this).find(".show-more-filters-block--js").attr("data-height-block", $(this).find(".show-more-filters-block--js").innerHeight());
	          var hShowBlock = 0;
	          for (var i = 0; i < $(this).attr("data-show-more-filters"); i++) {
	            var wElem = $(this).find(".show-more-filters-elem--js").eq(i).innerHeight();
	            hShowBlock += wElem;
	          }
	          $(this).find(".show-more-filters-block--js").attr("data-show-height-block", hShowBlock);
	          $(this).find(".show-more-filters-block--js").css("max-height", hShowBlock);
	          $(this).find(".show-more-filters-btn--js").css("display", "block");
	        }
	      });
	      $(".filters-modal.modal--js").css("display", "none");
	    }
	  },
	  methods: {
	    clear: function clear() {
	      this.$Bitrix.eventEmitter.emit("clearFilter", {});
	    }
	  },
	  /*html*/
	  template: "\n\n\t<template v-if=\"FILTERS.length != 0\">\n\n\n\t\t<div class=\"filters_wrapper\">\n\t\t\t<CheckboxCard v-for=\"(value, name) in FILTERS\" :key=\"name\" :value=\"value\" :name=\"name\"></CheckboxCard>\n    </div>\n\n\t\t<div class=\"modal filters-modal modal--js\" data-modal=\"filters\">\n\t\t\t<div class=\"modal-wrap-scroll\">\n\t\t\t\t<button class=\"close-btn close-modal--js\">\n\t\t\t\t\t<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t\t<path d=\"M13.4868 11.8939L13.3807 12L13.4869 12.1061L17.9428 16.5592C17.9428 16.5592 17.9428 16.5592 17.9428 16.5592C18.126 16.7424 18.2289 16.9909 18.2289 17.25C18.2289 17.5091 18.126 17.7576 17.9428 17.9408C17.7595 18.124 17.5111 18.2269 17.2519 18.2269C16.9928 18.2269 16.7444 18.124 16.5611 17.9408L12.108 13.4877L12.002 13.3816L11.8959 13.4877L7.44276 17.9408C7.25955 18.124 7.01106 18.2269 6.75195 18.2269C6.49285 18.2269 6.24436 18.124 6.06114 17.9408C5.87793 17.7576 5.775 17.5091 5.775 17.25C5.775 16.9909 5.87793 16.7424 6.06114 16.5592L10.5143 12.1061L10.6203 12L10.5143 11.8939L6.06114 7.44081C5.87793 7.25759 5.775 7.0091 5.775 6.75C5.775 6.49089 5.87793 6.2424 6.06114 6.05919C6.24436 5.87598 6.49285 5.77305 6.75195 5.77305C7.01105 5.77305 7.25955 5.87598 7.44276 6.05919L11.8959 10.5123L12.002 10.6184L12.108 10.5123L16.561 6.05928C16.7442 5.87642 16.9926 5.77381 17.2514 5.77404C17.5102 5.77427 17.7584 5.87731 17.9413 6.0605L18.0468 5.95519L17.9413 6.0605C18.1241 6.24369 18.2267 6.49202 18.2265 6.75086C18.2263 7.0097 18.1232 7.25785 17.94 7.44071L17.9399 7.44081L13.4868 11.8939Z\" stroke=\"white\" stroke-width=\"0.3\" />\n\t\t\t\t\t</svg>\n\t\t\t\t</button>\n\t\t\t\t<h2 class=\"mb-24-fixed tal not-scroll--js\">\u0424\u0438\u043B\u044C\u0442\u0440\u044B</h2>\n\n\t\t\t\t<div class=\"filters-modal_wrapper mb-24-fixed scroll-part\">\n\t\t\t\t\t\t<CheckboxCardModal v-for=\"(value, name) in FILTERS\" :key=\"name\" :value=\"value\" :name=\"name\"></CheckboxCardModal>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"filters-modal_btns not-scroll--js\">\n\t\t\t\t\t<a href=\"javascript:void(0)\" class=\"as-button m-0-a green btn_apply--js close-modal--js\">\u041F\u0440\u0438\u043C\u0435\u043D\u0438\u0442\u044C</a>\n\t\t\t\t\t<a @click=\"clear\" href=\"javascript:void(0)\" class=\"m-0-a btn_reset btn_reset--js\">\u0421\u0431\u0440\u043E\u0441\u0438\u0442\u044C \u0432\u0441\u0435 \u0444\u0438\u043B\u044C\u0442\u0440\u044B</a>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t</template>\n\n  "
	});

	exports.FiltersForm = FiltersForm;

}((this.BX.Oft = this.BX.Oft || {}),BX.Vue3,BX.Event));
//# sourceMappingURL=FiltersForm.bundle.js.map
