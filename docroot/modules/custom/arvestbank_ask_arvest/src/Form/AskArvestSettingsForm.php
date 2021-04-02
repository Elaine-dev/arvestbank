<?php

namespace Drupal\arvestbank_ask_arvest\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure ask arvest settings for this site.
 */
class AskArvestSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'arvestbank_ask_arvest.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'arvestbank_ask_arvest_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get configuration for this modul.
    $config = $this->config(static::SETTINGS);

    // Field for IntelliSuggest Endpoint.
    $form['intellisuggest_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IntelliSuggest Endpoint'),
      '#default_value' => $config->get('intellisuggest_endpoint'),
      '#description' => 'Enter the endpoint at which the Answers API IntelliSuggest endpoint can be reached.',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Retrieve and update configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('intellisuggest_endpoint', $form_state->getValue('intellisuggest_endpoint'))
      ->save();

    parent::submitForm($form, $form_state);

  }

}
