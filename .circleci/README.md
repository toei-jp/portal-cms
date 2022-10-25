# CircleCI

[ドキュメント](https://circleci.com/docs/ja/)

## Contexts

toei-jp (Organizations) > Organization Settings > Contexts

### Docker Hub

| Name | Value |
|:---|:---|
|DOCKERHUB_ID |Docker Hub ユーザ |
|DOCKERHUB_ACCESS_TOKEN |Access Token |

## Environment Variables

toei-jp (Organizations) > portal-cms (Projects) > Project Settings > Environment Variables

### デプロイ

| Name | Value |
|:---|:---|
| ENV_VARIABLES_DEVELOPMENT | 環境変数を設定したyaml形式データをBase64エンコード （development用） |
| GCLOUD_SERVICE_KEY_DEVELOPMENT | Googleプロジェクトのフルサービス・キーJSONファイル （development用） |
| GOOGLE_COMPUTE_REGION_DEVELOPMENT | gcloud CLI のデフォルトとして設定する Google compute region （development用） |
| GOOGLE_PROJECT_ID_DEVELOPMENT | gcloud CLIのデフォルトとして設定するGoogleプロジェクトID （development用） |
