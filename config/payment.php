<?php

return [
    'bank_transfer' => [
        'bank_name' => env('BANK_TRANSFER_BANK_NAME', 'MB Bank'),
        'bank_code' => env('BANK_TRANSFER_BANK_CODE', 'MB'),
        'account_number' => env('BANK_TRANSFER_ACCOUNT_NUMBER', '0123456789'),
        'account_name' => env('BANK_TRANSFER_ACCOUNT_NAME', 'HLA WIFI SHOP'),
    ],
];
