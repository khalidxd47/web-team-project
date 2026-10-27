# Store - T-shirt Merch Store (CS3810T)

Small-batch streetwear t-shirt store built with vanilla PHP, JavaScript, CSS and MySQL (PDO).

## Pages
| Page | Purpose |
|------|---------|
| `index.php` | Hero, featured tees, quick-add-to-bag modal |
| `about.php` | Brand story, fabric specs and size guide tables |
| `products.php` | Catalog with category and size filters |
| `details.php` | Single tee: size picker, stock counter, add to bag |
| `gallery.php` | Lookbook slideshow (next/prev, dots, auto-play) |
| `contact.php` | Contact and custom print requests (JS + PHP validation) |
| `dashboard.php` | Admin inventory: CRUD, live stock tracker, instant search |
| `login.php`, `register.php`, `logout.php`, `bag.php` | Session auth and shopping bag |

## Structure
```
config/db.php          PDO connection (store_db)
includes/              header, footer, helpers, product card, quick-add modal
assets/css/style.css   Main stylesheet (tokens, Grid/Flexbox, media queries)
assets/js/             main.js (nav + modal), slideshow.js, validate.js
assets/images/         T-shirt SVGs (work offline)
schema.sql             Database, tables and seed data
```

## Setup
1. Copy the folder to `C:\wamp64\www\` (WAMP) or `C:\xampp\htdocs\` (XAMPP).
2. Import `schema.sql` in phpMyAdmin (creates `store_db`).
3. Open http://localhost/web-team-project/

## Demo accounts
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@store.com | admin123 |
| Customer | customer@store.com | customer123 |

Admins are redirected to the dashboard after login; customers go to the shop.
