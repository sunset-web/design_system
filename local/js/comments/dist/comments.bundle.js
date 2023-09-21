this.BX = this.BX || {};
(function (exports,ui_vue3,ui_vue3_pinia) {
  'use strict';

  var CommentCard = {
    mounted: function mounted() {
      this.$nextTick(function () {
        this.$refs.elem.scrollIntoView();
      });
    },
    props: ["comment"],
    computed: {
      htmlText: function htmlText() {
        // функция из index.js
        return findAndReplaceLink(this.comment.text);
      }
    },
    /* html */
    template: "\n    <li ref=\"elem\" class=\"comments__item\" :data-id=\"comment.id\">\n      <div class=\"comments__date\">{{comment.date}}</div>\n      <div class=\"comments__message\">{{$Bitrix.Loc.getMessage('COMMENT_USER', {'#USER#': comment.user})}}{{comment.user}}</div>\n      <div class=\"comments__text\" v-html=\"htmlText\"></div>\n    </li>\n  "
  };

  var commentsStore = ui_vue3_pinia.defineStore("commentsStore", {
    state: function state() {
      return {
        units: []
      };
    },
    actions: {
      getUnit: function getUnit(id) {
        var arrInStore = this.units.find(function (unit) {
          return unit.id === id;
        });
        return arrInStore;
      },
      constructUnit: function constructUnit(id, comments) {
        var arrInStore = this.units.find(function (unit) {
          return unit.id === id;
        });
        if (arrInStore) {
          arrInStore.comments = comments;
        } else {
          this.units.push({
            id: id,
            comments: comments
          });
        }
      },
      createComments: function createComments(id, comment) {
        var arrInStore = this.units.find(function (unit) {
          return unit.id === id;
        });
        if (arrInStore) {
          arrInStore.comments.push(comment);
        } else {
          this.units.push({
            id: id,
            comments: comment
          });
        }
        return comment.id;
      },
      loadComments: function loadComments(id, comments) {
        var _arrInStore$comments;
        var arrInStore = this.units.find(function (unit) {
          return unit.id === id;
        });
        (_arrInStore$comments = arrInStore.comments).unshift.apply(_arrInStore$comments, babelHelpers.toConsumableArray(comments));
      }
    }
  });

  function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
  function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { babelHelpers.defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
  function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
  function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
  var store = ui_vue3_pinia.createPinia();
  var _application = /*#__PURE__*/new WeakMap();
  var Comments = /*#__PURE__*/function () {
    function Comments(rootNode) {
      babelHelpers.classCallCheck(this, Comments);
      _classPrivateFieldInitSpec(this, _application, {
        writable: true,
        value: void 0
      });
      this.rootNode = document.querySelector(rootNode);
    }
    babelHelpers.createClass(Comments, [{
      key: "init",
      value: function init(id) {
        babelHelpers.classPrivateFieldSet(this, _application, ui_vue3.BitrixVue.createApp({
          components: {
            CommentCard: CommentCard
          },
          data: function data() {
            return {
              itemID: id,
              text: "",
              loading: false
            };
          },
          computed: _objectSpread(_objectSpread({}, ui_vue3_pinia.mapState(commentsStore, ["units"])), {}, {
            commentsLoc: function commentsLoc() {
              var _this = this;
              var u = this.units.filter(function (e) {
                return e.id == _this.itemID;
              })[0];
              var res = u ? u.comments : null;
              return res;
            },
            offset: function offset() {
              return this.commentsLoc.length;
            }
          }),
          methods: _objectSpread({
            send: function send(e) {
              var _this2 = this;
              e.preventDefault();
              BX.ajax.runComponentAction("itin:comments", "create", {
                mode: "class",
                data: {
                  unit: this.itemID,
                  text: this.text
                }
              }).then(function (response) {
                _this2.text = "";
              });
            },
            load: function load() {
              var _this3 = this;
              if (this.$refs.list.scrollTop < 300 && !this.loading) {
                this.loading = true;
                BX.ajax.runComponentAction("itin:comments", "get", {
                  mode: "class",
                  data: {
                    unit: this.itemID,
                    offset: this.offset
                  }
                }).then(function (response) {
                  _this3.loadComments(_this3.itemID, response.data.result);
                  setTimeout(function () {
                    _this3.loading = false;
                  }, 300);
                });
              }
            }
          }, ui_vue3_pinia.mapActions(commentsStore, {
            constructUnit: "constructUnit",
            getUnit: "getUnit",
            createComments: "createComments",
            loadComments: "loadComments"
          })),
          mounted: function mounted() {
            var _this4 = this;
            var arrInStore = this.getUnit(this.itemID);
            if (!arrInStore) {
              BX.ajax.runComponentAction("itin:comments", "get", {
                mode: "class",
                data: {
                  unit: this.itemID,
                  offset: 0
                }
              }).then(function (response) {
                _this4.constructUnit(_this4.itemID, response.data.result);
              });
            }
          },
          /* html */
          template: "\n\t\t\t\t\t<ul ref=\"list\" class=\"comments__list\" @scroll.prevent=\"load\">\n\t\t\t\t\t\t<CommentCard v-for=\"comment in commentsLoc\" :comment=\"comment\" :key=\"comment.id\"></CommentCard>\n\t\t\t\t\t</ul>\n\t\t\t\t\t<div class=\"comments__functional\">\n\t\t\t\t\t\t<textarea v-model=\"text\" name=\"new-comment\" :placeholder=\"$Bitrix.Loc.getMessage('NEW_COMMENT_PH')\"></textarea>\n\t\t\t\t\t\t<div class=\"comments__button-group flex fd-column-xs\">\n\t\t\t\t\t\t\t<button type=\"submit\" class=\"btn-blue p13-24 comment-js-create-btn\" @click=\"send\">{{$Bitrix.Loc.getMessage('COMMENT_SEND_BTN')}}</button>\n\t\t\t\t\t\t\t<button class=\"btn-white p13-24 cancel\">{{$Bitrix.Loc.getMessage('COMMENT_CANCLE_BTN')}}</button>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n    "
        }));
        babelHelpers.classPrivateFieldGet(this, _application).use(store).mount(this.rootNode);
      }
    }, {
      key: "destroy",
      value: function destroy() {
        babelHelpers.classPrivateFieldGet(this, _application).unmount();
      }
    }]);
    return Comments;
  }();
  BX.PULL.start();
  BX.addCustomEvent("onPullEvent", function (module_id, command, params) {
    if (module_id == "addnewcommentnotification" && command == "sendComment") {
      console.log(params);
      commentsStore().createComments(params.response.id, params.response.res);
      updateList();
      $(".comment-notification-js").addClass("active-notification");
      setTimeout(function () {
        $(".comment-notification-js").removeClass("active-notification");
      }, 4000);
    }
  });

  exports.Comments = Comments;

}((this.BX.Local = this.BX.Local || {}),BX.Vue3,BX.Vue3.Pinia));
//# sourceMappingURL=comments.bundle.js.map
