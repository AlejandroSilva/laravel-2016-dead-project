<html>
<head>
    <title>Oportunidad - @yield('title')</title>
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

    {{--<link rel='stylesheet' href='/vendor/react-widgets/react-widgets.min.css'>--}}
    {{--<link rel='stylesheet' href='/css/styles.css'>--}}
    <style>
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
    @yield('hmtl-body')

    <script src="/bundle.js"></script>
    <script src="/vendor/jquery/jquery-2.2.1.min.js"></script>
    <script src="/vendor/bootstrap/bootstrap.min.js"></script>
</body>
</html>
{{-- Styles en esta seccion hacen que se haga render mas rapido en la pagina --}}
<link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>
<link rel='stylesheet' href='/css/frontEnd.css'>