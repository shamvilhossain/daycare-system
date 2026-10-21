# Deploying kinder-care to a server

How to set up the production `.env` and finish the first deployment.

## 1. Production `.env`

Copy this into `.env` on the server, then replace every `CHANGE_ME`.

```dotenv
APP_NAME="kinder-care"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://CHANGE_ME.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CHANGE_ME
DB_USERNAME=CHANGE_ME
DB_PASSWORD=CHANGE_ME

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail: "log" writes emails to the log file. Switch to smtp once you have mail credentials.
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@CHANGE_ME.com"
MAIL_FROM_NAME="KinderCare"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Parent app / Play Store (config/parentapp.php)
ORG_NAME="KinderCare"
ORG_CONTACT_EMAIL=CHANGE_ME
ORG_CONTACT_PHONE=CHANGE_ME
ORG_ADDRESS="CHANGE_ME, Dhaka"
PRIVACY_POLICY_UPDATED=2026-10-20
ACCOUNT_DELETION_DAYS=30
TWA_PACKAGE_NAME=
TWA_SHA256_FINGERPRINTS=

# Online payments / SSLCommerz (config/payments.php). Gateway credentials live in payment_channels.
PAYMENT_LINK_DAYS=7
PAYMENT_RECEIPT_DAYS=365
# Leave empty on the server: links and callbacks use APP_URL
PAYMENT_PUBLIC_URL=
PAYMENT_CALLBACK_URL=
PAYMENT_VERIFY_SSL=true

# AdminLTE showcase pages (keep false in production)
ADMINLTE_DEMO=false
ADMINLTE_DOCS=false

# One-time admission fee (BDT) on a child's first programme enrolment. 0 = off.
ADMISSION_FEE=5000
ADMISSION_FEE_DUE_DAYS=7
```

### Values to fill in

| Key | What to put |
|---|---|
| `APP_KEY` | Leave empty, then run `php artisan key:generate` (step 2). Never change it after launch: logins and encrypted data depend on it. Never commit it to git. |
| `APP_URL` | Your HTTPS domain, no trailing slash. If the app is in a subfolder, include it, e.g. `https://example.com/kinder-care`. |
| `DB_*` | The MySQL database and user created on the server. |
| `MAIL_FROM_ADDRESS` | An address on your domain. |
| `ORG_*` | Contact details shown on the privacy policy page. |
| `ADMISSION_FEE` | Your real admission fee in Taka (5000 is a placeholder). `0` turns admission invoices off. |
| `TWA_*` | Fill in when the Play Store app is built (package name and signing certificate SHA-256). |

### How this differs from local development

| Setting | Server | Why |
|---|---|---|
| `APP_ENV` / `APP_DEBUG` | `production` / `false` | Hides error details from visitors |
| `SESSION_SECURE_COOKIE` | `true` | Login cookie only sent over HTTPS |
| `SESSION_ENCRYPT` | `true` | Session data encrypted |
| `LOG_STACK` / `LOG_LEVEL` | `daily` / `error` | One log file per day, errors only |
| `MAIL_MAILER` | `log` | Mailtrap test credentials don't work on the server |
| `PAYMENT_PUBLIC_URL` / `PAYMENT_CALLBACK_URL` | empty | Only needed for a localhost tunnel; the server uses `APP_URL` |

## 2. First deployment commands

Run in the project folder on the server:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- Point the web server's document root at the `public/` folder.
- The `storage/` and `bootstrap/cache/` folders must be writable by the web server user.
- After any later `.env` change, run `php artisan config:cache` again.

## 3. Online payments (SSLCommerz)

- The store ID and password are in the `payment_channels` table, not in `.env`. Switch that row from sandbox to your live store when you go live.
- SSLCommerz must be able to reach `https://your-domain/payment/sslcommerz/...`, so the domain needs a valid SSL certificate.
- Do one small real payment after going live and check the invoice turns **Paid**.

## 4. Checklist before opening to parents

- [ ] `APP_DEBUG=false`
- [ ] HTTPS works and `APP_URL` matches it exactly
- [ ] `php artisan migrate --force` ran without errors
- [ ] `ADMISSION_FEE` set to the real amount
- [ ] Live SSLCommerz credentials in `payment_channels`
- [ ] A test parent can log in, see the Fees tab and pay
- [ ] Database backups scheduled
