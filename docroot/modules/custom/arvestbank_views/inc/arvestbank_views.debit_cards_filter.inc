<?php

/**
 * @file
 * Contains arvestbank_views/inc/arvestbank_views.debit_cards_filter.inc.
 */

/**
 * Adjustments to the debit cards filter for zip code search.
 *
 * @param array $form
 *   Views exposed form.
 */
function arvestbank_views_debit_cards_filter(array &$form) {

  // Hide the geolocation fields from the exposed form.
  // We need to alter them later so can't set to #access=FALSE in this case.
  $form['center']['coordinates']['#attributes'] = ['style' => 'display:none;'];
  $form['field_geolocation_proximity']['#type'] = 'hidden';
  $form['field_geolocation_proximity']['#attributes'] = ['style' => 'display:none;'];

  // Capture the zip code in the form.
  $form['geolocation_zip'] = [
    '#type' => 'textfield',
    '#title' => 'School Zip Code',
    '#size' => 10,
    '#maxlength' => 5,
    '#placeholder' => 'Enter Zip Code',
  ];

  return $form;

}
