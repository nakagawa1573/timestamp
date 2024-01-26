<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    <title>Atte</title>
</head>

<body>

    <header class="header">
        <h1 class="header__ttl">
            Atte
        </h1>
        @yield('menu')
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <p class="footer__txt">
            Atte,inc.
        </p>
    </footer>
</body>

</html>
