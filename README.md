# stock-management

WordPress plugin for inventory and order management.

## Features

- 📦 Stock management (inventory tracking, bulk registration)
- 🛒 Order management (CRUD, lot number tracking)
- 👥 Customer management
- 📊 Delivery schedule visualization
- 📄 Export (inventory certificate, shipping slips)

## Requirements

- WordPress 5.0+
- PHP 8.0+
- MySQL 5.7+

## Installation

1. Clone this repository to `wp-content/plugins/`
2. Create `config/config.php` from template
3. Activate the plugin in WordPress admin

```bash
cd wp-content/plugins/
git clone https://github.com/geeknow112/stock-management.git
```

## Structure

```
controllers/    # MVC controllers
models/         # Database models
views/          # Blade templates
cli/            # CLI tools (webhook, sales)
library/        # External libraries (BladeOne, Rakit)
```

## User Roles

| Role | Access |
|------|--------|
| Administrator | Full access |
| Editor | Orders, Delivery schedule, Summary |
| Subscriber | Delivery schedule only |

## Versioning and Rollback

Every version that runs in production is marked with a Git tag (`vX.Y.Z`).
Changes keep landing on `main`, so a tag is the reference point to return to when a change causes trouble.

### Tags

| Tag | Commit | Note |
|-----|--------|------|
| `v1.0.0` | `5fa9cf2` | The version running in production as of 2026-11-14. `main` has only README changes on top of it. |

### Marking a new production version

Run this right after deploying, on the exact commit that was deployed:

```bash
git tag vX.Y.Z <commit>
git push origin vX.Y.Z
```

Do not move or delete an existing tag.

### Rolling back

Check out the tag that was running before the problematic change, and deploy it with the usual deployment procedure.

```bash
git fetch --tags
git checkout v1.0.0
```

The production deployment procedure itself is not documented here yet. It will be added when the next production deployment is carried out.

## テスト環境(wp-env)

E2Eテスト用に、WordPress + MySQL + 本プラグインをDockerで起動できます。
本番のデータ・認証情報は使いません。

前提: Docker と Node.js(18以上)がインストールされていること。

```bash
npm install          # 初回のみ
npm run env:start    # 起動(プラグインも自動で有効化)
npm run env:stop     # 停止
npm run env:reset    # DBを初期状態に戻す
npm run env:destroy  # 環境を完全に破棄
```

- サイト: http://localhost:8888
- 管理画面: http://localhost:8888/wp-admin (ユーザー `admin` / パスワード `password`、テスト専用)
- 設定は `.wp-env.json` にあります。

### E2Eテスト(Playwright)

Playwright(TypeScript)で、Administrator / Editor / Subscriber の3ロールのログイン状態を使ってテストします。
テスト専用のアカウント・データのみを使います。

```bash
npm install
npx playwright install chromium   # 初回のみ
npm run env:start                 # テスト環境を起動
npm run e2e                       # テスト実行(開始時にユーザー作成とログイン状態の保存を自動実行)
npm run e2e:seed                  # ユーザーの投入のみ(何度実行しても同じ結果)
npm run e2e:reset                 # プラグインのデータを空にして、ユーザーを再投入
```

- ロール別のログイン状態は `e2e/auth/<ロール>.json` に保存されます(git管理外)。
- テストでは `test.use({ storageState: storageStatePath('editor') })` で使えます。
- テストユーザーは `e2e/support/users.ts` で定義しています。
- ロール別のアクセス制御は `e2e/tests/access-control.spec.ts` で確認します。許可/拒否の一覧は `e2e/support/access-matrix.ts` にあり、メニュー定義(`stock-management.php` の `add_sub_menu()`)を変えたときはここも更新してください。

### CIでのE2E実行(GitHub Actions)

`.github/workflows/e2e.yml` が、PRの作成・更新時と main への push 時に自動で実行されます。
`npm ci` → `wp-env start` → Playwrightのインストール → `npm run e2e` の順で動き、管理画面へのログイン(3ロール)を確認します。
同じPRで新しい実行が始まると、古い実行は自動で取り消されます。

失敗したときの見方:

- PRの Checks から `E2E` を開き、失敗したステップのログを確認します。
- 実行画面の Artifacts から `playwright-report`(Playwrightのレポート・トレース)と `wp-env-logs`(WordPress/PHPのログ)をダウンロードできます(保存期間7日)。
