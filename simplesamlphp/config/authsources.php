<?php

$config = [
  'admin' => [
    'core:AdminPassword',
  ],
  'default-sp' => [
    'saml:SP',
    'privatekey' => '../certs/saml.pem',
    'certificate' => '../certs/saml.crt',
    'entityid' => 'https://sts.windows.net/a191b859-c4b3-4424-a14d-323b150a30ab/',
    'idp' => 'https://sts.windows.net/a191b859-c4b3-4424-a14d-323b150a30ab/',
  ],

  'prod-sp' => [
    'saml:SP',
    'privatekey' => '../certs/drupal-prod.pem',
    'certificate' => '../certs/drupal-prod.crt',
    'entityid' => 'https://sts.windows.net/2750d34d-88ad-4360-ad53-7966db844c3c/',
    'idp' => 'https://sts.windows.net/2750d34d-88ad-4360-ad53-7966db844c3c/',
  ],
];
