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

    // Coppied from ConfigFormBase->buildForm.
    $form['#theme'] = 'system_config_form';

    // Actions.
    $form['actions']['#type'] = 'actions';

    // Test Ping Identity button.
    $form['actions']['test_ping_identity_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Ping Identity Config by Gerating New Bearer Token'),
      '#submit' => [[$this, 'testPingIdentity']],
    ];

    // Test webtools api button.
    $form['actions']['test_webtools_config'] = [
      '#type' => 'submit',
      '#value' => t('Test Webtools API Config'),
      '#submit' => [[$this, 'testWebtools']],
    ];

    // Return form.
    return $form;
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

    // If the test was successfull.
    if ($requestSuccess) {
      $this->messenger()->addMessage('Successfully generated a ping identity bearer token.');
    }
    else {
      $this->messenger()->addError('Could not connect to ping identity.');
    }

  }

  /**
   * Form submit function to test webtools config.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function testWebtools(array &$form, FormStateInterface $form_state) {

    // Get webtools client.
    $webtoolsClient = \Drupal::service('arvestbank_webtools_api.webtools_client');

    // Test connectivity.
    $webtoolsClient->testConnectivity();

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
