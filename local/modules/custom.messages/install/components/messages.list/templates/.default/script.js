document.addEventListener("DOMContentLoaded", () => {
	// удаление элемента
	const deleteBtn = document.querySelector('.delete_message--js');

	deleteBtn.addEventListener('click', function (e) {
		BX.ajax.runComponentAction('custom:messages.messages.list', 'delete', {
			mode: 'class',
			data: {
				param: this.dataset.param,
			}
		}).then(function (response) {
			location.reload();
		});
	});
	// Изменение элемента
	const changeBtn = document.querySelector('.change_message--js');
	const sendBtn = document.querySelector('.send_message--js');

	changeBtn.addEventListener('click', function (e) {
		this.classList.add('hidden');
		this.closest('div').querySelector('.textarea_send--js').classList.remove('hidden');
	});
	sendBtn.addEventListener('click', function (e) {
		let val = this.closest('.textarea_send--js').querySelector('textarea').value;
		BX.ajax.runComponentAction('custom:messages.messages.list', 'update', {
			mode: 'class',
			data: {
				param: this.dataset.param,
				text: val,
			}
		}).then(function (response) {
			location.reload();
		});
	});
});