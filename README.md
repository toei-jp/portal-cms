# toei-jp/portal-cms

![Test](https://github.com/toei-jp/portal-cms/workflows/Test/badge.svg)

東映ポータルサイトのCMS管理画面

## システム要件

- PHP: 7.3
- MySQL: 5.7
- Azure App Service (Windows)
- Azure Blob Storage

## EditorConfig

[EditorConfig](https://editorconfig.org/) でコーディングスタイルを定義しています。

利用しているエディタやIDEにプラグインをインストールしてください。

[Download a Plugin](https://editorconfig.org/#download)

## Docker

ローカル環境としてDockerが利用できます。

※ 現状では開発環境としての利用のみを想定してます。

※ AzureはWindowsサーバですが、こちらはLinuxサーバです。

※ Storageエミュレーターはpreview版です。必要に応じてAzureプラットフォームで別途作成してください。

web: http://localhost:8000/

phpmyadmin: http://localhost:8080/

### docker-compose コマンド例

コンテナを作成し、起動する。

```sh
$ docker-compose up
```

## アプリケーション コマンド

```sh
$ php bin/console help
```

### viewキャッシュ削除

```sh
$ php bin/console cache:clear:view
```

## その他 コマンド

### PHP Lint

```sh
$ composer phplint
```

### PHP CodeSniffer

```sh
$ composer phpcs
```

### PHPStan

```sh
$ composer phpstan
```

### PHPUnit

```sh
$ composer phpunit
```
