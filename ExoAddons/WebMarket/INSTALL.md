# ExoAddons.WebMarket — Installation Guide

**Slug:** `web-market`
**Version:** 1.0.0
**Author:** ExoProject
**Registration:** Dynamic (installed via ExoDash admin UI)

---

## What is WebMarket?

A **Player-to-Player Web Market** for Silkroad Online private servers.

Players can list items from their in-game inventory for sale on the website, and other players can buy them using **Gold**, **Silk**, or **Web Points**. Features include:

- **Item listing** — players browse their in-game inventory and choose items to list
- **Multi-currency** — supports Gold, Silk (APH_ChangedSilk), and Web Points
- **Item claiming** — bought items delivered via chest box or filter/add-item
- **Vanguard filter integration** — uses item filter service for item name resolution
- **Commission tax** — configurable market fee (default 5%)
- **Listing limits** — max active listings per character
- **Admin panel** — settings management via ExoDash
- **Artisan diagnostic** — `market:filter-status` command to verify game DB connectivity

---

## Requirements

| Requirement | Details |
|-------------|---------|
| PHP | ^8.2 |
| Laravel | ^12.0 |
| ExoAddons Dashboard | ✅ Must be installed first |
| MySQL | 5.7+ / 8.0+ |
| Game DB connections | `account`, `shard`, `vanguard` (configured in `config/database.php`) |

### Required DB Connections

The WebMarket communicates with 3 game database connections. Make sure they exist in `config/database.php`:

```php
'account' => [
    'driver'   => 'sqlsrv',
    'host'     => env('ACCOUNT_DB_HOST'),
    'database' => env('ACCOUNT_DB_DATABASE'),   // SILKROAD_R_ACCOUNT
    'username' => env('ACCOUNT_DB_USERNAME'),
    'password' => env('ACCOUNT_DB_PASSWORD'),
],

'shard' => [
    'driver'   => 'sqlsrv',
    'host'     => env('SHARD_DB_HOST'),
    'database' => env('SHARD_DB_DATABASE'),     // SRO_VT_SHARD
    'username' => env('SHARD_DB_USERNAME'),
    'password' => env('SHARD_DB_PASSWORD'),
],

'vanguard' => [
    'driver'   => 'sqlsrv',
    'host'     => env('VANGUARD_DB_HOST'),
    'database' => env('VANGUARD_DB_DATABASE'),  // GB_JoymaxPortal
    'username' => env('VANGUARD_DB_USERNAME'),
    'password' => env('VANGUARD_DB_PASSWORD'),
],
```

### Required Game DB Objects

The following stored procedures / functions must exist in your game DB:

| Object | DB | Purpose |
|--------|----|---------|
| `_ShardManagerAddItem` | shard | Deliver item to player storage |
| `_ShardManagerRemoveItemBySlot` | shard | Remove item from inventory when listed |
| `_ShardManagerAddGold` | shard | Transfer gold between players |
| `APH_ChangedSilk` table | vanguard | Silk balance reads/writes |

Use the diagnostic command to verify:
```bash
php artisan market:filter-status
```

---

## Installation Steps

### Method A — Via ExoDash Admin UI (Recommended)

1. Copy the `WebMarket/` folder into your project's `ExoAddons/` directory
2. Run `php artisan cache:clear`
3. Go to **ExoDash** → `/exo-admin/addons`
4. Find **WebMarket** in the "Discovered" section
5. Click **Setup** — migrations run automatically
6. Done ✅

### Method B — Manual Installation

#### Step 1 — Copy the folder

```
your-project/
  ExoAddons/
    Dashboard/      ← must already be installed
    Affiliate/      ← (optional, no dependency)
    WebMarket/      ← copy here
```

#### Step 2 — Run migrations

```bash
php artisan migrate --path=ExoAddons/WebMarket/Migrations --force
```

Creates:

| Table | Purpose |
|-------|---------|
| `exo_market_listings` | Active and sold item listings |
| `exo_market_claims` | Records of items bought and pending delivery |

#### Step 3 — Publish assets

```bash
php artisan vendor:publish --tag=exoaddons-webmarketserviceprovider-assets --force
```

Or assets are auto-copied to `public/ExoAddons/WebMarket/css/` on first boot.

#### Step 4 — Clear cache

```bash
php artisan config:clear
php artisan cache:clear
```

> ℹ️ WebMarket is NOT added to `bootstrap/providers.php`.
> It is booted dynamically by the ExoAddonRegistry from the `exo_addons` table.

---

## Configuration

File: `ExoAddons/WebMarket/Config/webmarket.php`

