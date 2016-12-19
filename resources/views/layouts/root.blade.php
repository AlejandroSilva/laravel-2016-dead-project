<html lang="es">
<head>
    <title>SIG - @yield('title', 'Sistema Información Gerencial')</title>
    {{--<title>DEV BRANCH</title>--}}
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <meta name="viewport" content="width=device-width, user-scalable=no">

    {{-- demasiado css en la plantilla, pero a estas altiras da lo mismo la optimizacion... --}}
    <style>
        {{-- ABOVE THE FOLD --}}
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
        .navbar-right { margin-right: -15px; float: right !important; }
        .navbar-right .dropdown-menu { right: 0px; left: auto; }
        .container { padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; width: 1170px; }
        .row { margin-right: -15px; margin-left: -15px; }
        .panel { margin-bottom: 20px; background-color: rgb(255, 255, 255); border: 1px solid transparent; border-radius: 4px; box-shadow: rgba(0, 0, 0, 0.0470588) 0px 1px 1px; }
        .panel-primary { border-color: rgb(51, 122, 183); }
        .panel-heading { padding: 10px 15px; border-bottom: 1px solid transparent; border-top-left-radius: 3px; border-top-right-radius: 3px; }
        .panel-primary > .panel-heading { color: rgb(255, 255, 255); background-color: rgb(51, 122, 183); border-color: rgb(51, 122, 183); }
        .glyphicon { position: relative; top: 1px; display: inline-block; font-family: "Glyphicons Halflings"; font-style: normal; font-weight: 400; line-height: 1; -webkit-font-smoothing: antialiased; }
        b, strong { font-weight: 700; }

        .btn { display: inline-block; padding: 6px 12px; margin-bottom: 0px; font-size: 14px; font-weight: 400; line-height: 1.42857; text-align: center; white-space: nowrap; vertical-align: middle; touch-action: manipulation; cursor: pointer; user-select: none; background-image: none; border: 1px solid transparent; border-radius: 4px; }
        .btn-default { color: rgb(51, 51, 51); background-color: rgb(255, 255, 255); border-color: rgb(204, 204, 204); }
        .btn-group-xs > .btn, .btn-xs { padding: 1px 5px; font-size: 12px; line-height: 1.5; border-radius: 3px; }
        .btn-primary { color: rgb(255, 255, 255); background-color: rgb(51, 122, 183); border-color: rgb(46, 109, 164); }
        .btn-block { display: block; width: 100%; }

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
    <script async src="/vendor/zinga-scroller/ZingaAnimateScroller.min.js"></script>
    {{--<script src="/vendor/jquery/jquery-2.2.1.min.js"></script>--}}
    {{--<script src="/vendor/jquery/jquery-2.1.1.min-xs.js"></script>--}}
    {{--<script src="/vendor/bootstrap/bootstrap.min-xs.js"></script>--}}
    <script async src="/app/bundle.js"></script>
</body>
</html>
{{-- Styles en esta seccion hacen que se haga render mas rapido en la pagina --}}
<link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>
<link rel='stylesheet' href='/vendor/react-widgets/css/react-widgets.min.css'>
<link rel='stylesheet' href='/vendor/fixed-data-table/fixed-data-table.min.css'>
<link rel='stylesheet' href='/vendor/react-virtualized/styles.css'>
<link rel='stylesheet' href='/app/frontEnd.css'>