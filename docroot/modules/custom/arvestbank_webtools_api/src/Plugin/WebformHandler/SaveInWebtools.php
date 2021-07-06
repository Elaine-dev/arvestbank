<?php

namespace Drupal\arvestbank_webtools_api\Plugin\WebformHandler;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Render\Markup;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Spatie\ArrayToXml\ArrayToXml;
use Drupal\taxonomy\Entity\Term;

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

    // To avoid site studio induced multiple submissions check global variable.
    if (!isset($GLOBALS['sent_webform_submission_this_request'])) {

      // Indicate we're sending the submission now.
      $GLOBALS['sent_webform_submission_this_request'] = TRUE;

      // Build the form data to save.
      $xmlData
        = $this->buildFormDataXml($this->configuration['webtools_form_name'],
        $form_state);

      // Build the Guzzle request options.
      $requestOptions = [
        RequestOptions::JSON => [
          'FormName' => $this->configuration['webtools_form_name'],
          'XMLString' => $xmlData,
        ],
      ];

      // Make request.
      $requestSuccess = $this->webtoolsClient->makeFormSaveRequest($requestOptions);

      // If sending the webform failed.
      if (!$requestSuccess) {
        // Alert user of error, error logged in makeFormSaveRequest().
        $this->messenger()->addError('There was an error processing your submission, please contact support.', FALSE);
      }

    }

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
    $submittedValues = $this->getSubmittedValues($formName, $form_state);

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
            'value' => $this->formNameAlter($formName, $submittedValues),
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
    // Get exploded name parts just in case.
    $explodedNameParts = [
      'first_name_field_machine_name' => explode('.',$this->configuration['first_name_field_machine_name']),
      'last_name_field_machine_name' => explode('.',$this->configuration['last_name_field_machine_name']),
    ];

    // If we have a first name field set.
    if (
      isset($this->configuration['first_name_field_machine_name'])
      && $this->configuration['first_name_field_machine_name']
      && (
        isset($submittedValues[$this->configuration['first_name_field_machine_name']])
        || (
          count($explodedNameParts['first_name_field_machine_name']) == 2
          && isset($submittedValues[$explodedNameParts['first_name_field_machine_name'][0]][$explodedNameParts['first_name_field_machine_name'][1]])
        )
      )
    ) {

      // Non nested first name value.
      if (isset($submittedValues[$this->configuration['first_name_field_machine_name']])) {
        $firstNameValue = $submittedValues[$this->configuration['first_name_field_machine_name']];
      }
      // Nested first name value.
      else {
        $firstNameValue = $submittedValues[$explodedNameParts['first_name_field_machine_name'][0]][$explodedNameParts['first_name_field_machine_name'][1]];
      }

      // Add first name to xml.
      $requestData['meta']['meta'][] = [
        'name'  => 'firstName',
        'value' => $firstNameValue,
      ];
      // Indicate not to use the full name functionality.
      $nameFieldAdded = TRUE;
    }

    // If we have a last name field set.
    if (
      isset($this->configuration['last_name_field_machine_name'])
      && $this->configuration['last_name_field_machine_name']
      && (
        isset($submittedValues[$this->configuration['last_name_field_machine_name']])
        || (
          count($explodedNameParts['last_name_field_machine_name']) == 2
          && isset($submittedValues[$explodedNameParts['last_name_field_machine_name'][0]][$explodedNameParts['last_name_field_machine_name'][1]])
        )
      )
    ) {

      // Non nested last name value.
      if (isset($submittedValues[$this->configuration['last_name_field_machine_name']])) {
        $lastNameValue = $submittedValues[$this->configuration['last_name_field_machine_name']];
      }
      // Nested last name value.
      else {
        $lastNameValue = $submittedValues[$explodedNameParts['last_name_field_machine_name'][0]][$explodedNameParts['last_name_field_machine_name'][1]];
      }

      // Add first name to xml.
      $requestData['meta']['meta'][] = [
        'name'  => 'lastName',
        'value' => $lastNameValue,
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

      // Deal with multivalue fields.
      if (
        is_array($fieldValue)
        && is_numeric(array_keys($fieldValue)[0])
      ) {
        $fieldValue = ['value' => $fieldValue];
      }

      // Add form field to xml.
      $requestData['fields']['field'][] = [
        'name'  => $fieldName,
        'label' => $fieldLabel,
        'value' => $fieldValue,
      ];

    }

    // If branch location is available.
    if (
      isset($submittedValues['branch_location'])
      && $submittedValues['branch_location']
    ) {

      // Define properties of our location.
      $locationTermProperties = [
        'name' => $submittedValues['branch_location'],
        'vid'  => 'branch_location',
      ];

      // Load location term by name.
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($locationTermProperties);
      $term = reset($term);

      // Get legacy region id from location term.
      $legacyRegionIdFieldValue = $term->get('field_legacy_region_id')->getValue();

      // If we have a legacy region id to use.
      if (isset($legacyRegionIdFieldValue[0]['value'])) {
        $requestData['meta']['meta'][] = [
          'name'  => 'regionid',
          'value' => $legacyRegionIdFieldValue[0]['value'],
        ];
      }
    }

    $xmlConverterObject = new ArrayToXml($requestData, 'request');
    return $xmlConverterObject->dropXmlDeclaration()->toXml();

  }

  /**
   * Gets submitted values and titles from reference fields.
   *
   * Currently only webform_term_select is supported as that's all we use.
   *
   * Others are entity_autocomplete, webform_entity_checkboxes,
   * webform_entity_radios, webform_entity_select, webform_term_checkboxes.
   *
   * @param string $formName
   *   The webtools api form name.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state containing submitted values.
   *
   * @return array
   *   The submitted values.
   */
  private function getSubmittedValues(string $formName, FormStateInterface $form_state) {

    // Get submitted form values.
    $submittedValues = $form_state->cleanValues()->getValues();

    // Get form fields.
    $formFields = $form_state->getCompleteForm()['elements'];

    // Loop over form fields looking for reference fields.
    foreach ($formFields as $formFieldKey => $formFieldInfo) {

      // If we found a term reference field we have a value set for.
      if (
        isset($formFieldInfo['#webform_plugin_id'])
        && $formFieldInfo['#webform_plugin_id'] == 'webform_term_select'
        && isset($submittedValues[$formFieldKey])
        && is_numeric($submittedValues[$formFieldKey])
      ) {
        // Get term name.
        $termName = Term::load($submittedValues[$formFieldKey])->getName();
        // Replace tid in field value with term name.
        $submittedValues[$formFieldKey] = $termName;
      }

    }

    return $submittedValues;
  }

  /**
   * Allows altering of a webtools form name allowing for dynamic values.
   *
   * @param string $formName
   *   The form name.
   * @param array $submittedValues
   *   The submitted values.
   *
   * @return string
   *   Returns the altered form name.
   */
  private function formNameAlter(string $formName, array $submittedValues) {

    // If this is the small business connect form.
    if ($formName == 'smbus_connect') {
      // Look for state abbreviation in branch location option title.
      preg_match_all('/^([^ ]*) -/', $submittedValues['branch_location'], $matches);
      // If we found a state abbreviation.
      if (count($matches) == 2 && isset($matches[1][0])) {
        // Return the form name.
        return 'smbus_connect_' . strtolower($matches[1][0]);
      }
      // If the state abbreviation is malformed.
      else {
        // Log error.
        \Drupal::logger('arvestbank_webtools_api')->error('Branch location name found to be non-standard in form submission, defaulting to Arkansas (smbus_connect_ar).  Given non-standard branch location name: ' . $submittedValues['branch_location']);
        // Default to Arkansas.
        return 'smbus_connect_ar';
      }
    }
    // For all other forms return form name unaltered.
    else {
      return $formName;
    }

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
