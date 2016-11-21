<!doctype html>
<html lang="en" manifest="/manifest.appcache">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    {{-- STYLES --}}
    <link rel='stylesheet' href='/vendor/bootstrap/bootstrap.min.css'>


    {{-- JS --}}
    <script>
        function updateIndicator() {
            var indicator = document.getElementById('indicator');
            indicator.textContent = navigator.onLine ? 'online' : 'offline';
        }
    </script>
</head>
<body onload="updateIndicator()">
    <h1>wom offline</h1>

    <div class="panel">
        <div class="panel-body">
            <p>Internet está <span id="indicator">(estado)</span>
        </div>
        <div class="jumbotron">
            <h1>Hello, world!</h1>
            <h2>asdx</h2>
            <p>This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
            <p><a href="#" class="btn btn-primary btn-lg" role="button">Learn more</a></p>
        </div>
    </div>

    {{--https://developer.mozilla.org/en-US/docs/Web/HTML/Using_the_application_cache--}}
</body>
</html>