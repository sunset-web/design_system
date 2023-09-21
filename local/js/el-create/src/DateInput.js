export const DateInput = {
	name: "DateInput",
	props: ["field", "value"],
	mounted() {
		$(this.$refs["dateInput" + this.field.CODE]).on("input", (e) => {
			this.$emit("input", e);
		});
		// КАЛЕНДАРЬ

		// svg иконок (неактивной и активной)
		const grayIcon =
			"data:image/svg+xml,%3Csvg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M14 0C13.45 0 13 0.45 13 1V2H5V1C5 0.45 4.55 0 4 0C3.45 0 3 0.45 3 1V2H2C0.89 2 0.00999999 2.9 0.00999999 4L0 18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2H15V1C15 0.45 14.55 0 14 0ZM16 18H2V8H16V18ZM8 11C8 10.45 8.45 10 9 10C9.55 10 10 10.45 10 11C10 11.55 9.55 12 9 12C8.45 12 8 11.55 8 11ZM4 11C4 10.45 4.45 10 5 10C5.55 10 6 10.45 6 11C6 11.55 5.55 12 5 12C4.45 12 4 11.55 4 11ZM12 11C12 10.45 12.45 10 13 10C13.55 10 14 10.45 14 11C14 11.55 13.55 12 13 12C12.45 12 12 11.55 12 11ZM8 15C8 14.45 8.45 14 9 14C9.55 14 10 14.45 10 15C10 15.55 9.55 16 9 16C8.45 16 8 15.55 8 15ZM4 15C4 14.45 4.45 14 5 14C5.55 14 6 14.45 6 15C6 15.55 5.55 16 5 16C4.45 16 4 15.55 4 15ZM12 15C12 14.45 12.45 14 13 14C13.55 14 14 14.45 14 15C14 15.55 13.55 16 13 16C12.45 16 12 15.55 12 15Z' fill='%238895BB'/%3E%3C/svg%3E%0A";
		const blackIcon =
			"data:image/svg+xml,%3Csvg width='18' height='20' viewBox='0 0 18 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M14 0C13.45 0 13 0.45 13 1V2H5V1C5 0.45 4.55 0 4 0C3.45 0 3 0.45 3 1V2H2C0.89 2 0.00999999 2.9 0.00999999 4L0 18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2H15V1C15 0.45 14.55 0 14 0ZM16 18H2V8H16V18ZM8 11C8 10.45 8.45 10 9 10C9.55 10 10 10.45 10 11C10 11.55 9.55 12 9 12C8.45 12 8 11.55 8 11ZM4 11C4 10.45 4.45 10 5 10C5.55 10 6 10.45 6 11C6 11.55 5.55 12 5 12C4.45 12 4 11.55 4 11ZM12 11C12 10.45 12.45 10 13 10C13.55 10 14 10.45 14 11C14 11.55 13.55 12 13 12C12.45 12 12 11.55 12 11ZM8 15C8 14.45 8.45 14 9 14C9.55 14 10 14.45 10 15C10 15.55 9.55 16 9 16C8.45 16 8 15.55 8 15ZM4 15C4 14.45 4.45 14 5 14C5.55 14 6 14.45 6 15C6 15.55 5.55 16 5 16C4.45 16 4 15.55 4 15ZM12 15C12 14.45 12.45 14 13 14C13.55 14 14 14.45 14 15C14 15.55 13.55 16 13 16C12.45 16 12 15.55 12 15Z' fill='%231B254A'/%3E%3C/svg%3E%0A";

		//настройки календаря
		$.datepicker.regional["ru"] = {
			showOn: "both",
			buttonImage: grayIcon,
			buttonImageOnly: true,
			buttonText: "Выбрать дату",
			closeText: "Закрыть",
			prevText: "Предыдущий месяц",
			nextText: "Следующий месяц",
			currentText: "Сегодня",
			monthNames: [
				"Январь",
				"Февраль",
				"Март",
				"Апрель",
				"Май",
				"Июнь",
				"Июль",
				"Август",
				"Сентябрь",
				"Октябрь",
				"Ноябрь",
				"Декабрь",
			],
			monthNamesShort: [
				"Янв",
				"Фев",
				"Мар",
				"Апр",
				"Май",
				"Июн",
				"Июл",
				"Авг",
				"Сен",
				"Окт",
				"Ноя",
				"Дек",
			],
			dayNames: [
				"воскресенье",
				"понедельник",
				"вторник",
				"среда",
				"четверг",
				"пятница",
				"суббота",
			],
			dayNamesShort: ["вск", "пнд", "втр", "срд", "чтв", "птн", "сбт"],
			dayNamesMin: ["ВС", "ПН", "ВТ", "СР", "ЧТ", "ПТ", "СБ"],
			weekHeader: "Не",
			dateFormat: "dd.mm.yy",
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: "",
			showOtherMonths: true,
			showAnim: "",
			beforeShow: function (textbox, instance) {
				$(textbox).trigger("click");
				instance.dpDiv.css("padding", `24px`);

				// центрирование или
				// сдвиг к правому краю текстового поля
				// в зависимости от разрешения экрана
				const width = window.innerWidth;
				if (width < 768) {
					$(".overlay-datepicker--js").addClass("active");
					instance.dpDiv.css("padding", `16px`);
					instance.dpDiv.css("left", `17px`);
					instance.dpDiv.css("margin-left", "0");
					$(".overlay-js").addClass("active");
				} else if (width < 1200) {
					if (!$(textbox).hasClass("datepicker-small--js")) {
						instance.dpDiv.css("margin-left", "-44px");
					}
					if ($(textbox).hasClass("datepicker-big--js")) {
						instance.dpDiv.css("margin-left", "192px");
					}
				} else {
					if ($(textbox).hasClass("datepicker-big--js")) {
						instance.dpDiv.css("margin-left", "288px");
					}
				}

				// перекрытие шапки
				setTimeout(function () {
					instance.dpDiv.css("z-index", "7");
				}, 0);

				// смена иконки на черную при открытии
				$(this).next(".ui-datepicker-trigger").attr("src", blackIcon);
			},
			onSelect: function (date, instance) {
				$(this).trigger("input");

				$(".overlay-datepicker--js").removeClass("active");

				if ($(this).hasClass("date-input-start--js")) {
					const selectedDay = instance.selectedDay;
					const selectedMonth = instance.selectedMonth;
					const selectedYear = instance.selectedYear;

					const startDate = new Date(selectedYear, selectedMonth, selectedDay);
					$(".date-input-end--js").datepicker("option", "minDate", startDate);
				}
			},
			onClose: function () {
				// смена иконки на серую при закрытии
				$(this).next(".ui-datepicker-trigger").attr("src", grayIcon);
				const width = window.innerWidth;
				if (width <= 768) {
					$(".overlay-js").removeClass("active");
				}
			},
		};
		$.datepicker.setDefaults($.datepicker.regional["ru"]);
		$(".datepicker--js").datepicker();
		const startDateInput = $(".date-input-start--js").val();
		if (startDateInput) {
			const date = startDateInput.split(".");
			const [day, month, year] = date;
			const startDate = new Date(year, month - 1, day);
			$(".date-input-end--js").datepicker("destroy");
			$(".date-input-end--js").datepicker({ minDate: startDate });
		}
		$(document).on("click", ".overlay-datepicker--js", function () {
			$(this).removeClass("active");
		});

		const maskOptions = {
			mask: Date,
			pattern: "`d.`m.Y",
			blocks: {
				d: {
					mask: IMask.MaskedRange,
					from: 1,
					to: 31,
					maxLength: 2,
				},
				m: {
					mask: IMask.MaskedRange,
					from: 1,
					to: 12,
					maxLength: 2,
				},
				Y: {
					mask: IMask.MaskedRange,
					from: 1900,
					to: 9999,
				},
			},
			autofix: true,
		};

		$(".date-input--js").each(function () {
			IMask($(this)[0], maskOptions);
		});
	},
	methods: {
		updateDate(e) {
			this.$emit("input", e.target.value);
		},
	},
	/*html*/
	template: `
  <div class="form-input-field-group form-datepicker-group form-input-field-group--js">
    <input autocomplete="off" :value="value" @input="updateDate" :ref="'dateInput' + field.CODE" type="text" :placeholder="field.NAME + (field.REQUIRED?' *':'')" :id="field.CODE" :name="field.CODE" :data-name="field.CODE" class="date-input--js date-input-start--js datepicker--js" maxlength="10">
    <label :for="field.CODE" v-html="field.NAME + (field.REQUIRED?' *':'')"></label>
    <div class="help-icon help-icon_small help-icon--js">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M4.15613 1.75062C5.58992 0.792596 7.2756 0.28125 9 0.28125C11.3124 0.28125 13.53 1.19983 15.1651 2.83491C16.8002 4.47 17.7188 6.68764 17.7188 9C17.7188 10.7244 17.2074 12.4101 16.2494 13.8439C15.2914 15.2777 13.9297 16.3952 12.3365 17.0551C10.7434 17.715 8.99033 17.8876 7.29906 17.5512C5.60779 17.2148 4.05426 16.3844 2.83492 15.1651C1.61558 13.9457 0.785197 12.3922 0.448782 10.7009C0.112367 9.00967 0.285028 7.25662 0.944929 5.66348C1.60483 4.07034 2.72233 2.70865 4.15613 1.75062ZM9 1.21875C7.46102 1.21875 5.95659 1.67511 4.67697 2.53013C3.39735 3.38514 2.40001 4.60041 1.81107 6.02225C1.22212 7.44408 1.06803 9.00863 1.36827 10.518C1.66851 12.0275 2.4096 13.4139 3.49783 14.5022C4.58606 15.5904 5.97254 16.3315 7.48196 16.6317C8.99137 16.932 10.5559 16.7779 11.9778 16.1889C13.3996 15.6 14.6149 14.6027 15.4699 13.323C16.3249 12.0434 16.7813 10.539 16.7813 9C16.7813 6.93628 15.9614 4.95709 14.5022 3.49783C13.0429 2.03856 11.0637 1.21875 9 1.21875Z" />
          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0382 4.99126C9.86307 4.90654 9.4873 4.82255 9.00736 4.82836C8.49868 4.83531 7.99618 4.94728 7.59306 5.27762M10.0382 4.99126C10.4905 5.21163 11.1498 5.64489 11.1498 6.51584C11.1498 7.47363 10.5921 7.90351 9.63129 8.56045C9.11013 8.91677 8.7437 9.29654 8.51271 9.72347C8.27968 10.1542 8.20798 10.5896 8.20798 11.0156C8.20798 11.3263 8.46127 11.5781 8.77371 11.5781C9.08616 11.5781 9.33944 11.3263 9.33944 11.0156C9.33944 10.7206 9.38768 10.4809 9.50915 10.2564C9.63266 10.0281 9.8553 9.77257 10.2724 9.48739L10.2941 9.47252C11.2053 8.84965 12.2812 8.11414 12.2812 6.51584C12.2812 4.94597 11.0509 4.23178 10.5353 3.98071L10.5349 3.98049L10.5344 3.98026C10.1431 3.79059 9.57216 3.69643 8.99306 3.7035L8.99267 3.7035L8.99228 3.70351C8.34078 3.71235 7.54847 3.85633 6.8734 4.40955L6.87338 4.40956C6.33015 4.85477 6.0412 5.36035 5.88923 5.75974C5.81354 5.95865 5.77164 6.13149 5.74841 6.25875C5.73677 6.3225 5.72973 6.37526 5.72549 6.41467C5.72337 6.43439 5.72194 6.45082 5.72099 6.46368C5.72051 6.47011 5.72014 6.47565 5.71987 6.48026C5.71974 6.48257 5.71962 6.48465 5.71953 6.48649L5.7194 6.48908L5.71935 6.49024L5.71932 6.49079C5.71931 6.49105 5.7193 6.49132 6.28449 6.51584L5.7193 6.49132C5.70568 6.80167 5.94768 7.06424 6.25983 7.07778C6.56998 7.09123 6.83269 6.85377 6.84939 6.5463C6.84952 6.5447 6.84986 6.54066 6.85054 6.53434C6.85207 6.52009 6.85531 6.49452 6.86168 6.45964C6.87446 6.38963 6.89949 6.28393 6.94749 6.1578C7.04288 5.90712 7.22829 5.57658 7.59305 5.27763" />
          <path d="M8.71875 14.1562C9.18474 14.1562 9.5625 13.7785 9.5625 13.3125C9.5625 12.8465 9.18474 12.4688 8.71875 12.4688C8.25276 12.4688 7.875 12.8465 7.875 13.3125C7.875 13.7785 8.25276 14.1562 8.71875 14.1562Z" />
        </svg>
    </div>
    <div class="help-info help-info_small small help-info--js">
      {{this.$Bitrix.Loc.getMessage('DATE_INFO')}}
    </div>
  </div>
`,
};
