<?php
# This currently doesn't do anything. Can we select the Pdf Converter
# implementation using an environment variable somehow?
return [
    'default' => env('PDF_BACKEND', 'mpdf'),

    'mpdf' => ['class' => App\Pdf\MPdfConverter::class],

    'dompdf' => ['class' => App\Pdf\DompdfConverter::class]
];
