# ExoAddons Dashboard — Installation Guide

> **This is a CORE addon.** It must always be installed first.
> All other addons depend on it. Never remove it.

---

## What is Dashboard?

The ExoAddons admin panel and the brain of the entire ExoAddons system.
It provides:
- **Admin UI** at `/exo-admin` — manage all installed addons
- **ExoAddonRegistry** — discovers, installs, toggles, and updates addons
- **ExoHooks** — lightweight event bus for inter-addon communication
- **Dynamic addon booting** — loads enabled addons from the database on every request

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | ^8.2 |
| Laravel | ^12.0 |
| MySQL | 5.7+ / 8.0+ |

---

## Installation Steps

### Step 1 — Copy the folder

Copy the `Dashboard/` folder into your project's `ExoAddons/` directory:

```
your-project/
  ExoAddons/
    Dashboard/    ← place it here
```

### Step 2 — Verify autoload in composer.json

Make sure your `composer.json` has the `ExoAddons\` namespace mapped:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "ExoAddons\\": "ExoAddons/"
    }
}
```

If you added/changed anything, run:

```bash
composer dump-autoload
```

### Step 3 — Register the ServiceProvider

Add to `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    // ... other providers ...
    ExoAddons\Dashboard\DashboardServiceProvider::class,   // ← add this
];
```

> ⚠️ **Dashboard must come BEFORE any other ExoAddon provider.**

### Step 4 — Run migrations

```bash
php artisan migrate
```

This creates two tables:

| Table | Purpose |
|-------|---------|
| `exo_addons` | Registry of installed addons |
| `exo_addon_configs` | Per-addon key-value settings |

### Step 5 — Publish assets

```bash
php artisan vendor:publish --tag=exoaddons-dashboard-assets --force
```

Or let the auto-publish handle it — assets are copied automatically on first boot.

### Step 6 — Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Configuration

File: `ExoAddons/Dashboard/Config/dashboard.php`

```php
return [
    'enabled' => true,
    'prefix'  => 'exo-admin',     // URL prefix → /exo-admin
    'title'   => 'ExoAddons Dashboard',
    'version' => '1.0.0',
];
```

To change the admin URL, update `prefix`. Example: `'prefix' => 'admin'` → `/admin/addons`

---

## Accessing the Admin Panel

1. Open your browser → `http://yourdomain.com/exo-admin`
2. You'll be redirected to the login page
3. Log in with an admin account (user must have `role->is_admin === true`)

> The Dashboard supports both standard Laravel auth and legacy MD5 password auth (sro-cms compatible).

---

## Admin Panel Routes

| URL | Action |
|-----|--------|
| `GET /exo-admin/login` | Login page |
| `GET /exo-admin/addons` | All addons list |
| `GET /exo-admin/addons/{slug}` | Addon settings |
| `POST /exo-admin/addons/{slug}/setup` | Install addon |
| `POST /exo-admin/addons/{slug}/toggle` | Enable/disable addon |
| `POST /exo-admin/addons/{slug}/save` | Save addon settings |
| `POST /exo-admin/addons/{slug}/update` | Update addon version |
| `POST /exo-admin/addons/{slug}/uninstall` | Uninstall addon |

---

## Files Reference

```
Dashboard/
  DashboardServiceProvider.php   ← registers routes, views, migrations, boots addons
  addon.json                     ← (N/A — Dashboard is always static)
  Controllers/
    DashboardController.php      ← handles all admin panel HTTP actions
  Services/
    ExoAddonRegistry.php         ← core: scan, install, toggle, uninstall, update
  Support/
    ExoHooks.php                 ← inter-addon event system
  Routes/
    web.php                      ← all dashboard routes
  Views/
    layout.blade.php             ← master layout (all addon views extend this)
    login.blade.php              ← login page
    addons/
      index.blade.php            ← addon list page
      market.blade.php           ← market-specific page
  Config/
    dashboard.php                ← prefix, title, enabled flag
  Migrations/
    *_create_exo_addons_table.php
    *_create_exo_addon_configs_table.php
  Assets/
    css/                         ← published to public/ExoAddons/Dashboard/css/
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `/exo-admin` returns 404 | Check ServiceProvider is registered in `bootstrap/providers.php` |
| Login fails for admin user | Ensure `users.role.is_admin === true` in DB |
| Addon list is empty | Run `php artisan cache:clear` then refresh |
| Migration error on first boot | Run `php artisan migrate` manually |
| Assets not loading | Run `php artisan vendor:publish --tag=exoaddons-dashboard-assets --force` |

---

## Uninstalling

> ⚠️ Removing Dashboard will break ALL other ExoAddons. Do not uninstall unless migrating entirely.

1. Remove from `bootstrap/providers.php`
2. Drop tables: `exo_addons`, `exo_addon_configs`
3. Delete `ExoAddons/Dashboard/` folder
4. Run `composer dump-autoload`
