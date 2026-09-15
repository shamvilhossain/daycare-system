# Wire Therapy Sessions into Invoice Billing

Admin manually creates invoices for a child. Therapy billing is **optional and separate** from daycare billing — admin can choose to include completed therapy sessions, create a therapy-only invoice, or create a daycare-only invoice with manual line items.

## Findings from Codebase Review

**Current `invoice_items` migration has a problem:**
- `therapy_session_id` is defined as a **required non-nullable** foreign key — this means *every* invoice item must reference a therapy session, breaking all existing daycare line items (tuition, fees, etc.).
- There are no `quantity` or `unit_price` columns — the user wants a **price snapshot** from `therapy_services.session_rate`.

**Current `invoice_items` model** has no relationship to `TherapySession`, and `TherapySession` has no relationship back to `InvoiceItem`.

---

## Proposed Changes

### Migration Fix

#### [MODIFY] [create_invoice_items_table.php](file:///c:/laragon/www/daycare-system/database/migrations/2026_05_05_065608_create_invoice_items_table.php)

Fix the schema to support both daycare and therapy billing:

```diff
 $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
-$table->foreignId('therapy_session_id')->constrained('therapy_sessions')->cascadeOnDelete();
+$table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->nullOnDelete();
+// nullable — only set for therapy line items; null for daycare/manual items
+
 $table->string('description')->comment('e.g., tuition, Late Pickup Fee.');
+$table->unsignedInteger('quantity')->default(1);
+$table->decimal('unit_price', 10, 2);
+// price snapshot — copied from therapy_services.session_rate at billing time
 $table->decimal('amount', 10, 2);
+// amount = quantity × unit_price (pre-computed for convenience)
```

Key decisions:
- `therapy_session_id` becomes **nullable** — `null` for daycare/manual items, set for therapy items
- `nullOnDelete` instead of `cascadeOnDelete` — deleting a session shouldn't wipe invoice history
- `unit_price` stores the **snapshot** of `therapy_services.session_rate` at the moment of billing
- `quantity` defaults to 1 (one session = one line item), but keeps the column for flexibility
- `amount` stays as the computed `quantity × unit_price`

---

### Models

#### [MODIFY] [InvoiceItem.php](file:///c:/laragon/www/daycare-system/app/Models/InvoiceItem.php)

- Add `therapySession()` relationship
- Add `unit_price` and `quantity` to casts

#### [MODIFY] [TherapySession.php](file:///c:/laragon/www/daycare-system/app/Models/TherapySession.php)

- Add `invoiceItem()` relationship (hasOne) — a session maps to at most one invoice item
- Add `session_date` to casts

---

### Service Layer

#### [MODIFY] [InvoiceService.php](file:///c:/laragon/www/daycare-system/app/Services/InvoiceService.php)

Add a method `attachTherapySessions(Invoice $invoice, array $sessionIds)`:
1. Loads each `TherapySession` with its `service` relation
2. Filters to only `status = 'completed'`
3. Uses `whereDoesntHave('invoiceItem')` to skip sessions already billed — **same dedup pattern you described**
4. For each billable session, creates an `InvoiceItem` with:
   - `description` = e.g. "SLT Session — Sep 10, 2026"
   - `unit_price` = **snapshot** of `therapy_services.session_rate` at this moment
   - `quantity` = 1
   - `amount` = `unit_price × quantity`
   - `therapy_session_id` = the session's ID
5. Recalculates and updates `invoice.total_amount`

Update `createInvoice()` to:
- Accept an optional `include_therapy` flag and optional `therapy_session_ids` array
- When `include_therapy` is true and session IDs are provided, call `attachTherapySessions()` after creating the base invoice
- Manual line items (daycare charges) work exactly as before — completely independent

---

### Controller

#### [MODIFY] [InvoiceController.php](file:///c:/laragon/www/daycare-system/app/Http/Controllers/InvoiceController.php)

- Update `store()` validation to accept optional `therapy_session_ids` array
- Pass them through to `InvoiceService::createInvoice()`
- Add a new API endpoint `getBillableTherapySessions(Request $request)` that returns completed sessions for a child that haven't been billed yet (`whereDoesntHave('invoiceItem')`)

---

### Route

#### [MODIFY] [web.php](file:///c:/laragon/www/daycare-system/routes/web.php)

Add route for the new API endpoint:
```php
Route::get('/admin/invoices/get-therapy-sessions', [InvoiceController::class, 'getBillableTherapySessions'])
    ->name('admin.invoices.get-therapy-sessions');
```

---

### View (Create Invoice Form)

#### [MODIFY] [create.blade.php](file:///c:/laragon/www/daycare-system/resources/views/admin/invoices/create.blade.php)

After the child is selected, add a **"Include Therapy Sessions"** toggle/checkbox section:
- When toggled on, fetches billable (completed + not-yet-billed) therapy sessions for that child via AJAX
- Displays them as checkboxes with service name, date, therapist, and rate
- Selected sessions are submitted as `therapy_session_ids[]` hidden inputs
- Their amounts are included in the running total calculation
- Admin can still add manual line items alongside — or create a therapy-only invoice by not adding manual items, or a daycare-only invoice by leaving therapy unchecked

#### [MODIFY] [show.blade.php](file:///c:/laragon/www/daycare-system/resources/views/admin/invoices/show.blade.php)

- In the line items table, show a small badge (e.g. "Therapy") next to items that have a `therapy_session_id`, so admin can visually distinguish therapy charges from manual items

---

## Verification Plan

### Manual Verification
1. Run `php artisan migrate:fresh --seed` to verify migration changes
2. Create a therapy-only invoice — verify only completed unbilled sessions appear
3. Create a daycare-only invoice — verify manual items work as before
4. Create a mixed invoice — verify both therapy and manual items coexist
5. Try billing the same session twice — verify it's excluded from the billable list
6. Change a `therapy_services.session_rate` after billing — verify the invoice still shows the original rate
