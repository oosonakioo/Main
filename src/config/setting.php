<?php

return [
    // ck path and google
    'path-elfinder' => '',
    'google-analytics' => '',
    'google-tagmanager' => '',

    // export path
    'template-path' => 'uploads/template/',
    'excel-path' => 'uploads/excel/',
    'pdf-path' => 'uploads/pdf/',
    'image-path' => 'uploads/image/',
    'linefeed' => '{NEWLINE}',

    // encrypt
    'encrypt-pass' => 'paymentpassword',
    'encrypt-type' => 'AES-128-ECB',

    // payment
    // Test 'payment-url'         => 'https://testpaygate.ktc.co.th/scs/eng/merchandize/payment/payForm.jsp',
    'payment-url' => 'https://paygate.ktc.co.th/ktc/eng/merchandize/payment/payForm.jsp',
    'payment-merchantid' => '094300001',
    'payment-currcode' => '764',
    'payment-successurl' => 'payment/result/success',
    'payment-failurl' => 'payment/result/fail',
    'payment-cancelurl' => 'payment/result/cancel',
    'payment-paytype' => 'N',
    'payment-lang' => 'E',
    'payment-txttype' => 'Retail',

    // MENU IN BACKEND
    'contents' => [

    ],
    'catcontents' => [

    ],
    'lists' => [
    ],
];
