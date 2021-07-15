<?php
return [
    'modelName' => \App\SatWSRequestStatus::class,
    'useEnv' => false,
    'fiel_certificate' => env('FIEL_CERTIFICATE', ''),
    'fiel_privatekey' => env('FIEL_PRIVATEKEY', ''),
    'fiel_passphrase' => env('FIEL_PASSPHRASE', ''),
    'cfdiModel' => \App\Models\Cfdi::class,
];
