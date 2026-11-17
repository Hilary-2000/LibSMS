$(document).ready(function() {
	// Tables that are paginated on the server (data-server-paginated="1") must not be paginated,
	// searched or sorted again in the browser: those would only act on the current page.
	var serverPaged = { paging: false, searching: false, info: false, ordering: false };
	function opts(sel) { return $(sel).is("[data-server-paginated]") ? serverPaged : {}; }

	// table one
	$("#datatable").each(function () { $(this).DataTable(opts(this)); });
	// table two
	$("#datatable_check_out").each(function () { $(this).DataTable(opts(this)); });

	$("#datatable-buttons").DataTable({
		lengthChange: !1,
		buttons: ["copy", "excel", "pdf", "colvis"]
	}).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");
	$(".dataTables_length select").addClass("form-select form-select-sm");
});
