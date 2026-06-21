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
