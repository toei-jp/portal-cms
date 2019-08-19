# toei-jp/portal-cms

東映ポータルサイトのCMS管理画面

## システム要件

- PHP: 7.2
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

※ StorageはAzureプラットフォームで別途作成してください。

web: http://localhost:8000/

phpmyadmin: http://localhost:8080/

### コマンド例

コンテナを作成し、起動する。

```sh
$ docker-compose up
```

## その他

### PHP CodeSniffer

```sh
$ composer phpcs
```

### PHPStan

```sh
$ composer phpstan
```
