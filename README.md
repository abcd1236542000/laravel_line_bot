# 安裝

安裝相依套件 包含[linecorp/line-bot-sdk](https://github.com/line/line-bot-sdk-php)

```bash
composer install
```
複製env檔 

```bash
cp -p .env.example .env
```
產生 generate key

```bash
 php artisan key:generate
```
必要 table 需做migrate
```
php artisan migrate
```
----
# 設定
## 設定 .env檔 LINE Bot 參數

取得 Channel secret https://developers.line.biz/console/channel/{channel_id}

取得 Channel access token https://developers.line.biz/console/channel/{channel_id}/messaging-api

```bash
LINE_MESSAGE_CHANNEL_SECRET=Channel secret
LINE_MESSAGE_CHANNEL_ACCESS_TOKEN=Channel access token
```
## 設定LINE Messaging API webhook endpoint

設定 Webhook settings https://developers.line.biz/console/channel/{channel_id}/messaging-api

```
Webhook URL : https://{YOURDOMAIN}/api/webhook/bot/line
```
```
驗證 Webhook URL : Verify      
```
```
Use webhook : 開啟      
```
## 設定 .env檔 LINE Notify 參數

登錄服務及Callback URL https://notify-bot.line.me/my/services/new

取得Key https://notify-bot.line.me/my/services/edit?clientId={clientId}


```bash
LINE_NOTIFY_CLIENT_ID=Client ID
LINE_NOTIFY_CLIENT_SECRET=Client Secret
LINE_NOTIFY_REDIRECT_URI=https://{YOURDOMAIN}/api/notification/line/notify/authorize-callback
```
清快取

```bash
 php artisan config:clear
```
----
# 功能
## webhook
### 事件統整到下列路徑，直接複製SampleService並且更名或是直接修改對應service即可，命名規則{event 名稱}Service.php
```
App\Services\Bot\Line\Executes\*
...
```
- TextMessage 事件 : App\Services\Bot\Line\Executes\TextMessageService

----
# Heroku
## 部屬Heroku所需檔案
### Procfile [nginx](https://devcenter.heroku.com/articles/php-support#nginx)
```
web: vendor/bin/heroku-php-nginx -C nginx_app.conf public/
```
### nginx_app.conf [configuration](https://devcenter.heroku.com/articles/custom-php-settings#using-a-custom-application-level-nginx-configuration)
```
location / {
    # try to serve file directly, fallback to rewrite
    try_files $uri @rewriteapp;
}

location @rewriteapp {
    # rewrite all to index.php
    rewrite ^(.*)$ /index.php/$1 last;
}

location ~ ^/index\.php(/|$) {
    try_files @heroku-fcgi @heroku-fcgi;
    # ensure that /index.php isn't accessible directly, but only through a rewrite
    internal;
}
```