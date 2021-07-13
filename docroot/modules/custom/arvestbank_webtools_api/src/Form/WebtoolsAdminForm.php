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
      '#description' => 'Connection info for Ping Federate OAuth.<br/>Used to obtain bearer token for webtool form api requests.<br/>Should be autoswitched in secrets.settings.php.',
    ];

    // Add bearer token info to ping federate container description.
    if (\Drupal::state()->get('arvestbank_webtools_api__bearer_token')) {
      $form['ping-federate']['#description'] .= '<br/>Current bearer token: <b>'
        . \Drupal::state()->get('arvestbank_webtools_api__bearer_token')
        . '</b>';
      if (
        \Drupal::state()->get('arvestbank_webtools_api__bearer_token_expiration')
        && \Drupal::state()->get('arvestbank_webtools_api__bearer_token_expiration') > time()
      ) {
        $form['ping-federate']['#description'] .= '(not expired)';
      }
      else {
        $form['ping-federate']['#description'] .= '(expired)';
      }
    }

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

    // Test Ping Identity button.
    $form['ping-federate']['test_ping_identity_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Ping Identity Config'),
      '#submit' => [[$this, 'testPingIdentity']],
    ];

    // IBM Client Id.
    $form['webtools']['ibm-client-id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IBM Client Id'),
      '#description' => $this->t('The value to include for ibm-client-id in webtools requests.'),
      '#default_value' => $config->get('ibm-client-id'),
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
      '#description' => $this->t('The domain at which webtools api endpoints can be reached.'),
      '#default_value' => $config->get('webtools-domain'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Webtools Form Endpoint.
    $form['webtools']['webtools-form-endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webtools Form Endpoint'),
      '#description' => $this->t('The endpoint at which webtools form api can be reached.'),
      '#default_value' => $config->get('webtools-form-endpoint'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Webtools Deposit Rates Endpoint.
    $form['webtools']['deposit-rates-endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Deposit Rates Endpoint'),
      '#description' => $this->t('The endpoint at which the webtools deposit rates api can be reached.'),
      '#default_value' => $config->get('deposit-rates-endpoint'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Webtools Mortgage Rates Endpoint.
    $form['webtools']['webtools-mortgage-rates-endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mortgage Rates Endpoint'),
      '#description' => $this->t('The endpoint at which webtools form api can be reached.'),
      '#default_value' => $config->get('webtools-mortgage-rates-endpoint'),
      '#attributes' => ['disabled' => 'disabled'],
    ];

    // Copied from ConfigFormBase->buildForm.
    $form['#theme'] = 'system_config_form';

    // Actions.
    $form['actions']['#type'] = 'actions';

    // Test webtools api button.
    $form['webtools']['test_webtools_form_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Form API Config'),
      '#submit' => [[$this, 'testFormEndpoint']],
    ];

    // Test webtools api button.
    $form['webtools']['test_deposit_rates_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Deposit Rates API Config'),
      '#submit' => [[$this, 'testDepositRates']],
    ];

    // Test Webtools Mortgage Rates API button.
    $form['webtools']['test_webtools_mortgage_rates_api_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Mortgage Rates API Config'),
      '#submit' => [[$this, 'testMortgageRates']],
    ];

    // Coppied from ConfigFormBase->buildForm.
    $form['#theme'] = 'system_config_form';

    // Return form.
    return $form;
  }

  /**
   * Form submit function to test deposit rates config.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function testDepositRates(array &$form, FormStateInterface $form_state) {

    // Get webtools client.
    $webtoolsClient = \Drupal::service('arvestbank_webtools_api.webtools_client');

    // Test connectivity.
    $requestSuccess = $webtoolsClient->testDepositRatesEndpointConnectivity();

    // If the test was successful.
    if ($requestSuccess) {
      $this->messenger()->addMessage('Successfully connected to the deposit rates endpoint.');
    }
    else {
      $this->messenger()->addError('Could not connect to the deposit rates endpoint.');
    }

  }

  /**
   * Form submit function to test webtools mortgage rates endpoint config.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function testMortgageRates(array &$form, FormStateInterface $form_state) {

    // Get mortgage rates helper.
    $mortgageRatesHelper = \Drupal::service('arvestbank_rates.mortgage_rates_helper');

    // Test connectivity.
    $requestSuccess = $mortgageRatesHelper->testConnectivity();

    // If the test was successfull.
    if ($requestSuccess) {
      $this->messenger()->addMessage('Successfully connected to the webtools MortgageRates endpoint.');
    }
    else {
      $this->messenger()->addError('Could not connect to the webtools MortgageRates endpoint.');
    }

  }

  /**
   * Form submit function to test webtools form endpoint config.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function testFormEndpoint(array &$form, FormStateInterface $form_state) {

    // Get webtools client.
    $webtoolsClient = \Drupal::service('arvestbank_webtools_api.webtools_client');

    // Test connectivity.
    $requestSuccess = $webtoolsClient->testFormEndpointConnectivity();

    // If the test was successfull.
    if ($requestSuccess) {
      $this->messenger()->addMessage('Successfully connected to the webtools SaveFormData endpoint.');
    }
    else {
      $this->messenger()->addError('Could not connect to the webtools SaveFormData endpoint.');
    }

  }

  /**
   * Submit function to test ping identity config.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function testPingIdentity(array &$form, FormStateInterface $form_state) {

    // Get Ping Identity Client.
    $pingIdentityClient = \Drupal::service('arvestbank_webtools_api.ping_identity_client');

    // Attempt to generate a bearer token from ping identity.
    $requestSuccess = $pingIdentityClient->getNewBearerToken();

    // If the test was successful.
    if ($requestSuccess) {
      $this->messenger()->addMessage('Successfully generated a ping identity bearer token.');
    }
    else {
      $this->messenger()->addError('Could not connect to ping identity.');
    }

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

}
