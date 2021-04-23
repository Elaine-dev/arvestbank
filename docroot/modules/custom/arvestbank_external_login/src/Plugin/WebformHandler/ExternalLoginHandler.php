<?php

namespace Drupal\arvestbank_external_login\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "External Login Handler",
 *   label = @Translation("External Login Handler"),
 *   category = @Translation("External Login Handler"),
 *   description = @Translation("Redirects a user to their chosen service."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class ExternalLoginHandler extends WebformHandlerBase {

  /**
   * Defines the "Login Select" options that require special processing.
   *
   * @var array
   */
  private $nonStandardSelections = [
    'arvest_online_banking',
    'cash_manager',
    'mortgage',
    'investments_wealth',
  ];

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    // Get "Login Select" value.
    $loginSelectValue = $form_state->getValue('login_select');

    // If this is a standard redirect.
    if (!in_array($loginSelectValue, $this->nonStandardSelections)) {
      $response = new TrustedRedirectResponse(Url::fromUri($loginSelectValue)->toString());
      $form_state->setResponse($response);
    }
    // If this is a non-standard redirect.
    else {
      $this->handleNonstandardSelection($form_state, $loginSelectValue);
    }

  }

  /**
   * Handles non-standard login select values.
   *
   * Currently both custom non-standard actions are handled in js.
   *
   * This could be used for warnings or for functionality that does require php.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param string $loginSelectValue
   *   The selected login.
   */
  private function handleNonstandardSelection(FormStateInterface &$form_state, string $loginSelectValue) {

    // If user selected "Cash Manager".
    if ($loginSelectValue == 'cash_manager') {
      // Should not reach this, the action of the form should be changed to go
      // directly to a cash manager endpoint with js when this is selected.
    }
    // If user seleted "Investments - Wealth".
    elseif ($loginSelectValue == 'investments_wealth') {
      // Should not reach this, the action of the form should be changed to go
      // directly to a sso endpoint with js when this is selected.
    }

  }

}
