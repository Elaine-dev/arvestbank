<?php

/**
 * @file Views alters.
 */

/**
 * Implements hook_views_data_alter().
 */
function arvestbank_phone_numbers_views_data_alter(array &$data) {
  $data['node']['phone_number_usage'] = [
    'title' => t('Phone Number Usage'),
    'field' => [
      'title' => t('Phone Number References'),
      'help' => t('Outputs links to content that references a token for a given phone number.'),
      'id' => 'phone_references',
    ],
  ];
}
