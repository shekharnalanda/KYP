<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mantra MIS100V2 Iris Connector
    |--------------------------------------------------------------------------
    |
    | Keep this token only in the production environment and the trusted
    | Windows connector. Never place it in browser JavaScript or screenshots.
    |
    */
    'connector_token' => env('KYP_IRIS_CONNECTOR_TOKEN'),

    /*
    | A 120-minute KYP lab session is completed after 110 verified minutes.
    */
    'minimum_session_minutes' => (int) env('KYP_IRIS_MINIMUM_SESSION_MINUTES', 110),
];
