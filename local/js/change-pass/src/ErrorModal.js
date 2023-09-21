export const ErrorModal = {
	props: ["ERROR"],
	/*html*/
	template: `
  <div style="position:relative; z-index:200;">
    <div @click="$emit('closeModal');" class="overlay overlay-js active"></div>
    <div @click="$emit('closeModal');" class="overlay overlay-desktop overlay-desktop-js active"></div>
    <div class="modal-loading-result flex fd-column ai-center ajax-error active" style="height: fit-content;">
      <h3 class="modal-loading-result__title tac">Ошибка</h3>
      <p class="tac small" style="text-align:left" v-html="ERROR"></p>
      <button @click="$emit('closeModal');" class="btn-red p13-24">Окей</button>
    </div>
  </div>
`,
};
