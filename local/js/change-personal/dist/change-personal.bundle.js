this.BX = this.BX || {};
(function (exports,ui_vue3) {
  'use strict';

  var ErrorModal = {
    props: ["ERROR"],
    /*html*/
    template: "\n  <div style=\"position:relative; z-index:200;\">\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n    <div class=\"modal-loading-result flex fd-column ai-center ajax-error active\" style=\"height: fit-content;\">\n      <h3 class=\"modal-loading-result__title tac\">\u041E\u0448\u0438\u0431\u043A\u0430</h3>\n      <p class=\"tac small\" style=\"text-align:left\" v-html=\"ERROR\"></p>\n      <button @click=\"$emit('closeModal');\" class=\"btn-red p13-24\">\u041E\u043A\u0435\u0439</button>\n    </div>\n  </div>\n"
  };

  var SuccessModal = {
    /*html*/
    template: "\n  <div style=\"position:relative; z-index:200;\">\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n    <div class=\"modal-loading-result flex fd-column ai-center ajax-success active\">\n      <div class=\"modal-loading-result__title tac\">\u0423\u0441\u043F\u0435\u0448\u043D\u043E</div>\n      <p class=\"tac small\">\u0412\u0441\u0435 \u0438\u0437\u043C\u0435\u043D\u0435\u043D\u0438\u044F \u0443\u0441\u043F\u0435\u0448\u043D\u043E \u0441\u043E\u0445\u0440\u0430\u043D\u0435\u043D\u044B</p>\n      <button @click=\"$emit('closeModal');\" class=\"btn-blue p13-24 js-modal-close\">\u041E\u043A\u0435\u0439</button>\n    </div>\n  </div>\n"
  };

  function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
  function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
  var _app = /*#__PURE__*/new WeakMap();
  var ChangePersonal = /*#__PURE__*/function () {
    function ChangePersonal(rootNode) {
      babelHelpers.classCallCheck(this, ChangePersonal);
      _classPrivateFieldInitSpec(this, _app, {
        writable: true,
        value: void 0
      });
      this.rootNode = document.querySelector(rootNode);
    }
    babelHelpers.createClass(ChangePersonal, [{
      key: "init",
      value: function init() {
        babelHelpers.classPrivateFieldSet(this, _app, ui_vue3.BitrixVue.createApp({
          name: "ChangePersonal",
          components: {
            ErrorModal: ErrorModal,
            SuccessModal: SuccessModal
          },
          data: function data() {
            return {
              RequiredFields: ["NAME", "LAST_NAME", "EMAIL", "PERSONAL_PHONE"],
              Fields: {},
              defaults: {},
              cancelCount: 0,
              Error: false,
              Success: false,
              fade: 0
            };
          },
          mounted: function mounted() {
            var _this = this;
            this.fade++;
            //
            // https://dev.1c-bitrix.ru/api_help/js_lib/ajax/bx_ajax.php
            BX.ajax({
              url: "/local/api/change.personal/default.php",
              data: {
                action: "get"
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
              onsuccess: function onsuccess(data) {
                var res = JSON.parse(data);
                _this.Fields.LAST_NAME = res.LAST_NAME;
                _this.Fields.NAME = res.NAME;
                _this.Fields.SECOND_NAME = res.SECOND_NAME;
                _this.Fields.EMAIL = res.EMAIL;
                _this.Fields.PERSONAL_PHONE = res.PERSONAL_PHONE;
                _this.defaults = JSON.parse(JSON.stringify(_this.Fields));
              },
              onfailure: function onfailure(data) {
                console.log(data);
              }
            });
          },
          methods: {
            SuccessDone: function SuccessDone() {
              this.Success = false;
            },
            ErrorDone: function ErrorDone() {
              this.Error = false;
            },
            cancel: function cancel() {
              this.Fields = JSON.parse(JSON.stringify(this.defaults));
              this.cancelCount++;
            },
            save: function save() {
              var _this2 = this;
              var err = [];
              this.RequiredFields.forEach(function (code) {
                if (_this2.Fields[code] == "") {
                  err.push(code);
                }
              });
              if (err.length > 0) {
                this.Error = "";
                err.forEach(function (code) {
                  var name = $("input[name=\"".concat(code, "\"]")).attr("placeholder");
                  var str = "Не заполнено обязательное поле " + name + "<br>";
                  if (name) {
                    _this2.Error += str;
                  }
                });
              } else {
                BX.ajax({
                  url: "https://itin.online/local/api/change.personal/default.php",
                  data: {
                    action: "update",
                    fields: {
                      LAST_NAME: this.Fields.LAST_NAME,
                      NAME: this.Fields.NAME,
                      SECOND_NAME: this.Fields.SECOND_NAME,
                      EMAIL: this.Fields.EMAIL,
                      PHONE_NUMBER: this.Fields.PERSONAL_PHONE
                    }
                  },
                  method: "POST",
                  timeout: 30,
                  async: true,
                  processData: true,
                  scriptsRunFirst: true,
                  emulateOnload: true,
                  start: true,
                  cache: false,
                  onsuccess: function onsuccess(data) {
                    // обработка ответа
                    var res = JSON.parse(data);
                    console.log(res);
                    if (res.SUCCESS) {
                      _this2.Success = true;
                      _this2.defaults = JSON.parse(JSON.stringify(_this2.Fields));
                      $(".header__profile--js .user_name").html(_this2.Fields.NAME + " " + _this2.Fields.LAST_NAME);
                    } else {
                      _this2.Error = res.ERROR;
                    }
                  },
                  onfailure: function onfailure(err) {
                    console.error(err);
                  }
                });
              }
            }
          },
          computed: {
            disabled: function disabled() {
              var dis = true;
              for (var key in this.Fields) {
                if (Object.hasOwnProperty.call(this.Fields, key)) {
                  if (this.Fields[key] != this.defaults[key]) {
                    dis = false;
                  }
                }
              }
              return dis;
            }
          },
          // watch: {
          // 	Fields: {
          // 		handler(val) {
          // 			// console.log(val);
          // 		},
          // 		deep: true,
          // 	},
          // },
          /* html */
          template: "\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<ErrorModal @closeModal=\"ErrorDone\" :ERROR=\"Error\" v-if=\"Error\"></ErrorModal>\n\t\t\t\t\t</Transition>\n\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<SuccessModal @closeModal=\"SuccessDone\" v-if=\"Success\"></SuccessModal>\n\t\t\t\t\t</Transition>\n\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<div v-if=\"fade\" >\n\t\t\t\t\t\t\t<form class=\"profile__form--js\" :key=\"cancelCount\">\n\t\t\t\t\t\t\t\t<div class=\"profile__input-group flex fd-column\">\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" :placeholder=\"this.$Bitrix.Loc.getMessage('LAST_NAME')\" name=\"LAST_NAME\" v-model=\"Fields.LAST_NAME\">\n\t\t\t\t\t\t\t\t\t\t<label for=\"last-name\">{{this.$Bitrix.Loc.getMessage('LAST_NAME')}}</label>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" :placeholder=\"this.$Bitrix.Loc.getMessage('NAME')\" name=\"NAME\" v-model=\"Fields.NAME\">\n\t\t\t\t\t\t\t\t\t\t<label for=\"first-name\">{{this.$Bitrix.Loc.getMessage('NAME')}}</label>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" :placeholder=\"this.$Bitrix.Loc.getMessage('SECOND_NAME')\" name=\"SECOND_NAME\" v-model=\"Fields.SECOND_NAME\">\n\t\t\t\t\t\t\t\t\t\t<label for=\"middle-name\">{{this.$Bitrix.Loc.getMessage('SECOND_NAME')}}</label>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"text\" :placeholder=\"this.$Bitrix.Loc.getMessage('EMAIL')\" name=\"EMAIL\" v-model=\"Fields.EMAIL\">\n\t\t\t\t\t\t\t\t\t\t<label for=\"email\">{{this.$Bitrix.Loc.getMessage('EMAIL')}}</label>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input ref=\"phone\" type=\"text\" :placeholder=\"this.$Bitrix.Loc.getMessage('USER_PHONE')\" name=\"PERSONAL_PHONE\" v-model=\"Fields.PERSONAL_PHONE\" class=\"phone-input--js\" maxlength=\"18\">\n\t\t\t\t\t\t\t\t\t\t<label for=\"phone\">{{this.$Bitrix.Loc.getMessage('USER_PHONE')}}</label>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t<div class=\"profile__button-group flex\">\n\t\t\t\t\t\t\t\t\t<button @click.prevent=\"save\" class=\"btn-blue p13-24\" :disabled=\"disabled\">{{this.$Bitrix.Loc.getMessage('SAVE_BTN')}}</button>\n\t\t\t\t\t\t\t\t\t<button @click.prevent=\"cancel\" class=\"btn-white p13-24\" :disabled=\"disabled\">{{this.$Bitrix.Loc.getMessage('MAIN_RESET')}}</button>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</form>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</Transition>\n\t\t\t"
        }));
        babelHelpers.classPrivateFieldGet(this, _app).mount(this.rootNode);
      }
    }]);
    return ChangePersonal;
  }();

  exports.ChangePersonal = ChangePersonal;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3));
//# sourceMappingURL=change-personal.bundle.js.map
