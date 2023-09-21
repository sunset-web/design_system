import { BitrixVue } from "ui.vue3";
import { CommentCard } from "./comment";
import { createPinia, mapState, mapActions } from "ui.vue3.pinia";
import { commentsStore } from "./commentsStore";
const store = createPinia();

export class Comments {
	#application;

	constructor(rootNode) {
		this.rootNode = document.querySelector(rootNode);
	}

	init(id) {
		this.#application = BitrixVue.createApp({
			components: {
				CommentCard,
			},
			data() {
				return {
					itemID: id,
					text: "",
					loading: false,
				};
			},
			computed: {
				...mapState(commentsStore, ["units"]),
				commentsLoc() {
					let u = this.units.filter((e) => e.id == this.itemID)[0];
					let res = u ? u.comments : null;
					return res;
				},
				offset() {
					return this.commentsLoc.length;
				},
			},
			methods: {
				send(e) {
					e.preventDefault();
					BX.ajax
						.runComponentAction("itin:comments", "create", {
							mode: "class",
							data: {
								unit: this.itemID,
								text: this.text,
							},
						})
						.then((response) => {
							this.text = "";
						});
				},
				load() {
					if (this.$refs.list.scrollTop < 300 && !this.loading) {
						this.loading = true;
						BX.ajax
							.runComponentAction("itin:comments", "get", {
								mode: "class",
								data: {
									unit: this.itemID,
									offset: this.offset,
								},
							})
							.then((response) => {
								this.loadComments(this.itemID, response.data.result);
								setTimeout(() => {
									this.loading = false;
								}, 300);
							});
					}
				},
				...mapActions(commentsStore, {
					constructUnit: "constructUnit",
					getUnit: "getUnit",
					createComments: "createComments",
					loadComments: "loadComments",
				}),
			},
			mounted() {
				let arrInStore = this.getUnit(this.itemID);
				if (!arrInStore) {
					BX.ajax
						.runComponentAction("itin:comments", "get", {
							mode: "class",
							data: {
								unit: this.itemID,
								offset: 0,
							},
						})
						.then((response) => {
							this.constructUnit(this.itemID, response.data.result);
						});
				}
			},
			/* html */
			template: `
					<ul ref="list" class="comments__list" @scroll.prevent="load">
						<CommentCard v-for="comment in commentsLoc" :comment="comment" :key="comment.id"></CommentCard>
					</ul>
					<div class="comments__functional">
						<textarea v-model="text" name="new-comment" :placeholder="$Bitrix.Loc.getMessage('NEW_COMMENT_PH')"></textarea>
						<div class="comments__button-group flex fd-column-xs">
							<button type="submit" class="btn-blue p13-24 comment-js-create-btn" @click="send">{{$Bitrix.Loc.getMessage('COMMENT_SEND_BTN')}}</button>
							<button class="btn-white p13-24 cancel">{{$Bitrix.Loc.getMessage('COMMENT_CANCLE_BTN')}}</button>
						</div>
					</div>
    `,
		});
		this.#application.use(store).mount(this.rootNode);
	}

	destroy() {
		this.#application.unmount();
	}
}

BX.PULL.start();
BX.addCustomEvent("onPullEvent", (module_id, command, params) => {
	if (module_id == "addnewcommentnotification" && command == "sendComment") {
		console.log(params);
		commentsStore().createComments(params.response.id, params.response.res);
		updateList();
		$(".comment-notification-js").addClass("active-notification");
		setTimeout(() => {
			$(".comment-notification-js").removeClass("active-notification");
		}, 4000);
	}
});
