export const DeleteModal = {
	name: "DeleteModal",
	/*html*/
	template: `
<div @click="$emit('closeModal');" class="overlay overlay-js active"></div>
<div @click="$emit('closeModal');" class="overlay overlay-desktop overlay-desktop-js active"></div>
<div class="modal-window flex fd-column ai-center delete-modal-js active">
   <div class="modal-delete-confirm__title tac">{{this.$Bitrix.Loc.getMessage('MODAL_DELETE_TITTLE')}}</div>
   <p class="tac small">{{this.$Bitrix.Loc.getMessage('MODAL_DELETE_TEXT')}}</p>
   <div class="modal-window__button-group flex fd-column-s">
      <button @click="$emit('deleteClick');" class="btn-red p13-24">{{this.$Bitrix.Loc.getMessage('DELETE_BTN')}}</button>
      <button @click="$emit('closeModal');" class="btn-white p13-24">{{this.$Bitrix.Loc.getMessage('CANCEL')}}</button>
   </div>
</div>
  `,
};
