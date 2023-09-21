export const SuccessModal = {
	/*html*/
	template: `
  <div style="position:relative; z-index:200;">
    <div @click="$emit('closeModal');" class="overlay overlay-js active"></div>
    <div @click="$emit('closeModal');" class="overlay overlay-desktop overlay-desktop-js active"></div>
    <div class="modal-loading-result flex fd-column ai-center ajax-success active">
      <div class="modal-loading-result__title tac">Успешно</div>
      <p class="tac small">Все изменения успешно сохранены</p>
      <button @click="$emit('closeModal');" class="btn-blue p13-24 js-modal-close">Окей</button>
    </div>
  </div>
`,
};
