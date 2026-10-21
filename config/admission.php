<?php

/*
|--------------------------------------------------------------------------
| Admission fee
|--------------------------------------------------------------------------
| One-time fee charged on a child's FIRST programme enrolment (once per lifetime).
| It is billed on an "admission invoice" together with the first month's tuition.
| Set ADMISSION_FEE=0 to switch admission invoices off.
*/

return [

    // Fixed admission fee in BDT
    'fee' => (float) env('ADMISSION_FEE', 0),

    // Invoice line label
    'label' => env('ADMISSION_FEE_LABEL', 'Admission Fee (one-time)'),

    // Days the parent has to pay the admission invoice
    'due_days' => (int) env('ADMISSION_FEE_DUE_DAYS', 7),

];
