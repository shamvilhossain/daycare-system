# Lost Child QR Safety Tag Feature

Build a complete "lost child QR safety tag" system: QR codes that encode a public URL showing the child's first name and guardian phone, so anyone who finds a lost child can call the parent immediately.

## Proposed Changes

### Database — Migration for `qr_path` column

The existing `child_safety_tags` migration has no `qr_path` column. We need a new migration to add it.

#### [NEW] `database/migrations/xxxx_add_qr_path_to_child_safety_tags_table.php`
- Adds `string('qr_path')->nullable()` after the `label` column.

---

### Models

#### [MODIFY] [`ChildSafetyTag.php`](file:///c:/laragon/www/daycare-system/app/Models/ChildSafetyTag.php)
- Add missing `use Illuminate\Support\Str;` import (the `booted()` method already references `Str::random(32)` but the import is missing).
- Add `$casts` for `is_active => boolean`.

#### [MODIFY] [`ChildFoundReport.php`](file:///c:/laragon/www/daycare-system/app/Models/ChildFoundReport.php)
- Already looks correct — `$guarded = []` and `safetyTag()` relationship. No changes needed.

#### [MODIFY] [`Child.php`](file:///c:/laragon/www/daycare-system/app/Models/Child.php)
- Add `safetyTags()` hasMany relationship.
- Add `parentProfile()` accessor — returns the **primary** parent from the many-to-many `parents()` relationship (where `pivot.is_primary = true`), falling back to the first parent. This is needed for the public card's "Call Guardian" button.

---

### Controller — ChildSafetyCardController (public, no auth)

#### [MODIFY] [`ChildSafetyCardController.php`](file:///c:/laragon/www/daycare-system/app/Http/Controllers/ChildSafetyCardController.php)
Currently an empty skeleton. Will implement:

- **`show(string $token)`**: Find active `ChildSafetyTag` by token (404 if not found/inactive), load `child.parents`, return `safety.card` view.
- **`reportFound(Request $request, string $token)`**: Inline validation for `reporter_phone`, `latitude`, `longitude`; create `ChildFoundReport` inside `DB::transaction`; dispatch `SendFoundChildAlertJob`; redirect back with success status.

---

### Controller — Safety tag admin actions on ChildController

#### [MODIFY] [`ChildController.php`](file:///c:/laragon/www/daycare-system/app/Http/Controllers/ChildController.php)
Add three new methods:

- **`show(Child $child)`**: Load safety tags + parents, return `admin.children.show` view. (Currently missing — the resource route exists but `show()` was never implemented.)
- **`generateSafetyTag(Request $request, Child $child)`**: Validate optional `label`, create `ChildSafetyTag` inside `DB::transaction`, generate QR PNG via Endroid Builder, save to `storage/app/public/safety-tags/{token}.png`, update tag's `qr_path`.
- **`deactivateSafetyTag(ChildSafetyTag $tag)`**: Set `is_active = false`, delete stored QR file.

---

### Routes

#### [MODIFY] [`routes/web.php`](file:///c:/laragon/www/daycare-system/routes/web.php)

**Public routes** (outside `auth` middleware, throttled):
```php
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/s/{token}', [ChildSafetyCardController::class, 'show'])->name('safety.card');
    Route::post('/s/{token}/found', [ChildSafetyCardController::class, 'reportFound'])->name('safety.report');
});
```

**Admin routes** (inside existing `role:admin` group):
```php
Route::post('/admin/children/{child}/safety-tag', [ChildController::class, 'generateSafetyTag'])->name('admin.children.generate-safety-tag');
Route::patch('/admin/children/safety-tag/{tag}/deactivate', [ChildController::class, 'deactivateSafetyTag'])->name('admin.children.deactivate-safety-tag');
```

---

### Job

#### [NEW] `app/Jobs/SendFoundChildAlertJob.php`
- `implements ShouldQueue`, uses `SerializesModels`
- Constructor takes `ChildFoundReport $report`
- `handle()`: resolve child via `$report->safetyTag->child`, get guardian phone from primary parent or `ec_phone`, build Google Maps link if lat/lng, compose SMS message, mark `$report->status = 'notified'`
- Actual SMS/WhatsApp call left as a `// TODO:` comment

---

### Views

#### [NEW] `resources/views/admin/children/show.blade.php`
- Same layout pattern as [`edit.blade.php`](file:///c:/laragon/www/daycare-system/resources/views/admin/children/edit.blade.php) (full HTML doc with AdminLTE, sidebar, navbar)
- Displays child details (name, DOB, photo, parents, emergency contact)
- **Safety Tags section**: form to generate new tag (optional label), table of existing tags showing label, QR image, token, status, download link, deactivate button
- Compact page-banner matching the recently updated style

#### [NEW] `resources/views/safety/card.blade.php`
- **Standalone HTML** — does NOT extend adminlte (public, no auth)
- Bootstrap 5.3 via CDN `<link>` only
- `<meta name="robots" content="noindex, nofollow">`
- Mobile-first, max-width ~480px centered card
- Shows: child's **first_name only** (never last name, address, medical info)
- Big green `<a href="tel:...">` Call Guardian button
- Secondary emergency contact button if `$child->ec_phone` exists
- "I Found This Child" form with `@csrf`, optional `reporter_phone`, hidden `latitude`/`longitude`, JS geolocation on submit
- `session('status')` success alert

---

## Security Checklist
- ✅ Public page shows only `first_name` + phone contacts — no last name, address, medical, allergies
- ✅ Token is `Str::random(32)` — cryptographically random, never derived from IDs
- ✅ Public routes outside `auth` middleware, throttled at 30 req/min
- ✅ `is_active` checked on every public lookup via `firstOrFail()` query
- ✅ `noindex, nofollow` meta tag on public card

## Verification Plan

### Manual Verification
- Confirm migration adds `qr_path` column
- Confirm QR code generation produces valid PNG files in `storage/app/public/safety-tags/`
- Confirm public `/s/{token}` route shows only first name and phone
- Confirm deactivated tags return 404
- Confirm "I Found This Child" form creates a `ChildFoundReport` and dispatches the job
- Confirm throttling works (>30 requests in 1 minute should be rate-limited)
