<?php

namespace Drupal\arvestbank_webtools_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for webtools api connectivity.
 */
class WebtoolsAdminForm extends ConfigFormBase {

  /**
   * Define the form value keys not to save to config.
   *
   * @var formValueKeysToIgnore
   */
  private $formValueKeysToIgnore = [
    'form_build_id',
    'form_id',
    'form_token',
    'op',
    'submit',
  ];

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get config directly so overrides show up.
    $config = \Drupal::config('arvestbank_webtools_api.settings');

    // Ping Federate Container.
    $form['ping-federate'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#title' => 'Ping Federate OAuth Connection Details',
      '#description' => 'Connection info for Ping Federate OAuth.<br/>Used to obtain bearer token for webtool api requests.<br/>Should be autoswitched in secrets.settings.php.',
    ];

    // OAuth Endpoint field.
    $form['ping-federate']['oauth_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OAUTH Endpoint'),
      '#description' => $this->t('The ping federate OAuth endpoint to connect to.'),
      '#default_value' => $config->get('oauth_endpoint'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // OAuth Client Id.
    $form['ping-federate']['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('The ping federate client id.'),
      '#default_value' => $config->get('client_id'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // OAuth Client Secret.
    $form['ping-federate']['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('The ping federate client secret.'),
      '#default_value' => $config->get('client_secret'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Webtools Container.
    $form['webtools'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#title' => 'Webtools API Connection Details',
      '#description' => 'Connection info for Arvest Bank\'s Webtools API<br/>Should be autoswitched in secrets.settings.php.',
    ];

    // IBM Client Id.
    $form['webtools']['ibm-client-id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IBM Client Id'),
      '#description' => $this->t('The value to include for ibm-client-id in webtools requests.'),
      '#default_value' => $config->get('ibm-client-id'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Webtools Domain.
    $form['webtools']['webtools-domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webtools Domain'),
      '#description' => $this->t('The domain at which webtool api endpoints can be reached.'),
      '#default_value' => $config->get('webtools-domain'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'arvestbank_webtools_api.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'arvestbank_webtools_admin_form';
  }

  /**
   * Note that we're not saving here config to be set in secrets.settings.php.
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Run parent submit.
    parent::submitForm($form, $form_state);

  }

}
