<?php

namespace Drupal\arvestbank_webtools_api\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Spatie\ArrayToXml\ArrayToXml;

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

    // Build the form data to save.
    $xmlData = $this->buildFormDataXml($this->configuration['webtools_form_name'], $form_state);

    // Build the Guzzle request options.
    $requestOptions = [
      RequestOptions::JSON => [
        'FormName' => $this->configuration['webtools_form_name'],
        'XMLString' => $xmlData,
      ],
    ];

    // Make request.
    $this->webtoolsClient->makeFormSaveRequest($requestOptions);

  }

  /**
   * Populates an xml request from a submitted form state.
   *
   * @param string $formName
   *   The webtools api form name.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state containing submitted values.
   *
   * @return string
   *   An xml string to be sent for storage in the webtool endpoint.
   */
  private function buildFormDataXml(string $formName, FormStateInterface $form_state) {

    // Base form data array.
    $requestData = [
      'meta' => [
        'meta' => [
          'name'  => 'formName',
          'value' => $formName,
        ],
      ],
    ];

    // Return xml string derived from our array.
    return ArrayToXml::convert($requestData, 'request');

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
