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
];
