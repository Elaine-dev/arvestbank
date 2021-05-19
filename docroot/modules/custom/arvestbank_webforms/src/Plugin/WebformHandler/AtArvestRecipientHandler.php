<?php

namespace Drupal\arvestbank_webforms\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "arvestbank_webforms_atarvestrecipient",
 *   label = @Translation("@Arvest.com Recipient"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Adds @arvest.com to the recipient formfield."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class AtArvestRecipientHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'atarvest_recipient_fieldname' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $form['atarvest_recipient_fieldname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('@Arvest.com Recipient Fieldname'),
      '#default_value' => $this->configuration['atarvest_recipient_fieldname'],
      '#description' => 'Enter the fieldname of the recipient field.',
      '#required' => TRUE,
    ];

    return $this->setSettingsParents($form);

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['atarvest_recipient_fieldname'] = $form_state->getValue('atarvest_recipient_fieldname');
  }

  /**
   * {@inheritdoc}
   *
   * @todo can't get this to show up on the settings summary.
   */
  public function getSummary() {
    $settings = $this->configuration['atarvest_recipient_fieldname'];
    return [
      '#settings' => $settings,
    ] + parent::getSummary();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->addArvestDomain($form_state);
  }

  /**
   * Appends @arvest.com to the recipient.
   */
  private function addArvestDomain(FormStateInterface $formState) {

    // Grab the fieldname that is the recipient.
    $fieldname = explode("\r\n", $this->configuration['atarvest_recipient_fieldname']);

    $value = !empty($formState->getValue($fieldname)) ? Html::escape($formState->getValue($fieldname)) : NULL;
    if (!empty($value)) {
      // Make sure it's not a full email address - just grab the first part.
      $value_ar = explode('@', $value);
      $value_clean = array_shift($value_ar);
      $value_clean .= '@arvest.com';
      // Set the value with the domain.
      $formState->setValue($fieldname, $value_clean);
    }

  }

}
