{{--
    Payment field for a therapy session: take it from one of the child's packages
    (default: oldest package with sessions left — FIFO) or pay per session on an invoice.
    Params: $selected (string|null — null = no previous choice), $excludeId (session being edited),
            $currentPackage (ChildTherapyPackage|null), $locked (bool — session already billed on an invoice)
--}}
@php
    $locked = $locked ?? false;
    $currentPackage = $currentPackage ?? null;
@endphp
<div class="col-12">
    <label class="form-label fw-semibold"><i class="bi bi-box-seam me-1 text-primary"></i>Payment</label>
    @if ($locked)
        <input type="hidden" name="child_therapy_package_id" value="">
        <div class="form-control bg-light">Pay per session — already billed on an invoice</div>
        <small class="text-muted">This session is on an invoice, so it can't be moved onto a package.</small>
    @else
        <select name="child_therapy_package_id" id="packageChoice"
                class="form-select @error('child_therapy_package_id') is-invalid @enderror"
                data-url="{{ route('admin.therapy-sessions.package-options') }}"
                data-exclude="{{ $excludeId ?? '' }}"
                @if (!is_null($selected)) data-selected="{{ $selected }}" @endif
                @if ($currentPackage) data-current-id="{{ $currentPackage->id }}"
                    data-current-label="{{ ($currentPackage->package->name ?? 'Package #' . $currentPackage->id) . ' · bought ' . $currentPackage->purchased_at->format('d M Y') . ' (current)' }}" @endif>
            <option value="">Pay per session (bill on invoice)</option>
        </select>
        @error('child_therapy_package_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted" id="packageHint">Select the child, service and date to see the child's packages.</small>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sel = document.getElementById('packageChoice');
                const form = sel.form;
                const child = form.querySelector('[name="child_id"]');
                const service = form.querySelector('[name="therapy_service_id"]');
                const date = form.querySelector('[name="session_date"]');
                const hint = document.getElementById('packageHint');
                let wanted = sel.hasAttribute('data-selected') ? sel.dataset.selected : null; // old input / saved value
                let userPicked = wanted !== null;

                sel.addEventListener('change', () => { userPicked = true; });

                function option(value, label) {
                    const o = document.createElement('option');
                    o.value = value; o.textContent = label;
                    sel.appendChild(o);
                }

                function load() {
                    const keep = wanted !== null ? wanted : sel.value;
                    wanted = null;
                    if (!child.value || !service.value) {
                        sel.innerHTML = '';
                        option('', 'Pay per session (bill on invoice)');
                        hint.textContent = "Select the child, service and date to see the child's packages.";
                        return;
                    }
                    const qs = new URLSearchParams({ child_id: child.value, therapy_service_id: service.value, session_date: date.value || '' });
                    if (sel.dataset.exclude) qs.set('exclude', sel.dataset.exclude);

                    fetch(sel.dataset.url + '?' + qs, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.ok ? r.json() : { packages: [] })
                        .catch(() => ({ packages: [] }))
                        .then(({ packages }) => {
                            sel.innerHTML = '';
                            if (packages.length) {
                                option('auto', 'Use package — oldest first (' + packages[0].name + ', ' + packages[0].available + ' left)');
                                packages.forEach(p => option(p.id, p.name + ' · bought ' + p.purchased_at
                                    + (p.expires_at ? ' · expires ' + p.expires_at : '') + ' · ' + p.available + ' of ' + p.included + ' left'));
                            }
                            // Keep the package a saved session is already on, even if it no longer shows (e.g. expired since)
                            const cur = sel.dataset.currentId;
                            if (cur && !packages.some(p => String(p.id) === cur)) option(cur, sel.dataset.currentLabel);
                            option('', 'Pay per session (bill on invoice)');

                            const values = Array.from(sel.options).map(o => o.value);
                            sel.value = userPicked && values.includes(String(keep)) ? String(keep) : (packages.length ? 'auto' : '');
                            hint.textContent = packages.length
                                ? packages.length + ' package(s) with sessions left for this therapy — the oldest is used first unless you pick one.'
                                : 'No active package with sessions left for this therapy on that date — the session will be billed on an invoice.';
                        });
                }

                [child, service, date].forEach(el => el && el.addEventListener('change', load));
                load();
            });
        </script>
    @endif
</div>
