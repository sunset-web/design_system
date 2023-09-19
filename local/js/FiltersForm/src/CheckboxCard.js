import { BitrixVue } from "ui.vue3";
import { BaseEvent } from "main.core.events";
export const CheckboxCard = {
	name: "CheckboxCard",
	props: ["value", "name"],
	data() {
		return {
			checked: [],
		};
	},
	methods: {
		filtered() {
			this.$Bitrix.eventEmitter.emit("filter", {
				name: this.name,
				values: this.checked,
			});
		},
	},
	watch: {
		checked() {
			this.filtered();
		},
	},
	/*html*/
	template: `
        <div class="select select--js">
          <div class="select-title select-title--js">
            <input type="text" :placeholder="value.NAME" :name="name" :id="name" autocomplete="off">
            <label :for="name">{{value.NAME}}</label>
          </div>
          <div class="options options--js">
            <div class="options_wrapper">
              <div class="checkbox_wrapper" v-for="variable of value.LIST">
                <label class="custom-checkbox option-label">
                  <input type="checkbox" :value="variable.ID" v-model="checked">
                  <span class="checkbox_content">{{variable.NAME}}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
  `,
};
