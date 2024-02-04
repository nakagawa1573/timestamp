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

        <nav class="header__nav">
            <ul class="header__nav-list">
                @if (Auth::check())
                    <li class="header__nav-list__item">
                        <a class="header__nav-list__item-link" href="/">
                            ホーム
                        </a>
                    </li>
                    <li class="header__nav-list__item">
                        <a class="header__nav-list__item-link" href="/attendance">
                            日付一覧
                        </a>
                    </li>
                    <li class="header__nav-list__item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header__nav-list__item-btn">
                                ログアウト
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </nav>
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
