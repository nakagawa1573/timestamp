<style>
    .tfa__txt {
        text-align: center;
        margin-top: 80px;
        margin-bottom: 40px;
        font-size: 24px;
    }
</style>

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
    </header>

    <main>
        <p class="tfa__txt">
            登録メールアドレスに確認用メールを送信しました
        </p>
    </main>

    <footer class="footer">
        <p class="footer__txt">
            Atte,inc.
        </p>
    </footer>
</body>

</html>
