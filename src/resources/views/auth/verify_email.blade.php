<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify_email.css') }}">
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
        <form class="tfa__form" action="/email/verification-notification" method="post">
            @csrf
            <p class="tfa__form--txt">
                メールを再送する場合は下のボタンをクリックしてください
            </p>
            <div class="tfa__form--message">
                {{session('message') ?? '　'}}
            </div>
            <button class="tfa__form--btn" type="submit">
                再送する
            </button>
        </form>
    </main>

    <footer class="footer">
        <p class="footer__txt">
            Atte,inc.
        </p>
    </footer>
</body>

</html>
