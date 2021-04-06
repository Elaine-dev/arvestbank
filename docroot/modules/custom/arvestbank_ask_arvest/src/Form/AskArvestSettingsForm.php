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

    // Get configuration for this module.
    $config = $this->config(static::SETTINGS);

    // Field for IntelliSuggest Endpoint.
    $form['intellisuggest_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IntelliSuggest Endpoint'),
      '#default_value' => $config->get('intellisuggest_endpoint'),
      '#description' => 'Enter the endpoint at which the Answers API IntelliSuggest endpoint can be reached.<br/>This is generally https://[client name].intelliresponse.com/IntelliSuggest.',
    ];

    // Field for Intelliresponse Soap Endpoint.
    $form['intelliresponse_soap_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IntelliResponse Soap Endpoint'),
      '#default_value' => $config->get('intelliresponse_soap_endpoint'),
      '#description' => 'Enter the soap endpoint at which the IntelliResponse can be reached.<br/>This is generally http://[client name].intelliresponse.com/ws/ir-user/Ask?wsdl.',
    ];

    // Field for General Rest API Endpoint.
    $form['general_rest_api_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('General [24]7.ai "Answers" Rest API Endpoint'),
      '#default_value' => $config->get('general_rest_api_endpoint'),
      '#description' => 'Enter the endpoint at which the general [24]7.ai Answers REST API can be reached.<br/>This is generally http://[client name].intelliresponse.com/json/.',
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
      ->set('intelliresponse_soap_endpoint', $form_state->getValue('intelliresponse_soap_endpoint'))
      ->set('general_rest_api_endpoint', $form_state->getValue('general_rest_api_endpoint'))
      ->save();

    parent::submitForm($form, $form_state);

  }

}
