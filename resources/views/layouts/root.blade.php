<html>
<head>
    <title>SIG - @yield('title')</title>
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

    {{-- Styles --}}
    <link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>
    <link rel='stylesheet' href='/vendor/react-widgets/react-widgets.min.css'>
    <link rel='stylesheet' href='/css/styles.css'>
    <link rel='stylesheet' href='/css/frontEnd.css'>
</head>
<body>
    @yield('hmtl-body')

    <script src="/bundle.js"></script>
    <script src="/vendor/jquery/jquery-2.2.1.min.js"></script>
    <script src="/vendor/bootstrap/bootstrap.min.js"></script>
</body>
</html>