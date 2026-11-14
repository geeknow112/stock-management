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
