this.BX = this.BX || {};
(function (exports,ui_vue3) {
	'use strict';

	var DateInput = {
	  name: "DateInput",
	  props: ["field", "value"],
	  mounted: function mounted() {
	    var _this = this;
	    $(this.$refs["dateInput" + this.field.CODE]).on("input", function (e) {
	      _this.$emit("input", e);
	    });
	    // КАЛЕНДАРЬ

	    // svg иконок (неактивной и активной)
	    var grayIcon = "data:image/svg+xml,%3Csvg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M14 0C13.45 0 13 0.45 13 1V2H5V1C5 0.45 4.55 0 4 0C3.45 0 3 0.45 3 1V2H2C0.89 2 0.00999999 2.9 0.00999999 4L0 18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2H15V1C15 0.45 14.55 0 14 0ZM16 18H2V8H16V18ZM8 11C8 10.45 8.45 10 9 10C9.55 10 10 10.45 10 11C10 11.55 9.55 12 9 12C8.45 12 8 11.55 8 11ZM4 11C4 10.45 4.45 10 5 10C5.55 10 6 10.45 6 11C6 11.55 5.55 12 5 12C4.45 12 4 11.55 4 11ZM12 11C12 10.45 12.45 10 13 10C13.55 10 14 10.45 14 11C14 11.55 13.55 12 13 12C12.45 12 12 11.55 12 11ZM8 15C8 14.45 8.45 14 9 14C9.55 14 10 14.45 10 15C10 15.55 9.55 16 9 16C8.45 16 8 15.55 8 15ZM4 15C4 14.45 4.45 14 5 14C5.55 14 6 14.45 6 15C6 15.55 5.55 16 5 16C4.45 16 4 15.55 4 15ZM12 15C12 14.45 12.45 14 13 14C13.55 14 14 14.45 14 15C14 15.55 13.55 16 13 16C12.45 16 12 15.55 12 15Z' fill='%238895BB'/%3E%3C/svg%3E%0A";
	    var blackIcon = "data:image/svg+xml,%3Csvg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M14 0C13.45 0 13 0.45 13 1V2H5V1C5 0.45 4.55 0 4 0C3.45 0 3 0.45 3 1V2H2C0.89 2 0.00999999 2.9 0.00999999 4L0 18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2H15V1C15 0.45 14.55 0 14 0ZM16 18H2V8H16V18ZM8 11C8 10.45 8.45 10 9 10C9.55 10 10 10.45 10 11C10 11.55 9.55 12 9 12C8.45 12 8 11.55 8 11ZM4 11C4 10.45 4.45 10 5 10C5.55 10 6 10.45 6 11C6 11.55 5.55 12 5 12C4.45 12 4 11.55 4 11ZM12 11C12 10.45 12.45 10 13 10C13.55 10 14 10.45 14 11C14 11.55 13.55 12 13 12C12.45 12 12 11.55 12 11ZM8 15C8 14.45 8.45 14 9 14C9.55 14 10 14.45 10 15C10 15.55 9.55 16 9 16C8.45 16 8 15.55 8 15ZM4 15C4 14.45 4.45 14 5 14C5.55 14 6 14.45 6 15C6 15.55 5.55 16 5 16C4.45 16 4 15.55 4 15ZM12 15C12 14.45 12.45 14 13 14C13.55 14 14 14.45 14 15C14 15.55 13.55 16 13 16C12.45 16 12 15.55 12 15Z' fill='%231B254A'/%3E%3C/svg%3E%0A";

	    //настройки календаря
	    $.datepicker.regional["ru"] = {
	      showOn: "both",
	      buttonImage: grayIcon,
	      buttonImageOnly: true,
	      buttonText: "Выбрать дату",
	      closeText: "Закрыть",
	      prevText: "Предыдущий месяц",
	      nextText: "Следующий месяц",
	      currentText: "Сегодня",
	      monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	      monthNamesShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
	      dayNames: ["воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота"],
	      dayNamesShort: ["вск", "пнд", "втр", "срд", "чтв", "птн", "сбт"],
	      dayNamesMin: ["ВС", "ПН", "ВТ", "СР", "ЧТ", "ПТ", "СБ"],
	      weekHeader: "Не",
	      dateFormat: "dd.mm.yy",
	      firstDay: 1,
	      isRTL: false,
	      showMonthAfterYear: false,
	      yearSuffix: "",
	      showOtherMonths: true,
	      showAnim: "",
	      beforeShow: function beforeShow(textbox, instance) {
	        $(textbox).trigger("click");
	        instance.dpDiv.css("padding", "24px");

	        // центрирование или
	        // сдвиг к правому краю текстового поля
	        // в зависимости от разрешения экрана
	        var width = window.innerWidth;
	        if (width < 768) {
	          $(".overlay-datepicker--js").addClass("active");
	          instance.dpDiv.css("padding", "16px");
	          instance.dpDiv.css("left", "17px");
	          instance.dpDiv.css("margin-left", "0");
	          $(".overlay-js").addClass("active");
	        } else if (width < 1200) {
	          if (!$(textbox).hasClass("datepicker-small--js")) {
	            instance.dpDiv.css("margin-left", "-44px");
	          }
	          if ($(textbox).hasClass("datepicker-big--js")) {
	            instance.dpDiv.css("margin-left", "192px");
	          }
	        } else {
	          if ($(textbox).hasClass("datepicker-big--js")) {
	            instance.dpDiv.css("margin-left", "288px");
	          }
	        }

	        // перекрытие шапки
	        setTimeout(function () {
	          instance.dpDiv.css("z-index", "7");
	        }, 0);

	        // смена иконки на черную при открытии
	        $(this).next(".ui-datepicker-trigger").attr("src", blackIcon);
	      },
	      onSelect: function onSelect(date, instance) {
	        $(this).trigger("input");
	        $(".overlay-datepicker--js").removeClass("active");
	        if ($(this).hasClass("date-input-start--js")) {
	          var selectedDay = instance.selectedDay;
	          var selectedMonth = instance.selectedMonth;
	          var selectedYear = instance.selectedYear;
	          var startDate = new Date(selectedYear, selectedMonth, selectedDay);
	          $(".date-input-end--js").datepicker("option", "minDate", startDate);
	        }
	      },
	      onClose: function onClose() {
	        // смена иконки на серую при закрытии
	        $(this).next(".ui-datepicker-trigger").attr("src", grayIcon);
	        var width = window.innerWidth;
	        if (width <= 768) {
	          $(".overlay-js").removeClass("active");
	        }
	      }
	    };
	    $.datepicker.setDefaults($.datepicker.regional["ru"]);
	    $(".datepicker--js").datepicker();
	    var startDateInput = $(".date-input-start--js").val();
	    if (startDateInput) {
	      var date = startDateInput.split(".");
	      var _date = babelHelpers.slicedToArray(date, 3),
	        day = _date[0],
	        month = _date[1],
	        year = _date[2];
	      var startDate = new Date(year, month - 1, day);
	      $(".date-input-end--js").datepicker("destroy");
	      $(".date-input-end--js").datepicker({
	        minDate: startDate
	      });
	    }
	    $(document).on("click", ".overlay-datepicker--js", function () {
	      $(this).removeClass("active");
	    });
	    var maskOptions = {
	      mask: Date,
	      pattern: "`d.`m.Y",
	      blocks: {
	        d: {
	          mask: IMask.MaskedRange,
	          from: 1,
	          to: 31,
	          maxLength: 2
	        },
	        m: {
	          mask: IMask.MaskedRange,
	          from: 1,
	          to: 12,
	          maxLength: 2
	        },
	        Y: {
	          mask: IMask.MaskedRange,
	          from: 1900,
	          to: 9999
	        }
	      },
	      autofix: true
	    };
	    $(".date-input--js").each(function () {
	      IMask($(this)[0], maskOptions);
	    });
	  },
	  methods: {
	    updateDate: function updateDate(e) {
	      this.$emit("input", e.target.value);
	    }
	  },
	  /*html*/
	  template: "\n  <div class=\"form-input-field-group form-datepicker-group form-input-field-group--js\">\n    <input autocomplete=\"off\" :value=\"value\" @input=\"updateDate\" :ref=\"'dateInput' + field.CODE\" type=\"text\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" :id=\"field.CODE\" :name=\"field.CODE\" :data-name=\"field.CODE\" class=\"date-input--js date-input-start--js datepicker--js\" maxlength=\"10\">\n    <label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n    <div class=\"help-icon help-icon_small help-icon--js\">\n        <svg width=\"18\" height=\"18\" viewBox=\"0 0 18 18\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">\n          <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M4.15613 1.75062C5.58992 0.792596 7.2756 0.28125 9 0.28125C11.3124 0.28125 13.53 1.19983 15.1651 2.83491C16.8002 4.47 17.7188 6.68764 17.7188 9C17.7188 10.7244 17.2074 12.4101 16.2494 13.8439C15.2914 15.2777 13.9297 16.3952 12.3365 17.0551C10.7434 17.715 8.99033 17.8876 7.29906 17.5512C5.60779 17.2148 4.05426 16.3844 2.83492 15.1651C1.61558 13.9457 0.785197 12.3922 0.448782 10.7009C0.112367 9.00967 0.285028 7.25662 0.944929 5.66348C1.60483 4.07034 2.72233 2.70865 4.15613 1.75062ZM9 1.21875C7.46102 1.21875 5.95659 1.67511 4.67697 2.53013C3.39735 3.38514 2.40001 4.60041 1.81107 6.02225C1.22212 7.44408 1.06803 9.00863 1.36827 10.518C1.66851 12.0275 2.4096 13.4139 3.49783 14.5022C4.58606 15.5904 5.97254 16.3315 7.48196 16.6317C8.99137 16.932 10.5559 16.7779 11.9778 16.1889C13.3996 15.6 14.6149 14.6027 15.4699 13.323C16.3249 12.0434 16.7813 10.539 16.7813 9C16.7813 6.93628 15.9614 4.95709 14.5022 3.49783C13.0429 2.03856 11.0637 1.21875 9 1.21875Z\" />\n          <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M10.0382 4.99126C9.86307 4.90654 9.4873 4.82255 9.00736 4.82836C8.49868 4.83531 7.99618 4.94728 7.59306 5.27762M10.0382 4.99126C10.4905 5.21163 11.1498 5.64489 11.1498 6.51584C11.1498 7.47363 10.5921 7.90351 9.63129 8.56045C9.11013 8.91677 8.7437 9.29654 8.51271 9.72347C8.27968 10.1542 8.20798 10.5896 8.20798 11.0156C8.20798 11.3263 8.46127 11.5781 8.77371 11.5781C9.08616 11.5781 9.33944 11.3263 9.33944 11.0156C9.33944 10.7206 9.38768 10.4809 9.50915 10.2564C9.63266 10.0281 9.8553 9.77257 10.2724 9.48739L10.2941 9.47252C11.2053 8.84965 12.2812 8.11414 12.2812 6.51584C12.2812 4.94597 11.0509 4.23178 10.5353 3.98071L10.5349 3.98049L10.5344 3.98026C10.1431 3.79059 9.57216 3.69643 8.99306 3.7035L8.99267 3.7035L8.99228 3.70351C8.34078 3.71235 7.54847 3.85633 6.8734 4.40955L6.87338 4.40956C6.33015 4.85477 6.0412 5.36035 5.88923 5.75974C5.81354 5.95865 5.77164 6.13149 5.74841 6.25875C5.73677 6.3225 5.72973 6.37526 5.72549 6.41467C5.72337 6.43439 5.72194 6.45082 5.72099 6.46368C5.72051 6.47011 5.72014 6.47565 5.71987 6.48026C5.71974 6.48257 5.71962 6.48465 5.71953 6.48649L5.7194 6.48908L5.71935 6.49024L5.71932 6.49079C5.71931 6.49105 5.7193 6.49132 6.28449 6.51584L5.7193 6.49132C5.70568 6.80167 5.94768 7.06424 6.25983 7.07778C6.56998 7.09123 6.83269 6.85377 6.84939 6.5463C6.84952 6.5447 6.84986 6.54066 6.85054 6.53434C6.85207 6.52009 6.85531 6.49452 6.86168 6.45964C6.87446 6.38963 6.89949 6.28393 6.94749 6.1578C7.04288 5.90712 7.22829 5.57658 7.59305 5.27763\" />\n          <path d=\"M8.71875 14.1562C9.18474 14.1562 9.5625 13.7785 9.5625 13.3125C9.5625 12.8465 9.18474 12.4688 8.71875 12.4688C8.25276 12.4688 7.875 12.8465 7.875 13.3125C7.875 13.7785 8.25276 14.1562 8.71875 14.1562Z\" />\n        </svg>\n    </div>\n    <div class=\"help-info help-info_small small help-info--js\">\n      {{this.$Bitrix.Loc.getMessage('DATE_INFO')}}\n    </div>\n  </div>\n"
	};

	var ErrModal = {
	  props: ["ERR"],
	  /*html*/
	  template: "\n  <div @click=\"$emit('closeModalErr');\" class=\"overlay overlay-js active\"></div>\n  <div @click=\"$emit('closeModalErr');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n  <div class=\"modal-loading-result flex fd-column ai-center ajax-error active\" style=\"height: fit-content;\">\n    <h3 class=\"modal-loading-result__title tac\">\u041E\u0448\u0438\u0431\u043A\u0430</h3>\n    <p class=\"tac small\" style=\"text-align:left\">\n      <template v-for=\"field in ERR\">\n        \u0417\u0430\u043F\u043E\u043B\u043D\u0438\u0442\u0435 \u043F\u043E\u043B\u0435: {{field.NAME}} <br>\n      </template>\n    </p>\n    <button @click=\"$emit('closeModalErr');\" class=\"btn-red p13-24\">\u041E\u043A\u0435\u0439</button>\n  </div>\n"
	};

	var SuccessModalEdit = {
	  /*html*/
	  template: "\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-js active\"></div>\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n  <div class=\"modal-loading-result flex fd-column ai-center ajax-success active\">\n    <div class=\"modal-loading-result__title tac\">\u0423\u0441\u043F\u0435\u0448\u043D\u043E</div>\n    <p class=\"tac small\">\u0412\u0441\u0435 \u0438\u0437\u043C\u0435\u043D\u0435\u043D\u0438\u044F \u0443\u0441\u043F\u0435\u0448\u043D\u043E \u0441\u043E\u0445\u0440\u0430\u043D\u0435\u043D\u044B</p>\n    <button @click=\"$emit('closeModalSuccess');\" class=\"btn-blue p13-24 js-modal-close\">\u041E\u043A\u0435\u0439</button>\n  </div>\n"
	};

	var SuccessModalAdd = {
	  /*html*/
	  template: "\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-js active\"></div>\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n  <div class=\"modal-loading-result flex fd-column ai-center ajax-success active\">\n    <div class=\"modal-loading-result__title tac\">\u0423\u0441\u043F\u0435\u0448\u043D\u043E</div>\n    <p class=\"tac small\">\u0412\u0441\u0435 \u0438\u0437\u043C\u0435\u043D\u0435\u043D\u0438\u044F \u0443\u0441\u043F\u0435\u0448\u043D\u043E \u0441\u043E\u0445\u0440\u0430\u043D\u0435\u043D\u044B</p>\n    <button @click=\"$emit('closeModalSuccess');\" class=\"btn-blue p13-24 js-modal-close\">\u041E\u043A\u0435\u0439</button>\n  </div>\n"
	};

	var SuccessModalDelete = {
	  /*html*/
	  template: "\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-js active\"></div>\n  <div @click=\"$emit('closeModalSuccess');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n  <div class=\"modal-loading-result flex fd-column ai-center ajax-success active\">\n    <div class=\"modal-loading-result__title tac\">\u0423\u0441\u043F\u0435\u0448\u043D\u043E</div>\n    <p class=\"tac small\">\u0423\u0434\u0430\u043B\u0435\u043D\u0438\u0435 \u0437\u0430\u0432\u0435\u0440\u0448\u0435\u043D\u043E</p>\n    <button @click=\"$emit('closeModalSuccess');\" class=\"btn-blue p13-24 js-modal-close\">\u041E\u043A\u0435\u0439</button>\n  </div>\n"
	};

	var ErrorModal = {
	  name: "ErrorModal",
	  /*html*/
	  template: "\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n    <div class=\"modal-loading-result flex fd-column ai-center ajax-error active\" style=\"height: fit-content;\">\n      <h3 class=\"modal-loading-result__title tac\">{{this.$Bitrix.Loc.getMessage('ERR')}}</h3>\n      <p class=\"tac small\" style=\"text-align:left\"></p>\n      <button @click=\"$emit('closeModal');\" class=\"btn-red p13-24\">{{this.$Bitrix.Loc.getMessage('OK_BTN')}}</button>\n    </div>\n  "
	};

	var DeleteModal = {
	  name: "DeleteModal",
	  /*html*/
	  template: "\n<div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n<div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n<div class=\"modal-window flex fd-column ai-center delete-modal-js active\">\n   <div class=\"modal-delete-confirm__title tac\">{{this.$Bitrix.Loc.getMessage('MODAL_DELETE_TITTLE')}}</div>\n   <p class=\"tac small\">{{this.$Bitrix.Loc.getMessage('MODAL_DELETE_TEXT')}}</p>\n   <div class=\"modal-window__button-group flex fd-column-s\">\n      <button @click=\"$emit('deleteClick');\" class=\"btn-red p13-24\">{{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}</button>\n      <button @click=\"$emit('closeModal');\" class=\"btn-white p13-24\">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>\n   </div>\n</div>\n  "
	};

	function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
	function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
	var _app = /*#__PURE__*/new WeakMap();
	var ElementCreate = /*#__PURE__*/function () {
	  function ElementCreate(rootNode) {
	    babelHelpers.classCallCheck(this, ElementCreate);
	    _classPrivateFieldInitSpec(this, _app, {
	      writable: true,
	      value: void 0
	    });
	    this.rootNode = document.querySelector(rootNode);
	  }
	  babelHelpers.createClass(ElementCreate, [{
	    key: "init",
	    value: function init() {
	      babelHelpers.classPrivateFieldSet(this, _app, ui_vue3.BitrixVue.createApp({
	        name: "ElementCreate",
	        components: {
	          DateInput: DateInput,
	          ErrModal: ErrModal,
	          SuccessModalEdit: SuccessModalEdit,
	          SuccessModalAdd: SuccessModalAdd,
	          SuccessModalDelete: SuccessModalDelete,
	          ErrorModal: ErrorModal,
	          DeleteModal: DeleteModal
	        },
	        data: function data() {
	          return {
	            arResult: {},
	            cancelCount: 0,
	            FIELDS: {},
	            OPTIONS: {},
	            ERR: {},
	            SuccessAdd: false,
	            SuccessEdit: false,
	            SuccessDelete: false,
	            componentName: "",
	            scriptCast: "",
	            componentId: "",
	            IsDeleteModal: false,
	            IsErrorModal: false,
	            fade: 0,
	            backURL: "",
	            backID: ""
	          };
	        },
	        mounted: function mounted() {
	          var _this = this;
	          this.fade++;
	          BX.ajax({
	            url: El_Create_Script_Path,
	            data: {
	              el_id: El_Create_Vue_Id,
	              link_add: typeof Link_add != "undefined" ? Link_add : [""]
	            },
	            method: "POST",
	            dataType: "html",
	            // html|json|script – данные какого типа предполагаются в ответе
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
	              var res = JSON.parse(response);
	              _this.arResult = res.arResult;
	              _this.backID = typeof El_back_Id != "undefined" ? El_back_Id : "";
	              _this.componentName = res.componentName;
	              _this.componentId = res.componentId;
	              _this.scriptCast = res.scriptCast;
	              if (El_Create_Vue_Id) {
	                _this.FIELDS = JSON.parse(JSON.stringify(_this.arResult.ELEMENT.FIELDS));
	                _this.OPTIONS = JSON.parse(JSON.stringify(_this.arResult.ELEMENT.OPTIONS));
	              }
	            },
	            onfailure: function onfailure(response) {
	              console.error(response);
	            }
	          });

	          // * фикс стоимости
	          // ФОРМАТИРОВАНИЕ ЧИСЛА (РАЗДЕЛЕНИЕ НА РАЗРЯДЫ)
	          var formatFIeldWithRoubles = function formatFIeldWithRoubles(field) {
	            var val = field.val().replace(/[^0-9.]/g, "");
	            if (val.length === 0) {
	              field.val("");
	              return;
	            }
	            if (val.indexOf(".") != "-1") {
	              val = val.substring(0, val.indexOf(".") + 3);
	            }
	            val = val.replace(/\B(?=(\d{3})+(?!\d))/g, " ");
	            field.val(val + " ₽");
	          };
	          $(".format-number-field--js").each(function () {
	            formatFIeldWithRoubles($(this));
	          });
	          $.fn.setCursorPosition = function (pos) {
	            this.each(function (index, elem) {
	              if (elem.setSelectionRange) {
	                elem.setSelectionRange(pos, pos);
	              } else if (elem.createTextRange) {
	                var range = elem.createTextRange();
	                range.collapse(true);
	                range.moveEnd("character", pos);
	                range.moveStart("character", pos);
	                range.select();
	              }
	            });
	            return this;
	          };
	          $(document).on("input", ".format-number-field--js", function (_ref) {
	            var target = _ref.target;
	            formatFIeldWithRoubles($(target));
	            $(target).setCursorPosition($(target).val().length - 2);
	          });
	          // КОНЕЦ ФОРМАТИРОВАНИЕ ЧИСЛА (РАЗДЕЛЕНИЕ НА РАЗРЯДЫ)
	        },

	        methods: {
	          SuccessAddDone: function SuccessAddDone() {
	            this.SuccessAdd = false;
	            window.location.href = this.backURL ? this.backURL : window.location.href.replace("-create", "");
	          },
	          SuccessEditDone: function SuccessEditDone() {
	            this.SuccessEdit = false;
	          },
	          SuccessDeleteDone: function SuccessDeleteDone() {
	            this.SuccessDelete = false;
	            window.location.href = window.location.href.replace("".concat(El_Create_Vue_Id, "/"), "");
	          },
	          errorDone: function errorDone() {
	            this.ERR = {};
	          },
	          isEmpty: function isEmpty(obj) {
	            for (var key in obj) {
	              return false;
	            }
	            return true;
	          },
	          bxEdit: function bxEdit() {
	            var _this2 = this;
	            this.ERR = {};
	            for (var key in this.arResult.FIELDS) {
	              if (Object.hasOwnProperty.call(this.arResult.FIELDS, key)) {
	                var field = this.arResult.FIELDS[key];
	                if (field.REQUIRED && !this.FIELDS[key]) {
	                  this.ERR[key] = field;
	                }
	              }
	            }
	            for (var _key in this.arResult.OPTIONS) {
	              if (Object.hasOwnProperty.call(this.arResult.OPTIONS, _key)) {
	                var _field = this.arResult.OPTIONS[_key];
	                if (_field.REQUIRED && !this.OPTIONS[_key]) {
	                  this.ERR[_key] = _field;
	                }
	              }
	            }
	            for (var _key2 in this.arResult.DATES) {
	              if (Object.hasOwnProperty.call(this.arResult.DATES, _key2)) {
	                var _field2 = this.arResult.DATES[_key2];
	                if (_field2.REQUIRED && !this.FIELDS[_key2]) {
	                  this.ERR[_key2] = _field2;
	                }
	              }
	            }
	            if (!this.isEmpty(this.ERR)) ; else {
	              BX.ajax.runComponentAction(this.componentName, "edit", {
	                mode: "class",
	                data: {
	                  // переменные для получения параметров
	                  componentId: this.componentId,
	                  scriptCast: this.scriptCast,
	                  elementId: this.arResult.elementId,
	                  options: this.OPTIONS,
	                  fields: this.FIELDS
	                }
	              }).then(function (response) {
	                // обработка ответа
	                if (response.data.result["SUCCESS"]) {
	                  _this2.SuccessEdit = true;
	                } else if (response.data.result["ERROR"]) {
	                  var allFields = Object.assign(_this2.arResult.FIELDS, _this2.arResult.OPTIONS);
	                  response.data.result["ERROR"].forEach(function (code) {
	                    _this2.ERR[code] = allFields[code];
	                  });
	                }
	              }, function (res) {
	                _this2.IsErrorModal = true;
	              });
	            }
	          },
	          bxCreate: function bxCreate() {
	            var _this3 = this;
	            this.ERR = {};
	            for (var key in this.arResult.FIELDS) {
	              if (Object.hasOwnProperty.call(this.arResult.FIELDS, key)) {
	                var field = this.arResult.FIELDS[key];
	                if (field.REQUIRED && !this.FIELDS[key]) {
	                  this.ERR[key] = field;
	                }
	              }
	            }
	            for (var _key3 in this.arResult.OPTIONS) {
	              if (Object.hasOwnProperty.call(this.arResult.OPTIONS, _key3)) {
	                var _field3 = this.arResult.OPTIONS[_key3];
	                if (_field3.REQUIRED && !this.OPTIONS[_key3]) {
	                  this.ERR[_key3] = _field3;
	                }
	              }
	            }
	            for (var _key4 in this.arResult.DATES) {
	              if (Object.hasOwnProperty.call(this.arResult.DATES, _key4)) {
	                var _field4 = this.arResult.DATES[_key4];
	                if (_field4.REQUIRED && !this.FIELDS[_key4]) {
	                  this.ERR[_key4] = _field4;
	                }
	              }
	            }
	            if (!this.isEmpty(this.ERR)) ; else {
	              BX.ajax.runComponentAction(this.componentName, "create", {
	                mode: "class",
	                data: {
	                  // переменные для получения параметров
	                  componentId: this.componentId,
	                  scriptCast: this.scriptCast,
	                  back: this.backID,
	                  options: this.OPTIONS,
	                  fields: this.FIELDS
	                }
	              }).then(function (response) {
	                // обработка ответа
	                if (response.data.result["SUCCESS"]) {
	                  _this3.SuccessAdd = true;
	                  _this3.backURL = response.data.result["BACK_URL"] || "";
	                } else if (response.data.result["ERROR"]) {
	                  var allFields = Object.assign(_this3.arResult.FIELDS, _this3.arResult.OPTIONS);
	                  response.data.result["ERROR"].forEach(function (code) {
	                    _this3.ERR[code] = allFields[code];
	                  });
	                }
	              }, function (res) {
	                _this3.IsErrorModal = true;
	              });
	            }
	          },
	          bxDelete: function bxDelete() {
	            var _this4 = this;
	            BX.ajax.runComponentAction(this.componentName, "delete", {
	              mode: "class",
	              data: {
	                elementId: El_Create_Vue_Id
	              }
	            }).then(function (response) {
	              // обработка ответа
	              if (response.data.result) {
	                _this4.SuccessDelete = true;
	                _this4.IsDeleteModal = false;
	              } else {
	                _this4.IsDeleteModal = false;
	                _this4.IsErrorModal = true;
	              }
	            }, function (response) {
	              _this4.IsDeleteModal = false;
	              _this4.IsErrorModal = true;
	            });
	          },
	          cancel: function cancel() {
	            this.FIELDS = {};
	            this.OPTIONS = {};
	            this.cancelCount++;
	          },
	          cancelEdit: function cancelEdit() {
	            this.FIELDS = JSON.parse(JSON.stringify(this.arResult.ELEMENT.FIELDS));
	            this.OPTIONS = JSON.parse(JSON.stringify(this.arResult.ELEMENT.OPTIONS));
	            this.cancelCount++;
	          },
	          onInputPrice: function onInputPrice(e, key) {
	            // * фикс стоимости
	            this.FIELDS[key] = e.target.value.replace(/[^.\d]/g, "") * 1;
	          }
	        },
	        computed: {
	          disabled: function disabled() {
	            var dis = true;
	            for (var key in this.FIELDS) {
	              if (Object.hasOwnProperty.call(this.FIELDS, key)) {
	                var el = this.FIELDS[key];
	                if (el != "") {
	                  dis = false;
	                }
	              }
	            }
	            for (var _key5 in this.OPTIONS) {
	              if (Object.hasOwnProperty.call(this.OPTIONS, _key5)) {
	                var _el = this.OPTIONS[_key5];
	                if (_el != "") {
	                  dis = false;
	                }
	              }
	            }
	            return dis;
	          },
	          disabledUpdate: function disabledUpdate() {
	            var dis = true;
	            for (var key in this.FIELDS) {
	              if (Object.hasOwnProperty.call(this.FIELDS, key)) {
	                var el = this.FIELDS[key];
	                if (el != this.arResult.ELEMENT.FIELDS[key]) {
	                  dis = false;
	                }
	              }
	            }
	            for (var _key6 in this.OPTIONS) {
	              if (Object.hasOwnProperty.call(this.OPTIONS, _key6)) {
	                var _el2 = this.OPTIONS[_key6];
	                if (_el2 != this.arResult.ELEMENT.OPTIONS[_key6]) {
	                  dis = false;
	                }
	              }
	            }
	            return dis;
	          },
	          disableBtnDelete: function disableBtnDelete() {
	            return this.arResult.delBtn == "Y" && El_Create_Vue_Id != "";
	          }
	        },
	        /* html */
	        template: "\n\t\t\t\t\t<ErrModal @closeModalErr=\"errorDone\" :ERR=\"ERR\" v-if=\"!isEmpty(ERR)\"></ErrModal>\n\t\t\t\t\t<SuccessModalAdd @closeModalSuccess=\"SuccessAddDone\" v-if=\"SuccessAdd\"></SuccessModalAdd>\n\t\t\t\t\t<SuccessModalEdit @closeModalSuccess=\"SuccessEditDone\" v-if=\"SuccessEdit\"></SuccessModalEdit>\n\t\t\t\t\t<SuccessModalDelete @closeModalSuccess=\"SuccessDeleteDone\" v-if=\"SuccessDelete\"></SuccessModalDelete>\n\n\t\t\t\t\t<DeleteModal @closeModal=\"IsDeleteModal = false\" @deleteClick=\"bxDelete\" v-if=\"IsDeleteModal\"></DeleteModal>\n\t\t\t\t\t<ErrorModal @closeModal=\"IsErrorModal = false\" v-if=\"IsErrorModal\"></ErrorModal>\n\n\t\t\t\t\t<Transition>\n\t\t\t\t\t<div v-if=\"fade\" >\n\t\t\t\t\t\t<form id=\"createForm\" action=\"#\" method=\"POST\" class=\"tab-page current-tab-page form--js\" data-page=\"info\" :key=\"cancelCount\">\n\t\t\t\t\t\t\t<div class=\"create-page__input-group flex fd-column\">\n\n\t\t\t\t\t\t\t\t<template v-for=\"(field,key,index) in arResult.OPTIONS\" key=\"index\">\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\" v-if=\"field.TYPE == 'STRING'\">\n\t\t\t\t\t\t\t\t\t\t\t<input :data-name=\"field.CODE\" type=\"text\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" :name=\"field.CODE\" v-model=\"OPTIONS[key]\">\n\t\t\t\t\t\t\t\t\t\t\t<label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\" v-if=\"field.TYPE == 'HTML'\">\n\t\t\t\t\t\t\t\t\t\t\t<textarea :data-name=\"field.CODE\" :name=\"field.CODE\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" @input=\"OPTIONS[key] = $event.target.value\" v-html=\"OPTIONS[key]\"></textarea>\n\t\t\t\t\t\t\t\t\t\t\t<label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</template>\n\n\t\t\t\t\t\t\t\t<div class=\"datepickers-group flex fd-column-xs\" v-if=\"arResult.DATES\">\n\t\t\t\t\t\t\t\t\t\t<DateInput v-for=\"(field,key) in arResult.DATES\" :key=\"key\" :field=\"field\" @input=\"FIELDS[key] = $event.target.value\" :value=\"FIELDS[key]\"></DateInput>\n\t\t\t\t\t\t\t\t\t\t<div class=\"help-icon help-icon--js\">\n\t\t\t\t\t\t\t\t\t\t<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t\t\t\t\t\t\t\t<path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M5.5415 2.33417C7.45322 1.05679 9.7008 0.375 12 0.375C15.0831 0.375 18.04 1.59977 20.2201 3.77988C22.4002 5.95999 23.625 8.91686 23.625 12C23.625 14.2992 22.9432 16.5468 21.6658 18.4585C20.3885 20.3702 18.5729 21.8602 16.4487 22.7401C14.3245 23.62 11.9871 23.8502 9.73208 23.4016C7.47705 22.9531 5.40567 21.8459 3.77989 20.2201C2.1541 18.5943 1.04693 16.523 0.598376 14.2679C0.149823 12.0129 0.380037 9.6755 1.25991 7.55131C2.13977 5.42711 3.62978 3.61154 5.5415 2.33417ZM12 1.625C9.94802 1.625 7.94212 2.23348 6.23596 3.3735C4.5298 4.51352 3.20001 6.13388 2.41476 8.02966C1.6295 9.92544 1.42404 12.0115 1.82436 14.0241C2.22468 16.0366 3.2128 17.8853 4.66377 19.3362C6.11474 20.7872 7.96339 21.7753 9.97594 22.1756C11.9885 22.576 14.0746 22.3705 15.9703 21.5852C17.8661 20.8 19.4865 19.4702 20.6265 17.764C21.7665 16.0579 22.375 14.052 22.375 12C22.375 9.24838 21.2819 6.60946 19.3362 4.66377C17.3905 2.71808 14.7516 1.625 12 1.625Z\" />\n\t\t\t\t\t\t\t\t\t\t\t\t<path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M13.3842 6.65501C13.1508 6.54205 12.6497 6.43007 12.0098 6.43781C11.3316 6.44709 10.6616 6.59638 10.1241 7.03683M13.3842 6.65501C13.9874 6.94884 14.8664 7.52652 14.8664 8.68778C14.8664 9.96484 14.1228 10.538 12.8417 11.4139C12.1468 11.889 11.6583 12.3954 11.3503 12.9646C11.0396 13.5389 10.944 14.1194 10.944 14.6875C10.944 15.1017 11.2817 15.4375 11.6983 15.4375C12.1149 15.4375 12.4526 15.1017 12.4526 14.6875C12.4526 14.2942 12.5169 13.9745 12.6789 13.6752C12.8436 13.3708 13.1404 13.0301 13.6965 12.6498L13.7255 12.63C14.9404 11.7995 16.375 10.8188 16.375 8.68778C16.375 6.59463 14.7345 5.64237 14.0471 5.30762L14.0465 5.30732L14.0459 5.30702C13.5241 5.05411 12.7629 4.92857 11.9907 4.93799L11.9902 4.938L11.9897 4.93801C11.121 4.9498 10.0646 5.14178 9.16453 5.8794L9.16451 5.87942C8.4402 6.47303 8.05494 7.14713 7.8523 7.67966C7.75138 7.94487 7.69552 8.17532 7.66454 8.34499C7.64902 8.43 7.63965 8.50034 7.63399 8.55289C7.63116 8.57918 7.62926 8.60109 7.62798 8.61824C7.62734 8.62681 7.62686 8.6342 7.6265 8.64035C7.62632 8.64343 7.62616 8.6462 7.62604 8.64866L7.62587 8.65211L7.62579 8.65366L7.62576 8.65439C7.62575 8.65474 7.62573 8.65509 8.37932 8.68778L7.62573 8.65509C7.60757 9.06889 7.93024 9.41898 8.34644 9.43704C8.75998 9.45498 9.11025 9.13837 9.13253 8.7284C9.13269 8.72627 9.13314 8.72088 9.13405 8.71246C9.13609 8.69345 9.14042 8.65936 9.14891 8.61286C9.16595 8.5195 9.19933 8.37858 9.26332 8.21041C9.3905 7.87616 9.63772 7.43544 10.1241 7.03685\" />\n\t\t\t\t\t\t\t\t\t\t\t\t<path d=\"M11.625 18.875C12.2463 18.875 12.75 18.3713 12.75 17.75C12.75 17.1287 12.2463 16.625 11.625 16.625C11.0037 16.625 10.5 17.1287 10.5 17.75C10.5 18.3713 11.0037 18.875 11.625 18.875Z\" />\n\t\t\t\t\t\t\t\t\t\t</svg>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"help-info small help-info--js\">\n\t\t\t\t\t\t\t\t\t{{this.$Bitrix.Loc.getMessage('DATE_INFO')}}\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t<template v-for=\"(field,key,index) in arResult.FIELDS\" key=\"index\">\n\t\t\t\t\t\t\t\t\t<div class=\"create-page__subdivisions\" v-if=\"field.TYPE == 'SELECT'\">\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"__select __select_tariff __select-form-js \">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"__select__title __select_tariff__title\" :style=\"FIELDS[key]?'color: rgb(27, 37, 74);':''\" v-html=\"FIELDS[key]?field.VARIABLES[FIELDS[key]]:field.NAME + (field.REQUIRED?' *':'')\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"select_placeholder\" :class=\"{'visible':FIELDS[key]}\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></div>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"__select__content __select_tariff__content\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"__select__content_scrolling\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" :data-name=\"field.CODE\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" v-model=\"FIELDS[key]\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label v-if=\"!field.REQUIRED && FIELDS[key]\" class=\"__select__label __select_tariff__label\" @click=\"delete FIELDS[key]\">{{this.$Bitrix.Loc.getMessage('NOTHING')}}</label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<template v-for=\"(variable,key) in field.VARIABLES\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input @click=\"FIELDS[field.CODE] = key\" :id=\"key\" class=\"__select__input\" type=\"radio\" :name=\"key\" :value=\"key\" />\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label :for=\"key\" :data-val=\"key\" class=\"__select__label __select_tariff__label\" v-html=\"variable\"></label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</template>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<template v-if=\"field.LINK_ADD\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a :href=\"'/personal/'+field.API_CODE+'-create/?back='+arResult.elementId\" class=\"__select__create\">{{this.$Bitrix.Loc.getMessage('NEW_ELEMENT')}}</a>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</template>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\" v-if=\"field.TYPE == 'INT'\">\n\t\t\t\t\t\t\t\t\t\t\t<input :value=\"FIELDS[key]\" @input=\"(e)=>{onInputPrice(e,key)}\" :data-name=\"field.CODE\" :class=\"{'format-number-field--js':field.CODE=='PRICE'}\" type=\"text\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" :name=\"field.CODE\">\n\t\t\t\t\t\t\t\t\t\t\t<label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\" v-if=\"field.TYPE == 'STRING'\">\n\t\t\t\t\t\t\t\t\t\t\t<input :data-name=\"field.CODE\" type=\"text\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" :name=\"field.CODE\" v-model=\"FIELDS[key]\">\n\t\t\t\t\t\t\t\t\t\t\t<label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\" v-if=\"field.TYPE == 'HTML'\" >\n\t\t\t\t\t\t\t\t\t\t\t<textarea :data-name=\"field.CODE\" :name=\"field.CODE\" :placeholder=\"field.NAME + (field.REQUIRED?' *':'')\" v-model=\"FIELDS[key]\"></textarea>\n\t\t\t\t\t\t\t\t\t\t\t<label :for=\"field.CODE\" v-html=\"field.NAME + (field.REQUIRED?' *':'')\"></label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</template>\n\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<div class=\"create-page__button-group flex\">\n\t\t\t\t\t\t\t\t<button v-if=\"arResult.elementId\" @click.prevent=\"bxEdit\" class=\"btn-blue p13-24\" type=\"submit\" :disabled=\"disabledUpdate\">{{this.$Bitrix.Loc.getMessage('SAVE')}}</button>\n\t\t\t\t\t\t\t\t<button v-if=\"arResult.elementId\" @click=\"cancelEdit\" class=\"btn-white p13-24\" type=\"button\" :disabled=\"disabledUpdate\">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>\n\t\t\t\t\t\t\t\t<button v-if=\"!arResult.elementId\" @click.prevent=\"bxCreate\" class=\"btn-blue p13-24\" type=\"submit\" :disabled=\"disabled\">{{this.$Bitrix.Loc.getMessage('ADD')}}</button>\n\t\t\t\t\t\t\t\t<button v-if=\"!arResult.elementId\" @click=\"cancel\" class=\"btn-white p13-24\" type=\"button\" :disabled=\"disabled\">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>\n\t\t\t\t\t\t\t\t<button v-if=\"disableBtnDelete\" @click.prevent=\"IsDeleteModal = true\" class=\"btn-delete\" type=\"button\">{{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}</button>\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t</form>\n\t\t\t\t\t</div>\n\t\t\t\t\t</Transition>\n\t\t\t"
	      }));
	      babelHelpers.classPrivateFieldGet(this, _app).mount(this.rootNode);
	    }
	  }]);
	  return ElementCreate;
	}();

	exports.ElementCreate = ElementCreate;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3));
//# sourceMappingURL=el-create.bundle.js.map
