document.addEventListener("DOMContentLoaded", () => {
	// добавление элемента
	const addBtn = document.querySelector('.add_message--js');

	addBtn.addEventListener('click', function (e) {
		let val = this.closest('div').querySelector('textarea').value;
		BX.ajax.runComponentAction('custom:messages.messages.add', 'add', {
			mode: 'class',
			data: {
				param: this.dataset.param,
				text: val,
			}
		}).then(function (response) {
			console.log(response);
			location.reload();
		});
	});
});