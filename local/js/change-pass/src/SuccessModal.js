export const SuccessModal = {
	/*html*/
	template: `
  <div style="position:relative; z-index:200;">
    <div @click="$emit('closeModal');" class="overlay overlay-js active"></div>
    <div @click="$emit('closeModal');" class="overlay overlay-desktop overlay-desktop-js active"></div>
    <div class="modal-loading-result flex fd-column ai-center active">
      <div class="modal-loading-result__title tac">{{this.$Bitrix.Loc.getMessage('SUCCESS_SAVED')}}</div>
      <p class="tac small">{{this.$Bitrix.Loc.getMessage('SUCCESS_SAVED_DOP')}}</p>
      <button @click="$emit('closeModal');" class="btn-blue p13-24">{{this.$Bitrix.Loc.getMessage('OK_BTN')}}</button>
    </div>
  </div>
  `,
};