```php
return [
    'enabled'               => true,

    // Commission fee taken from every sale (percent)
    'tax'                   => 5,

    // Accepted currencies
    'currencies'            => ['gold', 'silk', 'points'],

    // APH_ChangedSilk SilkType used for premium silk in this server
    'silk_type'             => 3,

    // How bought items are delivered to buyers
    // 'chest_box'      → delivered via in-game chest
    // 'filter_add_item'→ delivered via vanguard filter
    'claim_delivery'        => 'chest_box',

    // Storage type for claimed items
    // 1 = character inventory, 2 = storage/warehouse
    'claim_storage_type'    => 2,

    // Minimum character level to create a listing
    'min_level'             => 1,

    // Max active listings per character at one time
    'max_active_listings'   => 5,

    // Default listing expiry duration in days
    'listing_duration_days' => 7,
];
```

> Most values can also be changed from **ExoDash → WebMarket → Settings** without touching this file.

---

## Available Routes

| Method | URL | Name | Auth | Description |
|--------|-----|------|------|-------------|
| `GET` | `/market` | `market.index` | Public | Market listing browse page |
| `GET` | `/market/sell` | `market.sell` | Auth | Sell item form |
| `GET` | `/market/sell/inventory/{charId}` | `market.inventory` | Auth | Fetch character inventory (AJAX) |
| `POST` | `/market/sell/list` | `market.list` | Auth | Create a new listing |
| `POST` | `/market/buy/{id}` | `market.buy` | Auth | Buy a listed item |
| `POST` | `/market/cancel/{id}` | `market.cancel` | Auth | Cancel own listing |
| `GET` | `/market/claims` | `market.claims` | Auth | View pending claims |
| `POST` | `/market/claims/{id}/claim` | `market.claims.claim` | Auth | Claim a purchased item |

> **Note:** The debug routes (`/market/debug-filter`, `/market/debug-jid`) are dev-only.
> They only work when `APP_DEBUG=true`. **Remove them from `Routes/web.php` in production.**

---

## Artisan Commands

| Command | Description |
|---------|-------------|
| `market:filter-status` | Diagnostic — checks game DB stored procedures and silk table accessibility |

Run to verify setup:

```bash
php artisan market:filter-status
```

---

## Admin Panel Integration

Access via: **ExoDash → WebMarket**

Settings panel (`web-market::admin.settings`) lets you configure:
- Tax rate
- Supported currencies
- Silk type
- Claim delivery method
- Storage type
- Max listings per character
- Listing duration

Changes saved to `exo_addon_configs` and applied immediately on next request.

---

## Health Check

```php
WebMarketServiceProvider::health()  // returns bool
```

Checks that `exo_market_listings` table is accessible.
Status shown in **ExoDash → Addons** list.

---

## Files Reference

```
WebMarket/
  WebMarketServiceProvider.php
  addon.json
  Config/
    webmarket.php
  Controllers/
    WebMarketController.php      ← all HTTP handlers (browse, sell, buy, claim)
  Commands/
    CheckFilterStatus.php        ← artisan: market:filter-status
  Models/
    MarketListing.php            ← listing record model
    MarketClaim.php              ← claim record model
  Services/
    MarketItemService.php        ← core business logic (list, buy, cancel, claim)
    VanguardFilterService.php    ← item name resolution via vanguard filter
  Routes/
    web.php                      ← all player-facing routes + debug routes
    debug_filter.php             ← (standalone debug script, dev only)
  Views/
    index.blade.php              ← market browse page
    sell.blade.php               ← sell item form
    claims.blade.php             ← claims list page
    admin/
      settings.blade.php        ← admin settings form
    partials/
      subnav.blade.php           ← market sub-navigation partial
  Migrations/
    2026_06_03_000001_create_exo_market_listings_table.php
    2026_06_03_000002_create_exo_market_claims_table.php
  Assets/
    css/                         ← published to public/ExoAddons/WebMarket/css/
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Market page shows 404 | Addon not installed in `exo_addons` table — run Setup via ExoDash |
| Inventory not loading | Check `account` and `shard` DB connections in `.env` |
| Silk balance shows 0 | Verify `silk_type` matches your server's `APH_ChangedSilk` SilkType |
| Items not delivered | Check `_ShardManagerAddItem` procedure exists — run `market:filter-status` |
| Health check fails | Run `php artisan migrate --path=ExoAddons/WebMarket/Migrations --force` |
| Debug routes returning 403 | Set `APP_DEBUG=true` in `.env` (dev only, never in production) |

---

## Uninstalling

### Via ExoDash UI
- **Toggle off**: disables the addon (data preserved)
- **Uninstall (soft)**: same as toggle off
- **Uninstall (hard)**: rolls back migrations, deletes configs

### Manual Hard Uninstall

```bash
php artisan migrate:reset --path=ExoAddons/WebMarket/Migrations --force
```

Then delete the `ExoAddons/WebMarket/` folder and clear cache:

```bash
php artisan cache:clear
```

> ⚠️ Hard uninstall will delete all market listings and claim records permanently.
