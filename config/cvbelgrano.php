<?php

/*
 * Datos propios de CV Belgrano Web.
 * Se leen con config('cvbelgrano.url_google_play').
 */

return [

    // Enlace de la app en Google Play (canal principal de descarga).
    'url_google_play' => 'https://play.google.com/store/apps/details?id=com.cvbelgrano.app',

    // Enlace directo al APK. Vacío = el botón se muestra sin acción (igual que en la landing).
    'url_apk' => env('CVBELGRANO_URL_APK', ''),

];
