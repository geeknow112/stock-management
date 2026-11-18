# E2Eテストの実行手順

stock-management の E2E テスト(Playwright)を、ローカルと CI で実行する手順です。
テストは WordPress + MySQL + 本プラグインを `wp-env`(Docker)で起動した環境に対して実行します。
本番のデータ・認証情報は使いません。テスト専用のアカウントとデータだけを使います。

## 前提

- Docker が起動していること
- Node.js 18 以上(CI は Node.js 20)

## ローカルでの実行

```bash
npm install                        # 初回のみ
npx playwright install chromium    # 初回のみ
npm run env:start                  # テスト環境を起動(プラグインも自動で有効化)
npm run e2e                        # テストを実行
npm run env:stop                   # 終了したら停止
```

- サイト: http://localhost:8888
- 管理画面: http://localhost:8888/wp-admin (ユーザー `admin` / パスワード `password`、テスト専用)
- 環境の設定は `.wp-env.json` にあります。

`npm run e2e` の開始時(`e2e/global-setup.ts`)に、テスト用ユーザーの作成と、ロール別のログイン状態の保存が自動で行われます。

### 補助コマンド

| コマンド | 内容 |
|----------|------|
| `npm run e2e:seed` | テスト用ユーザーの投入のみ(何度実行しても同じ結果) |
| `npm run e2e:reset` | プラグインのデータを空にして、ユーザーを再投入 |
| `npm run env:reset` | DB を初期状態に戻す |
| `npm run env:destroy` | 環境を完全に破棄 |

特定のテストだけを動かすときは、次のように実行します。

```bash
npx playwright test e2e/tests/login.spec.ts
npx playwright test -g "editor のアクセス制御"
```

### 結果の見方

- 実行が終わると、ターミナルに成功・失敗の件数と、失敗したテストの内容が出ます。
- 失敗したテストのトレースは `test-results/` に保存されます。次のコマンドで画面操作の履歴を確認できます。

```bash
npx playwright show-trace test-results/<失敗したテストのフォルダ>/trace.zip
```

- WordPress / PHP 側のエラーは、次のコマンドでログを確認します。

```bash
npx wp-env logs development --watch=false
```

## CI(GitHub Actions)での実行

`.github/workflows/e2e.yml` が、PR の作成・更新時と main への push 時に自動で実行されます。
`npm ci` → `wp-env start` → Playwright のインストール → `npm run e2e` の順に動きます。
同じ PR で新しい実行が始まると、古い実行は自動で取り消されます。

### 失敗したときのレポートの見方

1. PR の Checks から `E2E` を開き、失敗したステップのログを確認します。
2. 実行画面の Artifacts から、次の2つをダウンロードします(保存期間は7日)。
   - `playwright-report`: Playwright のレポートとトレース(`npx playwright show-trace` で開けます)
   - `wp-env-logs`: WordPress / PHP のログ(`development.log` と `tests.log`)

## テストの一覧

現在の合計は59件です(ロールごとの許可・拒否のページ数に応じて増減します)。

| ファイル | 確認していること | 件数 |
|----------|------------------|------|
| `e2e/tests/login.spec.ts` | administrator / editor / subscriber の3ロールが、保存したログイン状態で管理画面(`/wp-admin/`)に入れること | 3 |
| `e2e/tests/access-control.spec.ts` | ロールごとに、各管理ページへ「アクセスできる」「アクセスできない(403 または権限エラー画面)」が期待どおりであること | 45 |
| `e2e/tests/delivery-graph.spec.ts` | 配送予定表の表示、開始日の指定、受注の無い日の表示、検索ボタン(cmd_search)での日付検索 | 5 |
| `e2e/tests/sales-order.spec.ts` | 受注管理: 新規登録(入力→確認→登録)、必須項目が空のときの差し戻し、編集して更新、一括操作での削除、ロット番号での検索とロット登録画面での表示。専用の受注(sales=101〜103)を作り、終了時に消す | 6 |
| `e2e/tests/reports.spec.ts` | 帳票出力: 在庫証明書(stock-export)と倉出伝票(stock-export-day)の表示内容。PDF/CSVではなく画面のHTML表なので、見出し・品名・荷姿・個数・数量・顧客名を確認。共通のサンプルデータを読むだけで変更しない | 6 |

ロール別のアクセス可否は次のとおりです。
一覧の元データは `e2e/support/access-matrix.ts` で、メニュー定義(`stock-management.php` の `add_sub_menu()`)を変えたときはこちらも更新します。

| ロール | アクセスできるページ | 拒否されるページ |
|--------|----------------------|------------------|
| administrator | 全15ページ | なし |
| editor | `sales-detail` `sales-list` `delivery-graph` `delivery-graph-stg` `sales-summary` | 上記以外の10ページ |
| subscriber | `delivery-graph` `delivery-graph-stg` | 上記以外の13ページ |

受注管理・在庫管理・顧客管理・帳票出力の機能別テストは、別Issue(#1419〜#1422)で追加します。
追加したときは、この一覧にも行を足してください。

## テスト用アカウント

テスト専用です。定義は `e2e/support/users.ts` にあります。

| ロール | ユーザー名 | パスワード |
|--------|------------|------------|
| administrator | `admin` | `password` |
| editor | `e2e_editor` | `e2e-password` |
| subscriber | `e2e_subscriber` | `e2e-password` |

ロール別のログイン状態は `e2e/auth/<ロール>.json` に保存されます(git 管理外)。
テストでは `test.use({ storageState: storageStatePath('editor') })` で使います。
