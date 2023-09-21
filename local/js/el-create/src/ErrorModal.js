export const ErrorModal = {
	name: "ErrorModal",
	/*html*/
	template: `
    <div @click="$emit('closeModal');" class="overlay overlay-js active"></div>
    <div @click="$emit('closeModal');" class="overlay overlay-desktop overlay-desktop-js active"></div>
    <div class="modal-loading-result flex fd-column ai-center ajax-error active" style="height: fit-content;">
      <h3 class="modal-loading-result__title tac">{{this.$Bitrix.Loc.getMessage('ERR')}}</h3>
      <p class="tac small" style="text-align:left"></p>
      <button @click="$emit('closeModal');" class="btn-red p13-24">{{this.$Bitrix.Loc.getMessage('OK_BTN')}}</button>
    </div>
  `,
};
