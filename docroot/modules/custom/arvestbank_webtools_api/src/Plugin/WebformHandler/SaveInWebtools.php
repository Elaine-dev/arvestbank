<?php

namespace Drupal\arvestbank_webtools_api\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "Save Submissions In Webtools",
 *   label = @Translation("Save Submissions In Webtools"),
 *   category = @Translation("Save Submissions In Webtools"),
 *   description = @Translation("Sends the submission data to the Arvestbank webtools server."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class SaveInWebtools extends WebformHandlerBase {

  /**
   * The webtools client for making Webtools API requests.
   *
   * @var \Drupal\arvestbank_webtools_api\Services\WebtoolsClient
   */
  protected $webtoolsClient;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Run default webform handler create function.
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    // Add our webtools client service.
    $instance->webtoolsClient = $container->get('arvestbank_webtools_api.webtools_client');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'webtools_form_name' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    // Define the test request we want to make.
    $endpoint = $this->webtoolsConfig->get('webtools-form-endpoint');
    $requestOptions = [
      RequestOptions::JSON => [
        'FormName' => 'Test Form',
        'XMLString' => '<request><meta><meta><name>formName</name><value>test</value></meta></meta></request>',
      ],
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Add field for webtools form name.
    $form['webtools_form_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Webtools Form Name'),
      '#description'   => $this->t('The form name to store the submission data with in webtools ie "auto_warranty_quote".'),
      '#default_value' => $this->configuration['webtools_form_name'],
      '#required'      => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Run parent submit config form functionality.
    parent::submitConfigurationForm($form, $form_state);
    // Save our configuration.
    $this->configuration['webtools_form_name'] = $form_state->getValue('webtools_form_name');
  }

}
