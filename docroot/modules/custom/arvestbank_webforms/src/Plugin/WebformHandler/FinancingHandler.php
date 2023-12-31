<?php

namespace Drupal\arvestbank_webforms\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
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
  public function alterForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    // Show the financing options on the last page of the form,
    // this way the form is never submitted.  We are replacing the markup
    // with the financing options generated markup.
    if ($form_state->getStorage()['current_page'] === 'financing_options') {
      $financing_options_markup = self::financingOptionsMarkup($webform_submission);
      $form['elements']['financing_options']['financing_options_markup']['#markup'] = $financing_options_markup;
      // Remove the submit button.
      unset($form['actions']['submit']);
    }

  }

  /**
   * {@inheritdoc}
   */
  private function financingOptionsMarkup(WebformSubmissionInterface $webform_submission) {

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

    // Grab the webform data.
    $webform_data = $webform_submission->getData();

    // Standard route - anything but "other" on the first question.
    if ($webform_data['loan_type_1'] != 'other') {
      // These are the webform fieldnames used to determine the result.
      $fieldnames = [
        'loan_type_1',
        'loan_home_imp_type',
        'loan_secure',
        'loan_veh_type',
        'loan_secure_home',
      ];
    }
    // Else, they selected "Other Purposes" for "like money for...".
    else {
      // Webform fieldnames used to determine the result on the "other" path.
      $fieldnames = [
        'loan_type_1',
        'loan_other_purpose',
        'loan_secure',
        'loan_veh_type',
        'loan_secure_home',
      ];
    }

    // Holder for the form values.
    $values = [];

    // Loop through fieldnames appending to the values array.
    foreach ($fieldnames as $fieldname) {
      $values[] = $webform_submission->getData()[$fieldname];
    }

    // This will be the key for the correct results.
    $results_key = implode('|', $values);

    // This will return an array with $node_mapping keys.
    if ($webform_data['loan_type_1'] != 'other') {
      $results = \Drupal::service('arvestbank_webforms.financing_options')
        ->financingOptions($results_key);
    }
    else {
      $results = \Drupal::service('arvestbank_webforms.financing_options')
        ->financingOptionsOther($results_key);
    }

    // Holder for the rendered financing options, to be added to build later.
    $financing = [];

    // If we have results, add embedded views to an array.
    if (!empty($results)) {
      $results_ar = explode('|', $results);
      $featured = FALSE;
      foreach ($results_ar as $option) {
        if (array_key_exists($option, $node_mapping)) {
          $display_id = 'embed';
          if (!$featured) {
            $display_id = 'embed_featured';
            $featured = TRUE;
          }
          $financing[] = views_embed_view('financing', $display_id, $node_mapping[$option]);
        }
      }
    }

    // If there are financing options add to the build array.
    if (count($financing)) {

      // Beginning Markup.
      $build[] = [
        '#type' => 'markup',
        '#markup' => '<h2>Your Recommendation</h2><p>Based on your answers, this should be what you need!</p>',
      ];

      for ($i = 0; $i <= 2; $i++) {
        if (!empty($financing[$i])) {
          // If this is the second option add the "more options" text.
          if ($i == 1) {
            if (!empty($financing[2])) {
              $option_text = "two options";
            }
            else {
              $option_text = "option";
            }
            $message = "<p>We understand each customer's financial situation and financing needs are different. With that in mind, we have an additional $option_text that could also help you.</p>";
            $build[] = [
              '#type' => 'markup',
              '#markup' => $message,
            ];
          }
          // Add embeded view to the build array.
          $build[] = $financing[$i];
        }
      }

    }

    else {

      // Set the default response for no options.
      $message = "<p>Thank you for your interest in this loan from Arvest! Unfortunately, we do not take online applications for this type of loan at this time. To learn more about the loan and apply, please call (866) 952-9523 or visit your local branch.</p>";

      // This is the only exception.  If others are found make this a switch.
      if ($results_key === 'other|buy_home|||') {
        $message = "<p>We’re glad you’re interested in buying  a home with Arvest. Visit <a href=\"/homeloan\">arvest.com/homeloan</a> to apply or learn more from our mortgage division.</p>";
      }

      // Sorry no results.
      $build[] = [
        '#type' => 'markup',
        '#markup' => $message,
      ];

    }

    // Return the markup of the rendered build array.
    return render($build);

  }

}
