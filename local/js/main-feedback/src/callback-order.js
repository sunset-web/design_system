import { BitrixVue } from "ui.vue3";
import { MainFeedback } from "./main-feedback";

export class CallbackOrder {
	#app;

	constructor(rootNode) {
		this.rootNode = document.querySelector(rootNode);
	}

	init() {
		this.#app = BitrixVue.createApp({
			name: "CallbackOrder",
			components: {
				MainFeedback,
			},
			/* html */
			template: `
				<MainFeedback></MainFeedback>
			`,
		});
		this.#app.mount(this.rootNode);
	}
}
