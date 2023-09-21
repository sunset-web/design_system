this.BX = this.BX || {};
(function (exports,ui_vue3) {
  'use strict';

  var SuccessModal = {
    /*html*/
    template: "\n  <div style=\"position:relative; z-index:200;\">\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n    <div class=\"modal-loading-result flex fd-column ai-center active\">\n      <div class=\"modal-loading-result__title tac\">{{this.$Bitrix.Loc.getMessage('SUCCESS_SAVED')}}</div>\n      <p class=\"tac small\">{{this.$Bitrix.Loc.getMessage('SUCCESS_SAVED_DOP')}}</p>\n      <button @click=\"$emit('closeModal');\" class=\"btn-blue p13-24\">{{this.$Bitrix.Loc.getMessage('OK_BTN')}}</button>\n    </div>\n  </div>\n  "
  };

  var ErrorModal = {
    props: ["ERROR"],
    /*html*/
    template: "\n  <div style=\"position:relative; z-index:200;\">\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-js active\"></div>\n    <div @click=\"$emit('closeModal');\" class=\"overlay overlay-desktop overlay-desktop-js active\"></div>\n    <div class=\"modal-loading-result flex fd-column ai-center ajax-error active\" style=\"height: fit-content;\">\n      <h3 class=\"modal-loading-result__title tac\">\u041E\u0448\u0438\u0431\u043A\u0430</h3>\n      <p class=\"tac small\" style=\"text-align:left\" v-html=\"ERROR\"></p>\n      <button @click=\"$emit('closeModal');\" class=\"btn-red p13-24\">\u041E\u043A\u0435\u0439</button>\n    </div>\n  </div>\n"
  };

  function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
  function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
  var _app = /*#__PURE__*/new WeakMap();
  var ChangePass = /*#__PURE__*/function () {
    function ChangePass(rootNode) {
      babelHelpers.classCallCheck(this, ChangePass);
      _classPrivateFieldInitSpec(this, _app, {
        writable: true,
        value: void 0
      });
      this.rootNode = document.querySelector(rootNode);
    }
    babelHelpers.createClass(ChangePass, [{
      key: "init",
      value: function init() {
        babelHelpers.classPrivateFieldSet(this, _app, ui_vue3.BitrixVue.createApp({
          name: "ChangePass",
          components: {
            SuccessModal: SuccessModal,
            ErrorModal: ErrorModal
          },
          data: function data() {
            return {
              Success: false,
              Error: false,
              fade: 0,
              result: {},
              password: "",
              confirm_password: "",
              old_password: ""
            };
          },
          mounted: function mounted() {
            this.fade++;
          },
          methods: {
            SuccessDone: function SuccessDone() {
              this.Success = false;
            },
            ErrorDone: function ErrorDone() {
              this.Error = false;
            },
            save: function save() {
              var _this = this;
              BX.ajax({
                url: "https://itin.online/local/api/change.pass/default.php",
                data: {
                  password: this.password,
                  confirm_password: this.confirm_password,
                  old_password: this.old_password
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
                  if (res.SUCCESS == "Y") {
                    _this.Success = true;
                    _this.disableBtns = true;
                  } else {
                    _this.Error = res.ERROR;
                  }
                },
                onfailure: function onfailure(err) {
                  console.error(err);
                }
              });
            }
          },
          computed: {
            disableBtns: function disableBtns() {
              if (this.password !== "" && this.confirm_password !== "" && this.old_password !== "") {
                return false;
              } else {
                return true;
              }
            }
          },
          /* html */
          template: "\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<SuccessModal @closeModal=\"SuccessDone\" v-if=\"Success\"></SuccessModal>\n\t\t\t\t\t</Transition>\n\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<ErrorModal :ERROR=\"this.Error\" @closeModal=\"ErrorDone\" v-if=\"Error\"></ErrorModal>\n\t\t\t\t\t</Transition>\n\n\t\t\t\t\t<Transition>\n\t\t\t\t\t\t<div v-if=\"fade\" >\n\t\t\t\t\t\t\t<form class=\"security-form security-form--js form--js\">\n\n\t\t\t\t\t\t\t\t<div class=\"security-inputs-group flex fd-column\">\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"password\" :placeholder=\"this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS3')\" name=\"old_password\" v-model=\"old_password\" >\n\t\t\t\t\t\t\t\t\t\t<label class=\"js-label-password\" for=\"old_password\">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS3')}}</label>\n\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"eye-button eye-button--js\"></button>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"password\" :placeholder=\"this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS')\" name=\"password\" v-model=\"password\" >\n\t\t\t\t\t\t\t\t\t\t<label class=\"js-label-password\" for=\"password\">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS')}}</label>\n\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"eye-button eye-button--js\"></button>\n\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t<div class=\"form-input-field-group form-input-field-group--js\">\n\t\t\t\t\t\t\t\t\t\t<input type=\"password\" :placeholder=\"this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS2')\" name=\"confirm_password\" v-model=\"confirm_password\" >\n\t\t\t\t\t\t\t\t\t\t<label class=\"js-label-password\" for=\"confirm_password\">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_PASS2')}}</label>\n\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"eye-button eye-button--js\"></button>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t<button @click=\"save\" class=\"security-btn btn-blue p13-24 sign-in-btn\" type=\"submit\" :disabled=\"disableBtns\">{{this.$Bitrix.Loc.getMessage('MAIN_AUTH_CHD_FIELD_SUBMIT')}}</button>\n\n\t\t\t\t\t\t\t</form>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</Transition>\n\t\t\t"
        }));
        babelHelpers.classPrivateFieldGet(this, _app).mount(this.rootNode);
      }
    }]);
    return ChangePass;
  }();
  // {{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}

  exports.ChangePass = ChangePass;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3));
//# sourceMappingURL=change-pass.bundle.js.map
