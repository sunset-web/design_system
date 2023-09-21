export const SuccessModalDelete = {
	/*html*/
	template: `
  <div @click="$emit('closeModalSuccess');" class="overlay overlay-js active"></div>
  <div @click="$emit('closeModalSuccess');" class="overlay overlay-desktop overlay-desktop-js active"></div>
  <div class="modal-loading-result flex fd-column ai-center ajax-success active">
    <div class="modal-loading-result__title tac">Успешно</div>
    <p class="tac small">Удаление завершено</p>
    <button @click="$emit('closeModalSuccess');" class="btn-blue p13-24 js-modal-close">Окей</button>
  </div>
`,
};
