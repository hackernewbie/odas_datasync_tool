@yield('css')

<!-- Bootstrap Css -->
<link href="{{ URL::asset('/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />


<style type="text/css">
    .alert-error {
        color: #924040 !important;
        background-color: #fde1e1 !important;
        border-color: #fcd2d2 !important;
     }
        .alert-error .alert-link {
        color: #753333 !important;
    }
</style>
