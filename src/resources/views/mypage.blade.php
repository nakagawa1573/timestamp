@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <section class="content">
        <article class="profile">
            <div>
                <p class="profile__name">
                    {{ $user->name }}
                </p>
                <p class="profile__day">
                    登録日：{{ $user->created_at->format('Y-m-d') }}
                </p>
                <p class="profile__email">
                    メールアドレス：{{ $user->email }}
                </p>
            </div>
            <form class="btn__works" action="/attendance/user" method="get">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <button class="btn__works--btn">
                    個人勤怠表
                </button>
            </form>
        </article>

        <article class="tfa">
            @if (session('status') === 'two-factor-authentication-enabled')
                <div class="tfa__alert">
                    ２要素認証は有効になりました
                </div>
            @elseif(session('status') === 'two-factor-authentication-disabled')
                <div class="fra__alert">
                    2要素認証は無効になっています
                </div>
            @else
                <div class="fra__alert">
                    &nbsp;
                </div>
            @endif
            <form class="tfa__form" action="/user/two-factor-authentication" method="post">
                @csrf
                @if (auth()->user()->two_factor_secret)
                    @method('DELETE')
                    <button class="tfa__disable">
                        無効化
                    </button>
                    <div>
                        <div class="pb-5">
                            {!! str_replace('<svg', '<svg width="100%" height="100%"', auth()->user()->twoFactorQrCodeSvg()) !!}
                        </div>
                        <div class="tfa__list--group">
                            <div class="tfa__list--ttl">
                                リカバリーコード
                            </div>
                            <ul class="tfa__list--recovery">
                                @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                                    <li class="code">{{ $code }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <button class="tfa__enable">
                        有効化
                    </button>
                @endif
            </form>
        </article>
    </section>
@endsection
