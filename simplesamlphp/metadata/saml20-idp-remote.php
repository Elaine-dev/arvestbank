<?php

/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */
$metadata['https://sts.windows.net/a191b859-c4b3-4424-a14d-323b150a30ab/'] = array (
  'entityid' => 'https://sts.windows.net/a191b859-c4b3-4424-a14d-323b150a30ab/',
  'contacts' =>
    array (
    ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' =>
    array (
      0 =>
        array (
          'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
          'Location' => 'https://login.microsoftonline.com/a191b859-c4b3-4424-a14d-323b150a30ab/saml2',
        ),
      1 =>
        array (
          'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
          'Location' => 'https://login.microsoftonline.com/a191b859-c4b3-4424-a14d-323b150a30ab/saml2',
        ),
    ),
  'SingleLogoutService' =>
    array (
      0 =>
        array (
          'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
          'Location' => 'https://login.microsoftonline.com/a191b859-c4b3-4424-a14d-323b150a30ab/saml2',
        ),
    ),
  'ArtifactResolutionService' =>
    array (
    ),
  'NameIDFormats' =>
    array (
    ),
  'keys' =>
    array (
      0 =>
        array (
          'encryption' => false,
          'signing' => true,
          'type' => 'X509Certificate',
          'X509Certificate' => 'MIIC8DCCAdigAwIBAgIQe97psunXFp9MThDYDGJTJDANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMTAzMjYxODI2NTlaFw0yNDAzMjYxODI2NTlaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvy+i+OH1HwtVW72nUKiWgNYyUoMWL3sISC7AYr4f3RqQFRKXDHznVTOYa9/OwWGGVPPmlsunGaKAi7Z9pYtTQ+M5nK+ndek3GHu6nQu22bsLy2GZ/7K2DKCashEEXNrwTLsqdbcxQ+oZQ5kvadwZgDI9VCF1xcW8AKsyC7kIy5C8du1Oay17PHFzNE4wtJdIg+zypOHiNDL+RjZlTjF2zXDpgKZJJdUINppI4zB6S2qpL5CBp3oB6LO1UkoVH9OMDCUCIn/GWqNLD3BoPEqepo6gXaayM6WW3/wKkdu7DyYe/0qsW60rUwMaYkqwv9NAfTkm99BuXC0N7gfvym4jRQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCnOX5mxFhLtipPhacbikxzhR1dbTM5AjpJYNfgjPflSO2s1A/fyJ9DrhctxNw3w/ppBpW2BgtizGtafExjenJ7WE3RorsDOtfptaxSSFWG94wpikSBbv/OBYfF65Ize0s1pAtWJUbGM7MyC0Nzm1XFGfnqkQyeZytq3GCUDqG4ZQXeVA3pcazDuzWB3dHaezd7vsjl1ahxcOnl0UJjbsFxLso01DFk0DLVLaFhUx7KiwiD5HjJFcSXFsDtYVae08CC0wiLV6zUdDQXeqoC27felcA5aLLa8MUQibg4zcAsi5BbJd8c0M1MIo0d8sdNQJ9qTK33ztk5gIM9Vk9x0Fb7',
        ),
    ),
);
