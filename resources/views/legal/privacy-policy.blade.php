@extends('legal.layout')

@section('title', 'Privacy Policy')

@php
    $orgName = $org['name'];
    $contactLine = collect([
        $org['email'] ? 'Email: ' . $org['email'] : null,
        $org['phone'] ? 'Phone: ' . $org['phone'] : null,
        $org['address'] ? 'Address: ' . $org['address'] : null,
    ])->filter();
@endphp

@section('content')
    <div class="pp-card pp-legal">
        <div class="pp-page-title">Privacy Policy</div>
        <div class="pp-page-sub mb-0">Last updated {{ $updated->format('j F Y') }}</div>
    </div>

    <div class="pp-card pp-legal">
        <h2>1. Who we are</h2>
        <p>
            This policy explains how <strong>{{ $orgName }}</strong> (“we”, “us”) collects, uses and protects personal
            information in the {{ $orgName }} parent app and parent website (together, the “App”). The App lets parents
            and guardians follow their child’s day at our daycare: attendance, meals, naps, class photos, therapy
            sessions, fees and notices.
        </p>
        <p>
            The App is intended for parents and legal guardians aged 18 or over. It is not directed at children.
        </p>

        <h2>2. Information we collect</h2>
        <p><strong>a. Your account</strong> — provided by you when you register, or entered by our office when you enroll:</p>
        <ul>
            <li>Name, email address and mobile number</li>
            <li>Password (stored only in encrypted, hashed form)</li>
            <li>Optionally: national ID number, occupation, city, address and a profile photo</li>
            <li>Your relationship to the child and whether you are allowed to pick them up</li>
        </ul>

        <p><strong>b. Information about your child</strong> — provided by you or by our office:</p>
        <ul>
            <li>Name, date of birth and photo</li>
            <li>Allergies and medical notes</li>
            <li>Emergency contact details and authorised pick-up persons</li>
            <li>Documents you give us, such as a birth certificate, custody agreement or medical forms</li>
            <li>Program enrollment details</li>
        </ul>

        <p><strong>c. Records created by our staff</strong> while caring for your child:</p>
        <ul>
            <li>Attendance (check-in and check-out times)</li>
            <li>Daily care logs: meals, naps, diaper changes, medication given, incidents and activities</li>
            <li>Therapy sessions, therapy packages and session status</li>
            <li>Photos and videos of class activities</li>
        </ul>

        <p><strong>d. Billing information</strong></p>
        <ul>
            <li>Invoices, amounts due, payments made, payment method and payment reference numbers</li>
            <li>
                If online payment is offered, it is processed by a licensed payment provider. We receive the payment
                result and reference, and at most a masked card number — never your full card details or PIN.
            </li>
        </ul>

        <p><strong>e. Technical information</strong></p>
        <ul>
            <li>A session cookie that keeps you signed in</li>
            <li>IP address, browser/device type and sign-in times, used for security</li>
            <li>
                The App stores design files (styles, icons, fonts) on your device so it loads quickly and can show an
                offline page. It does not store your child’s information on your device.
            </li>
        </ul>

        <p><strong>f. Child safety QR tag</strong> (only if your child has one)</p>
        <ul>
            <li>
                Anyone who scans the tag sees your child’s first name and the guardian / emergency phone numbers so they
                can call you.
            </li>
            <li>
                The person who found your child can send us their name, phone number, a message and, if they allow it,
                their location.
            </li>
        </ul>

        <h2>3. How we use information</h2>
        <ul>
            <li>To care for your child and keep you informed about their day</li>
            <li>To manage enrollment, therapy sessions, invoices and payments</li>
            <li>To share notices and announcements from the daycare</li>
            <li>To keep children safe (authorised pick-up, emergency contacts, safety tags)</li>
            <li>To keep the App secure and to meet our legal and accounting obligations</li>
        </ul>
        <p>We do <strong>not</strong> sell personal information, show advertising, or use your data for advertising profiles.</p>

        <h2>4. Who can see your information</h2>
        <ul>
            <li><strong>Our staff</strong>, limited to what their role needs.</li>
            <li>
                <strong>Other parents in the same program</strong> can see class activity photos and videos, which may
                include your child. If you do not want your child to appear in shared photos, please tell the office.
            </li>
            <li><strong>Other guardians linked to the same child</strong> can see that child’s information.</li>
            <li>
                <strong>Service providers</strong> that help us run the App: our hosting provider; Google Fonts (your
                device downloads fonts from Google, which receives your IP address); and, when online payment is used, the
                payment provider.
            </li>
            <li><strong>Authorities</strong>, when the law requires it or to protect a child’s safety.</li>
        </ul>

        <h2>5. How long we keep information</h2>
        <ul>
            <li>Account information is kept while your account is active.</li>
            <li>
                When you ask us to delete your account, we remove your personal details and sign-in access within
                {{ $deletionDays }} days (see section 7).
            </li>
            <li>
                Invoices and payment records are kept as long as accounting and tax law requires. Childcare records
                (attendance, care logs, therapy and incident records) are kept by the daycare as part of its care records
                for as long as required by law or regulation.
            </li>
        </ul>

        <h2>6. Security</h2>
        <p>
            We use encrypted connections (HTTPS), store passwords only in hashed form, and limit staff access by role.
            No system is completely secure, so please keep your password private and sign out on shared devices.
        </p>

        <h2>7. Your choices and rights</h2>
        <ul>
            <li><strong>Access and correction:</strong> you can see your child’s information in the App, and ask the office to correct anything that is wrong.</li>
            <li><strong>Photos:</strong> you can ask us not to include your child in shared class photos.</li>
            <li>
                <strong>Delete your account:</strong> in the App go to <em>More → Delete account</em>, or use
                <a href="{{ route('account-deletion.show') }}">{{ route('account-deletion.show') }}</a>. We will remove
                your name, contact details, ID number, address, photo and sign-in access, and unlink your account from
                your child. Records described in section 5 that we must keep are retained without your contact details.
            </li>
        </ul>

        <h2>8. Changes to this policy</h2>
        <p>
            We may update this policy. The “Last updated” date at the top shows when it last changed. Important changes
            will also be announced in the App.
        </p>

        <h2>9. Contact us</h2>
        <p class="mb-1">Questions about privacy or your data? Contact {{ $orgName }}:</p>
        @if ($contactLine->isNotEmpty())
            <ul>
                @foreach ($contactLine as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        @else
            <p>Please contact the daycare office.</p>
        @endif
    </div>
@endsection
