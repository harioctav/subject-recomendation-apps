<!-- Core JS -->
<script src="{{ asset('assets/templates/src/js/codebase.app.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/lib/jquery.min.js') }}"></script>
<script src="{{ asset('assets/customs/javascripts/custom.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/templates/src/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>

<!-- Page JS Plugins -->
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<script src="{{ asset('assets/templates/src/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/ckeditor5-classic/build/ckeditor.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/plugins/chart.js/chart.umd.js') }}"></script>

<!-- Page JS Code -->
<script src="{{ asset('assets/templates/src/js/pages/be_tables_datatables.min.js') }}"></script>
<script src="{{ asset('assets/templates/src/js/pages/be_comp_charts.min.js') }}"></script>

<script>
  Codebase.helpersOnLoad([
    'js-flatpickr'
    , 'jq-datepicker'
    , 'jq-select2'
    , 'js-ckeditor5'
    , 'jq-easy-pie-chart'
  ])

</script>

@include('sweetalert::alert')
@stack('javascript')
@include('components.error-alert')
