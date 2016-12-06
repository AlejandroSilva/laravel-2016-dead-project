<html lang="es">
<head>
    <title>SIG - @yield('title', 'Sistema Información Gerencial')</title>
    {{--<title>DEV BRANCH</title>--}}
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <meta name="viewport" content="width=device-width, user-scalable=no">

    {{-- demasiado css en la plantilla, pero a estas altiras da lo mismo la optimizacion... --}}
    <style>
        {{-- Above the fold --}}
        * { box-sizing: border-box; }
        html { font-family: sans-serif; text-size-adjust: 100%; font-size: 10px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); }
        body { padding-top: 70px; margin: 0px; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.42857; color: rgb(51, 51, 51); background-color: rgb(255, 255, 255); }
        article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary { display: block; }
        .navbar { position: relative; min-height: 50px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .navbar-fixed-bottom, .navbar-fixed-top { position: fixed; right: 0px; left: 0px; z-index: 1030; border-radius: 0px; }
        .navbar-fixed-top { top: 0px; border-width: 0px 0px 1px; }
        .navbar-default { background-color: rgb(248, 248, 248); border-color: rgb(231, 231, 231); }
        .container-fluid { padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }
        .navbar-header { float: left; }
        .container-fluid > .navbar-collapse, .container-fluid > .navbar-header, .container > .navbar-collapse, .container > .navbar-header { margin-right: 0px; margin-left: 0px; }
        button, input, optgroup, select, textarea { margin: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; color: inherit; }
        button { overflow: visible; }
        button, select { text-transform: none; }
        button, html input[type="button"], input[type="reset"], input[type="submit"] { -webkit-appearance: button; cursor: pointer; }
        button, input, select, textarea { font-family: inherit; font-size: inherit; line-height: inherit; }
        .navbar-toggle { position: relative; float: right; padding: 9px 10px; margin-top: 8px; margin-right: 15px; margin-bottom: 8px; background-color: transparent; background-image: none; border: 1px solid transparent; border-radius: 4px; display: none; }
        .navbar-default .navbar-toggle { border-color: rgb(221, 221, 221); }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0px; margin: -1px; overflow: hidden; clip: rect(0px 0px 0px 0px); border: 0px; }
        .navbar-toggle .icon-bar { display: block; width: 22px; height: 2px; border-radius: 1px; }
        .navbar-default .navbar-toggle .icon-bar { background-color: rgb(136, 136, 136); }
        .navbar-toggle .icon-bar + .icon-bar { margin-top: 4px; }
        a { background-color: transparent; color: rgb(51, 122, 183); text-decoration: none; }
        .navbar-brand { float: left; height: 50px; padding: 15px; font-size: 18px; line-height: 20px; }
        .navbar-default .navbar-brand { color: rgb(119, 119, 119); }
        .navbar > .container .navbar-brand, .navbar > .container-fluid .navbar-brand { margin-left: -15px; }
        .collapse { display: none; }
        .navbar-collapse { padding-right: 15px; padding-left: 15px; overflow-x: visible; border-top: 0px; box-shadow: none; width: auto; }
        .navbar-collapse.collapse { padding-bottom: 0px; display: block !important; height: auto !important; overflow: visible !important; }
        .navbar-fixed-bottom .navbar-collapse, .navbar-fixed-top .navbar-collapse, .navbar-static-top .navbar-collapse { padding-right: 0px; padding-left: 0px; }
        .navbar-fixed-bottom .navbar-collapse, .navbar-fixed-top .navbar-collapse { max-height: 340px; }
        .navbar-default .navbar-collapse, .navbar-default .navbar-form { border-color: rgb(231, 231, 231); }
        ol, ul { margin-top: 0px; margin-bottom: 10px; }
        .nav { padding-left: 0px; margin-bottom: 0px; list-style: none; }
        .navbar-nav { margin: 0px; float: left; }
        .dropdown, .dropup { position: relative; }
        .nav > li { position: relative; display: block; }
        .navbar-nav > li { float: left; }
        .nav > li > a { position: relative; display: block; padding: 10px 15px; }
        .navbar-nav > li > a { padding-top: 15px; padding-bottom: 15px; line-height: 20px; }
        .navbar-default .navbar-nav > li > a { color: rgb(119, 119, 119); }
        .caret { display: inline-block; width: 0px; height: 0px; margin-left: 2px; vertical-align: middle; border-top: 4px dashed; border-right: 4px solid transparent; border-left: 4px solid transparent; }
        ol ol, ol ul, ul ol, ul ul { margin-bottom: 0px; }
        .dropdown-menu { position: absolute; top: 100%; left: 0px; z-index: 1000; display: none; float: left; min-width: 160px; padding: 5px 0px; margin: 2px 0px 0px; font-size: 14px; text-align: left; list-style: none; background-color: rgb(255, 255, 255); -webkit-background-clip: padding-box; background-clip: padding-box; border: 1px solid rgba(0, 0, 0, 0.14902); border-radius: 4px; box-shadow: rgba(0, 0, 0, 0.172549) 0px 6px 12px; }
        .navbar-nav > li > .dropdown-menu { margin-top: 0px; border-top-left-radius: 0px; border-top-right-radius: 0px; }
        .dropdown-header { display: block; padding: 3px 20px; font-size: 12px; line-height: 1.42857; color: rgb(119, 119, 119); white-space: nowrap; }
        .dropdown-menu > li > a { display: block; padding: 3px 20px; clear: both; font-weight: 400; line-height: 1.42857; color: rgb(51, 51, 51); white-space: nowrap; }
        .dropdown-menu .divider { height: 1px; margin: 9px 0px; overflow: hidden; background-color: rgb(229, 229, 229); }
        .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover { color: rgb(255, 255, 255); text-decoration: none; background-color: rgb(51, 122, 183); outline: 0px; }
        .navbar-right { margin-right: -15px; float: right !important; }
        .navbar-right .dropdown-menu { right: 0px; left: auto; }
        .container { padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; width: 1170px; }
        .row { margin-right: -15px; margin-left: -15px; }
        .page-header { padding-bottom: 9px; margin-right: 0px; margin-bottom: 20px; margin-left: 0px; border-bottom: 1px solid rgb(238, 238, 238); margin-top: 0px !important; margin: 40px 0px 20px; }
        .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 { position: relative; min-height: 1px; padding-right: 15px; padding-left: 15px; }
        .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 { float: left; }
        .col-sm-10 { width: 83.3333%; }
        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 { font-family: inherit; font-weight: 500; line-height: 1.1; color: inherit; }
        .h1, .h2, .h3, h1, h2, h3 { margin-top: 20px; margin-bottom: 10px; }
        .h2, h2 { font-size: 30px; }
        .page-header h2 { margin-bottom: 0px; }
        .col-sm-2 { width: 16.6667%; }
        .nav-header { margin-top: 20px; margin-bottom: 10px; }
        .nav-pills > li { float: left; }
        .btn { display: inline-block; padding: 6px 12px; margin-bottom: 0px; font-size: 14px; font-weight: 400; line-height: 1.42857; text-align: center; white-space: nowrap; vertical-align: middle; touch-action: manipulation; cursor: pointer; user-select: none; background-image: none; border: 1px solid transparent; border-radius: 4px; }
        .btn-group-xs > .btn, .btn-xs { padding: 1px 5px; font-size: 12px; line-height: 1.5; border-radius: 3px; }
        .btn-success { color: rgb(255, 255, 255); background-color: rgb(92, 184, 92); border-color: rgb(76, 174, 76); }
        .nav-pills > li > a { border-radius: 4px; }
        .nav-header > li > a { padding: 5px 15px !important; }
        .glyphicon { position: relative; top: 1px; display: inline-block; font-family: "Glyphicons Halflings"; font-style: normal; font-weight: 400; line-height: 1; -webkit-font-smoothing: antialiased; }
        .col-sm-3 { width: 25%; }
        .panel { margin-bottom: 20px; background-color: rgb(255, 255, 255); border: 1px solid transparent; border-radius: 4px; box-shadow: rgba(0, 0, 0, 0.0470588) 0px 1px 1px; }
        .panel-default { border-color: rgb(221, 221, 221); }
        .panel-heading { padding: 10px 15px; border-bottom: 1px solid transparent; border-top-left-radius: 3px; border-top-right-radius: 3px; }
        .panel-default > .panel-heading { color: rgb(51, 51, 51); background-color: rgb(245, 245, 245); border-color: rgb(221, 221, 221); }
        .panel-body { padding: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; max-width: 100%; margin-bottom: 5px; font-weight: 700; }
        input { line-height: normal; }
        .form-control { display: block; width: 100%; height: 34px; padding: 6px 12px; font-size: 14px; line-height: 1.42857; color: rgb(85, 85, 85); background-color: rgb(255, 255, 255); background-image: none; border: 1px solid rgb(204, 204, 204); border-radius: 4px; box-shadow: rgba(0, 0, 0, 0.0745098) 0px 1px 1px inset; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; }
        .input-group .form-control, .input-group-addon, .input-group-btn { display: table-cell; }
        .input-group-addon, .input-group-btn { width: 1%; white-space: nowrap; vertical-align: middle; }
        .input-group-btn { position: relative; font-size: 0px; white-space: nowrap; }
        .btn-primary { color: rgb(255, 255, 255); background-color: rgb(51, 122, 183); border-color: rgb(46, 109, 164); }
        .btn-block { display: block; width: 100%; }
        .btn-group-sm > .btn, .btn-sm { padding: 5px 10px; font-size: 12px; line-height: 1.5; border-radius: 3px; }
        .input-group-btn > .btn { position: relative; }
        input[type="button"].btn-block, input[type="reset"].btn-block, input[type="submit"].btn-block { width: 100%; }
        .input-group .form-control:last-child, .input-group-addon:last-child, .input-group-btn:first-child > .btn-group:not(:first-child) > .btn, .input-group-btn:first-child > .btn:not(:first-child), .input-group-btn:last-child > .btn, .input-group-btn:last-child > .btn-group > .btn, .input-group-btn:last-child > .dropdown-toggle { border-top-left-radius: 0px; border-bottom-left-radius: 0px; }
        .input-group-btn:last-child > .btn, .input-group-btn:last-child > .btn-group { z-index: 2; margin-left: -1px; }
        .col-sm-9 { width: 75%; }
        table { border-spacing: 0px; border-collapse: collapse; background-color: transparent; }
        .table { width: 100%; max-width: 100%; margin-bottom: 20px; }
        .table-bordered { border: 1px solid rgb(221, 221, 221); }
        .tabla-nominas { font-size: 12px; }
        .panel > .panel-collapse > .table, .panel > .table, .panel > .table-responsive > .table { margin-bottom: 0px; }
        .panel > .table-bordered, .panel > .table-responsive > .table-bordered { border: 0px; }
        .panel > .table-responsive:last-child > .table:last-child, .panel > .table:last-child { border-bottom-right-radius: 3px; border-bottom-left-radius: 3px; }
        td, th { padding: 0px; }
        th { text-align: left; }
        .table th { text-align: center; }
        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th { padding: 8px; line-height: 1.42857; vertical-align: top; border-top: 1px solid rgb(221, 221, 221); }
        .table > thead > tr > th { vertical-align: bottom; border-bottom: 2px solid rgb(221, 221, 221); }
        .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th { padding: 5px; }
        .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th { border: 1px solid rgb(221, 221, 221); }
        .table-bordered > thead > tr > td, .table-bordered > thead > tr > th { border-bottom-width: 2px; }
        .table > caption + thead > tr:first-child > td, .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > td, .table > thead:first-child > tr:first-child > th { border-top: 0px; }
        .panel > .table-bordered > tbody > tr > td:first-child, .panel > .table-bordered > tbody > tr > th:first-child, .panel > .table-bordered > tfoot > tr > td:first-child, .panel > .table-bordered > tfoot > tr > th:first-child, .panel > .table-bordered > thead > tr > td:first-child, .panel > .table-bordered > thead > tr > th:first-child, .panel > .table-responsive > .table-bordered > tbody > tr > td:first-child, .panel > .table-responsive > .table-bordered > tbody > tr > th:first-child, .panel > .table-responsive > .table-bordered > tfoot > tr > td:first-child, .panel > .table-responsive > .table-bordered > tfoot > tr > th:first-child, .panel > .table-responsive > .table-bordered > thead > tr > td:first-child, .panel > .table-responsive > .table-bordered > thead > tr > th:first-child { border-left: 0px; }
        .panel > .table-bordered > tbody > tr:first-child > td, .panel > .table-bordered > tbody > tr:first-child > th, .panel > .table-bordered > thead > tr:first-child > td, .panel > .table-bordered > thead > tr:first-child > th, .panel > .table-responsive > .table-bordered > tbody > tr:first-child > td, .panel > .table-responsive > .table-bordered > tbody > tr:first-child > th, .panel > .table-responsive > .table-bordered > thead > tr:first-child > td, .panel > .table-responsive > .table-bordered > thead > tr:first-child > th { border-bottom: 0px; }
        .panel > .table-bordered > tbody > tr > td:last-child, .panel > .table-bordered > tbody > tr > th:last-child, .panel > .table-bordered > tfoot > tr > td:last-child, .panel > .table-bordered > tfoot > tr > th:last-child, .panel > .table-bordered > thead > tr > td:last-child, .panel > .table-bordered > thead > tr > th:last-child, .panel > .table-responsive > .table-bordered > tbody > tr > td:last-child, .panel > .table-responsive > .table-bordered > tbody > tr > th:last-child, .panel > .table-responsive > .table-bordered > tfoot > tr > td:last-child, .panel > .table-responsive > .table-bordered > tfoot > tr > th:last-child, .panel > .table-responsive > .table-bordered > thead > tr > td:last-child, .panel > .table-responsive > .table-bordered > thead > tr > th:last-child { border-right: 0px; }
        .table > tbody { font-size: 14px; }
        .table td { text-align: center; }
        p { margin: 0px 0px 10px; }
        .btn-default { color: rgb(51, 51, 51); background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204); }
        .btn-100 { text-align: left !important; }




        {{-- Custom navbar --}}
        body {
            padding-top: 70px;
        }
        /* Sidebar navigation */
        a.active.collapsed,
        .nav-header > a.active,
        .nav-header > a:hover,
        .nav-header > a:focus {
            color: #fff;
            background-color: #428bca;
        }
        .nav-header > a:focus {
            color: #337ab7;
            text-decoration: underline;
            /* background-color: #eee; */
        }
        .nav-stacked > .active > a,
        .nav-stacked > .active > a:hover,
        .nav-stacked > .active > a:focus {
            color: #fff;
            background-color: #428bca;
        }
        .nav-header > .nav {
            padding-left: 10px;
            padding-right: 10px;
        }
        .nav-header > a:hover {
            color: #337ab7;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @include('layouts.menu.menu')

    @yield('body')

    {{-- Scroller para react-data-tables --}}
    <script src="/vendor/zinga-scroller/ZingaAnimateScroller.min.js"></script>
    <script src="/vendor/jquery/jquery-2.2.1.min.js"></script>
    <script src="/vendor/bootstrap/bootstrap.min.js"></script>
    <script async src="/app/bundle.js"></script>
</body>
</html>
{{-- Styles en esta seccion hacen que se haga render mas rapido en la pagina --}}
{{--<link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>--}}
{{--<link rel='stylesheet' href='/vendor/react-widgets/react-widgets.min.css'>--}}
{{--<link rel='stylesheet' href='/vendor/fixed-data-table/fixed-data-table.min.css'>--}}
<link rel='stylesheet' href='/app/frontEnd.css'>