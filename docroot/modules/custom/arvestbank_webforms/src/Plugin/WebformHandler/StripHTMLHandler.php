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
 *   id = "arvestbank_webforms_striphtml",
 *   label = @Translation("Strip HTML"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Strip HTML from form values specifed by fieldnames."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class StripHTMLHandler extends WebformHandlerBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'striphtml_fieldnames' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $form['striphtml_fieldnames'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Strip HTML Fieldnames'),
      '#default_value' => $this->configuration['striphtml_fieldnames'],
      '#description' => 'Enter one field name per line.',
      '#required' => TRUE,
    ];

    return $this->setSettingsParents($form);

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['striphtml_fieldnames'] = $form_state->getValue('striphtml_fieldnames');
  }

  /**
   * {@inheritdoc}
   *
   * @todo can't get this to show up on the settings summary.
   */
  public function getSummary() {
    $settings = $this->configuration['striphtml_fieldnames'];
    return [
      '#settings' => $settings,
    ] + parent::getSummary();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->stripHtml($form_state);
  }

  /**
   * Strips html from specified fieldname values.
   */
  private function stripHtml(FormStateInterface $formState) {

    // Grab the list of fieldnames as an array.
    $fieldnames = explode("\r\n", $this->configuration['striphtml_fieldnames']);

    // Loop through the fieldnames and strip HTML from the values.
    foreach ($fieldnames as $fieldname) {
      $value = !empty($formState->getValue($fieldname)) ? Html::escape($formState->getValue($fieldname)) : NULL;
      if (!empty($value)) {
        $value_clean = strip_tags($value);
        if ($value_clean != $value) {
          $formState->setValue($fieldname, $value_clean);
        }
      }
    }

  }

}
