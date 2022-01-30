(function ($) {
	$(function () {
		const $selectAll = $("#whp_select_all");

		$("#whp_hide_on_cpt_tax").select2({
			multiple: true,
			width: "100%",
			placeholder: whpPlugin.selectTaxonomyLabel,
		});

		if ($selectAll.length == 0) {
			return;
		}

		const totalChecked = $(
			"input[type=checkbox][id^=whp_hide_]:checked"
		).length;
		const totalOptions = $("input[type=checkbox][id^=whp_hide_]").length;

		if (totalChecked === totalOptions) {
			$selectAll.attr("checked", true);
		}

		const toggleAllOptions = function () {
			if ($(this).is(":checked")) {
				$("input[type=checkbox][id^=whp_hide_]").prop("checked", true);
			} else {
				$("input[type=checkbox][id^=whp_hide_]").prop("checked", false);
			}
		};

		$selectAll.on("change", toggleAllOptions);
	});
})(jQuery);
