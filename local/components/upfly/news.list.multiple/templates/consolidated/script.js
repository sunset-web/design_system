// ? code refactoring
const preUpdateList = function (link) {
	let data = getAllUrlParams();
	data.signedParameters = BX.ComponentEx.signedParameters;
	data.componentName = BX.ComponentEx.componentName;
	data.templateName = BX.ComponentEx.templateName;
	setPreloader($("#ajaxUpdateList").closest("table").parent());
	BX.ajax({
		/* Куда отправить запрос */
		url: link ? link : window.location.href,
		method: "post",
		dataType: "html",
		data: data,
		onsuccess: function (data) {
			// обновление списка
			if ($("#ajaxUpdateList")) {
				$("#ajaxUpdateList").get(0).outerHTML = $(data)
					.find("#ajaxUpdateList")
					.get(0).outerHTML;
			}
			// обновление суммы
			if ($(".top-panel__total")) {
				$(".top-panel__total").get(0).outerHTML = $(data)
					.find(".top-panel__total")
					.get(0).outerHTML;
			}
			// обновление пагинации
			if ($("#ajaxUpdatePagenavigation")) {
				$("#ajaxUpdatePagenavigation").get(0).outerHTML = $(data)
					.find("#ajaxUpdatePagenavigation")
					.get(0).outerHTML;
			}
			// добавление подписи если ничего не найдено
			if ($(".filter-not-found")) {
				$(".filter-not-found").get(0).outerHTML = $(data)
					.find(".filter-not-found")
					.get(0).outerHTML;
			}
			// обновление чекбоксов
			checkCurCheckALLLLL();
			// убрать задизейбл на сортировке
			$("thead .disable").removeClass("disable");
			// убрать задизейбл на тарифах
			$(".__select-checkbox-tariff-js .__select__content-big")
				.find("label")
				.removeClass("disable");

			removePreloader($("#ajaxUpdateList").closest("table").parent());
		},
		onfailure: (e) => {
			console.error(e);
		},
	});
};
const updateListDeb = debounce(preUpdateList, 100);
//// BX.ComponentEx.ajaxPath

function setCompFilter(filter_company) {
	const url = new URL(window.location);
	url.searchParams.set("filter_company", filter_company);
	window.history.pushState({}, "", url);
	updateListDeb(BX.ComponentEx.ajaxPath);
}
function setDateFilter(filter_date) {
	const url = new URL(window.location);
	url.searchParams.set("filter_date", filter_date);
	window.history.pushState({}, "", url);
	updateListDeb(BX.ComponentEx.ajaxPath);
}

$(document).on(
	"click",
	".companies_filter-js .__filter .custom-checkbox",
	function (e) {
		let inputs = [...$(this).closest(".__filter").find("input:checked")];
		let arrComp = [];
		inputs.forEach((inp) => {
			arrComp.push(inp.value);
		});
		setCompFilter(arrComp.join("_"));
	}
);
$(document).on(
	"click",
	".date-year_filter-js .__select__content .__select__label",
	function (e) {
		$(".date-year_filter-js").data("date", $(this).prev().val());
		let filter =
			"01." +
			$(".date-month_filter-js").data("date") +
			"." +
			$(".date-year_filter-js").data("date");

		setDateFilter(filter);
	}
);
$(document).on(
	"click",
	".date-month_filter-js .__select__content .__select__label",
	function (e) {
		$(".date-month_filter-js").data("date", $(this).prev().val());
		let filter =
			"01." +
			$(".date-month_filter-js").data("date") +
			"." +
			$(".date-year_filter-js").data("date");
		setDateFilter(filter);
	}
);

// фильтры при загрузке
$(document).ready(function () {
	PreloadSelectCheckboxes(document.querySelector(".companies_filter-js"), true);
});
