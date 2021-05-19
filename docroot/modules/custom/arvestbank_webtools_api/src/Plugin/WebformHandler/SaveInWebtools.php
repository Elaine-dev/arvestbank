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
      'first_name_field_machine_name' => '',
      'last_name_field_machine_name' => '',
      'full_name_field_machine_name' => '',
      'email_field_machine_name' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

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
   *
   * @throws \DOMException
   */
  private function buildFormDataXml(string $formName, FormStateInterface $form_state) {

    // Get submitted form values.
    $submittedValues = $form_state->cleanValues()->getValues();

    // Get form storage.
    $storage = $form_state->getStorage();

    // Ensure original form page referer is set.
    if (!isset($storage['_arvestbank_webtools__referrer'])) {
      // We don't know the referer to the form page, so use null.
      $storage['_arvestbank_webtools__referrer'] = '';
    }

    // Get referer for this request which should be the page form is on.
    $formPageUrl = '';
    if (isset($_SERVER['HTTP_REFERER'])) {
      $formPageUrl = $_SERVER['HTTP_REFERER'];
    }

    // Base form data array.
    $requestData = [
      'meta' => [
        'meta' => [
          [
            'name'  => 'formName',
            'value' => $formName,
          ],
          [
            'name'  => 'datetime',
            'value' => date('Y-m-d\TH:i:s.v', time()),
          ],
          [
            'name'  => 'formUrl',
            'value' => $formPageUrl,
          ],
          [
            'name'  => 'referer',
            'value' => $storage['_arvestbank_webtools__referrer'],
          ],
          [
            'name'  => 'ipaddress',
            'value' => $_SERVER['REMOTE_ADDR'],
          ],
          [
            'name'  => 'userAgent',
            'value' => $_SERVER['HTTP_USER_AGENT'],
          ],
        ],
      ],
    ];

    // Track if we've settled on the name we'll send.
    $nameFieldAdded = FALSE;

    // If we have a first name field set.
    if (
      isset($this->configuration['first_name_field_machine_name'])
      && $this->configuration['first_name_field_machine_name']
      && isset($submittedValues[$this->configuration['first_name_field_machine_name']])
    ) {
      // Add first name to xml.
      $requestData['meta']['meta'][] = [
        'name'  => 'firstName',
        'value' => $submittedValues[$this->configuration['first_name_field_machine_name']],
      ];
      // Indicate not to use the full name functionality.
      $nameFieldAdded = TRUE;
    }

    // If we have a last name field set.
    if (
      isset($this->configuration['last_name_field_machine_name'])
      && $this->configuration['last_name_field_machine_name']
      && isset($submittedValues[$this->configuration['last_name_field_machine_name']])
    ) {
      // Add first name to xml.
      $requestData['meta']['meta'][] = [
        'name'  => 'lastName',
        'value' => $submittedValues[$this->configuration['last_name_field_machine_name']],
      ];
      // Indicate not to use the full name functionality.
      $nameFieldAdded = TRUE;
    }

    // If we have a full name field set and we didn't already set the name.
    if (
      isset($this->configuration['full_name_field_machine_name'])
      && $this->configuration['full_name_field_machine_name']
      && isset($submittedValues[$this->configuration['full_name_field_machine_name']])
      && !$nameFieldAdded
    ) {

      // Get array of words in the name field.
      $nameParts = explode(
        ' ',
        $submittedValues[$this->configuration['full_name_field_machine_name']]
      );

      // If there's only one word use that as the first name.
      if (count($nameParts) === 1) {
        // Add first name to xml.
        $requestData['meta']['meta'][] = [
          'name'  => 'firstName',
          'value' => array_pop($nameParts),
        ];
      }
      // If we have more than one name part.
      else {
        // Get the index of the final name part.
        $finalNamePartIndex = array_key_last($nameParts);
        // Use the final name part as the last name.
        $lastName = $nameParts[$finalNamePartIndex];
        // Remove the final name part from the name parts array.
        unset($nameParts[$finalNamePartIndex]);
        // Use the rest of the name parts as the first name.
        $firstName = implode(' ', $nameParts);
        // Add first name to xml.
        $requestData['meta']['meta'][] = [
          'name'  => 'firstName',
          'value' => $firstName,
        ];
        // Add last name to xml.
        $requestData['meta']['meta'][] = [
          'name'  => 'lastName',
          'value' => $lastName,
        ];
      }
    }

    // If we have an email field set.
    if (
      isset($this->configuration['email_field_machine_name'])
      && $this->configuration['email_field_machine_name']
      && isset($submittedValues[$this->configuration['email_field_machine_name']])
    ) {
      // Add first name to xml.
      $requestData['meta']['meta'][] = [
        'name'  => 'email',
        'value' => $submittedValues[$this->configuration['email_field_machine_name']],
      ];
    }

    // Add fields container to xml.
    $requestData['fields']['field'] = [];

    // Get fields on webform containing labels.
    $webformFields = $form_state->getFormObject()->getEntity()->getWebform()->getElementsInitializedAndFlattened();

    // Loop over submitted form fields.
    foreach ($submittedValues as $fieldName => $fieldValue) {

      // If this is a normal field.
      if (isset($webformFields[$fieldName]['#title'])) {
        // Get label for field.
        $fieldLabel = $webformFields[$fieldName]['#title'];
      }
      // If this is non-standard field (didn't encounter).
      else {
        // Make a label from the field name.
        $fieldLabel = ucwords(str_replace('_', ' ', $fieldName));
      }

      // Add form field to xml.
      $requestData['fields']['field'][] = [
        'name'  => $fieldName,
        'label' => $fieldLabel,
        'value' => $fieldValue,
      ];

    }

    $xmlConverterObject = new ArrayToXml($requestData, 'request');
    return $xmlConverterObject->prettify()->dropXmlDeclaration()->toXml();

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

    // Add field for first name field machine name.
    $form['first_name_field_machine_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('First Name Field'),
      '#description'   => $this->t('If this form contains a field for first name enter the machine name of that field here.<br/>This field needs to be sent to the API differently.'),
      '#default_value' => $this->configuration['first_name_field_machine_name'],
    ];

    // Add field for last name field machine name.
    $form['last_name_field_machine_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Last Name Field'),
      '#description'   => $this->t('If this form contains a field for last name enter the machine name of that field here.<br/>This field needs to be sent to the API differently.'),
      '#default_value' => $this->configuration['last_name_field_machine_name'],
    ];

    // Add field for full name field machine name.
    $form['full_name_field_machine_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Full Name Field'),
      '#description'   => $this->t('If this form contains a field for full name enter the machine name of that field here.<br/>This field needs to be sent to the API differently.'),
      '#default_value' => $this->configuration['full_name_field_machine_name'],
    ];

    // Add field for email field machine name.
    $form['email_field_machine_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Email Field'),
      '#description'   => $this->t('If this form contains a field for email enter the machine name of that field here.<br/>This field needs to be sent to the API differently.'),
      '#default_value' => $this->configuration['email_field_machine_name'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {

    // Run parent submit config form functionality.
    parent::submitConfigurationForm($form, $form_state);

    // Get the custom config fields for this webform handler.
    $customFields = $this->defaultConfiguration();
    // Loop over our custom fields.
    foreach ($customFields as $customFieldKey => $customFieldDefultValue) {
      // Save our configuration.
      $this->configuration[$customFieldKey] = $form_state->getValue($customFieldKey);
    }

  }

}
