<?php

namespace Drupal\arvestbank_webforms\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "arvestbank_webforms_financing",
 *   label = @Translation("Financing Options"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Displays financing options based off of form selections."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class FinancingHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {

    // We will return a build array.
    $build = [];

    // Mapping of financing option "machine" name with node ID.
    // Keys map to the $financing_options_mapping array (service).
    $node_mapping = [
      // Auto Loan.
      'auto' => 13401,
      // CD Installment.
      'cd_install' => 13441,
      // CD Line of Credit.
      'cd_loc' => 13446,
      // Home Equity Adjustable Rate Mortgage.
      'he_arm' => 13426,
      // Home Equity Line of Credit.
      'heloc' => 13431,
      // Home Equity Loan.
      'he_loan' => 13421,
      // Marine or Boat.
      'marine_boat' => 13406,
      // Motorcycle.
      'motorcycle' => 13411,
      // Personal Line of Credit.
      'ploc' => 13451,
      // Powersports.
      'atv_utv' => 13456,
      // RV.
      'rv' => 13416,
      // Unsecured.
      'unsecured' => 13461,
      // Unsecured Home Improvement.
      'unsecured_home_imp' => 13436,
    ];

    // These are the webform fieldnames used to determine the result.
    $fieldnames = [
      'loan_type_1',
      'loan_home_imp_type',
      'loan_secure',
      'loan_veh_type',
      'loan_secure_home',
    ];

    // Holder for the form values.
    $values = [];

    // Loop through fieldnames appending to the values array.
    foreach ($fieldnames as $fieldname) {
      $values[] = $webform_submission->getData()[$fieldname];
    }

    // This will be the key for the correct results.
    $results_key = implode('|', $values);

    // This will return an array with $node_mapping keys.
    $results = \Drupal::service('arvestbank_webforms.financing_options')->financingOptions($results_key);

    // Holder for the rendered financing options, to be added to build later.
    $financing = [];

    if (!empty($results)) {
      $results_ar = explode('|', $results);
      foreach ($results_ar as $option) {
        if (array_key_exists($option, $node_mapping)) {
          $financing[] = views_embed_view('financing', 'default', $node_mapping[$option]);
        }
      }
    }

    // If there are finanainc options add to the build array.
    if (count($financing)) {

      // Beginning Markup.
      $build[] = [
        '#type' => 'markup',
        '#markup' => 'WE HAVE LOANS FOR YOU THIS IS THE BEST',
      ];

      for ($i = 0; $i <= 2; $i++) {
        if (!empty($financing[$i])) {
          // If this is the second option add the "more options" text.
          if ($i == 1) {
            $build[] = [
              '#type' => 'markup',
              '#markup' => 'HERE ARE OTHER OPTIONS',
            ];
          }
          // Add embeded view to the build array.
          $build[] = $financing[$i];
        }
      }

    }

    else {

      // Sorry no results.
      $build[] = [
        '#type' => 'markup',
        '#markup' => 'SORRY NO LOANS AVAILABLE',
      ];

    }

    // Overwrite the confirmation message with this nice build array.
    $this->getWebform()->setSettingOverride('confirmation_message', render($build));

    
    // https://www.drupal.org/docs/8/modules/webform/webform-cookbook/how-to-programmatically-skip-pages-in-wizard-forms

  }

}
