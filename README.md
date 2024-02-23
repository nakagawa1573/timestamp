# timestamp
### 勤務時間を記録し、確認することのできるアプリ
![top](https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/top.png)


## 作成した目的
模擬案件を通して実践に近い開発経験を積むために作成

## アプリケーションURL
- 本番環境：http://54.250.249.198/
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/

## 機能一覧
<table>
<tr>
<th>
<div style="text-align: center;">
ログイン画面
</div>
</th>
<th>
<div style="text-align: center;">
会員登録画面
</div>
</th>
</tr>
<tr>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/login.png">
</td>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/register.png">
</td>
</tr>
</table>

<table>
<tr>
<th>
<div style="text-align: center;">
メール確認画面
</div>
</th>
<th>
<div style="text-align: center;">
タイムスタンプ画面
</div>
</th>
</tr>
<tr>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/mail.png">
</td>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/top.png">
</td>
</tr>
<tr>
<td>
会員登録をすると、登録したメールアドレスに確認メールが届きます。
再送ボタンを押すことで確認メールを再送することも可能です。
</td>
<td>
勤務開始・勤務終了・休憩開始・休憩終了時間を記録することができます。
</td>
</tr>
</table>

<table>
<tr>
<th>
<div style="text-align: center;">
日付別勤怠表画面
</div>
</th>
<th>
<div style="text-align: center;">
ユーザー一覧画面
</div>
</th>
</tr>
<tr>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/attendance.png">
</td>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/users.png">
</td>
</tr>
<tr>
<td>
誰がどれくらい勤務をしたかを確認することができます。
</td>
<td>
登録されているユーザーを確認することができます。
勤怠表ボタンを押すことで個人勤怠表確認画面へと遷移します。
</td>
</tr>
</table>

<table>
<tr>
<th>
<div style="text-align: center;">
個人勤怠表画面
</div>
</th>
<th>
<div style="text-align: center;">
マイページ画面
</div>
</th>
</tr>
<tr>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/attendance_user.png">
</td>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/mypage.png">
</td>
</tr>
<tr>
<td>
選択した人の勤怠状況を確認できます
</td>
<td>
自分の登録メールアドレスと登録日を確認できます。ほかに、自分の個人勤怠表画面に遷移するボタンと、２要素認証を設定するためのボタンがあります。
</td>
</tr>
</table>

<table>
<tr>
<th colspan="2">
<div style="text-align: center;">
2要素認証機能
</div>
</th>
</tr>
<tr>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/TFA.png">
</td>
<td>
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/TFA_challenge.png">
</td>
</tr>
<tr>
<td colspan="2">
2要素認証を有効化することでQRコードとリカバリーコードが表示されます。QRコードをGoogle Authenticatorなどのアプリで読み込むことでコードを受け取ることができるようになり、ログイン時にコードの入力を求められるようになります。
</td>
</tr>
</table>

## 使用技術

<table>
<tr>
<td>
フロントエンド
</td>
<td>
HTML , CSS
</td>
</tr>
<tr>
<td>
バックエンド
</td>
<td>
PHP：8.3 ,
Laravel：10.42 ,
MySQL：8.0.36
</td>
</tr>
<tr>
<td>
インフラ
</td>
<td>
Docker (開発環境) ,
AWS
</td>
</tr>
<tr>
<td>
その他
</td>
<td>
Git , GitHub
</td>
</tr>
</table>

## テーブル設計
 <img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/table.png">

## ER図
<img src="https://raw.githubusercontent.com/nakagawa1573/images/main/timestamp/timestamp.drawio.png">

## 環境構築
### Dockerビルド
1. git clone git@github.com:nakagawa1573/timestamp.git
2. docker-compose up -d --build

＊MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.ymlファイルを編集してください。

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、MAILの記述と環境変数を変更。
4. php artisan key:generate
5. php artisan migrate
