<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 5 CSS -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaF1cf8GzJQ8b90LyPa5GmHB2pDkklHU6yoR8bFbrK9voFlr4h5ad5L5bfm" crossorigin="anonymous">


<!-- Optional: jQuery (si tienes dependencias que aún lo requieren) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Bootstrap 5 JS (con Popper integrado) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQp3PE8Z2AHp7zYE1cEV4pMl5hlFLXWPCmGmFpF3X2KRpGPs8x" crossorigin="anonymous"></script>

<!-- Reemplazar AdminLTE App si deseas mantener sus estilos, pero recuerda que AdminLTE 3 depende de Bootstrap 4 y puede no ser completamente compatible con Bootstrap 5 -->
<!-- Si decides omitir AdminLTE, omite esta línea -->
<script src="{{ asset('vendor/crudbooster/assets/adminlte/dist/js/app.js') }}" type="text/javascript"></script>

<!-- Reemplaza los componentes específicos de AdminLTE según sea necesario -->

<!-- Datepicker (puede necesitar actualización si usas una versión basada en Bootstrap 3/4) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>

<!-- Daterangepicker actualizado -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- Timepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>

<!-- Lightbox -->
<link rel="stylesheet" href="{{ asset('vendor/crudbooster/assets/lightbox/dist/css/lightbox.min.css') }}">
<script src="{{ asset('vendor/crudbooster/assets/lightbox/dist/js/lightbox.min.js') }}"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Money Format -->
<script src="{{ asset('vendor/crudbooster/jquery.price_format.2.0.min.js') }}"></script>

<!-- DataTable actualizado -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    var ASSET_URL = "{{ asset('/') }}";
    var APP_NAME = "{{ Session::get('appname') }}";
    var ADMIN_PATH = '{{ url(config("crudbooster.ADMIN_PATH")) }}';
    var NOTIFICATION_JSON = "{{ route('NotificationsControllerGetLatestJson') }}";
    var NOTIFICATION_INDEX = "{{ route('NotificationsControllerGetIndex') }}";

    var NOTIFICATION_YOU_HAVE = "{{ cbLang('notification_you_have') }}";
    var NOTIFICATION_NOTIFICATIONS = "{{ cbLang('notification_notification') }}";
    var NOTIFICATION_NEW = "{{ cbLang('notification_new') }}";

    $(document).ready(function () {
        $('.datatables-simple').DataTable();
    });
</script>
<script src="{{ asset('vendor/crudbooster/assets/js/main.js') . '?r=' . time() }}"></script>

