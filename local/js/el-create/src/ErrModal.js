export const ErrModal = {
	props: ["ERR"],
	/*html*/
	template: `
  <div @click="$emit('closeModalErr');" class="overlay overlay-js active"></div>
  <div @click="$emit('closeModalErr');" class="overlay overlay-desktop overlay-desktop-js active"></div>
  <div class="modal-loading-result flex fd-column ai-center ajax-error active" style="height: fit-content;">
    <h3 class="modal-loading-result__title tac">Ошибка</h3>
    <p class="tac small" style="text-align:left">
      <template v-for="field in ERR">
        Заполните поле: {{field.NAME}} <br>
      </template>
    </p>
    <button @click="$emit('closeModalErr');" class="btn-red p13-24">Окей</button>
  </div>
`,
};
