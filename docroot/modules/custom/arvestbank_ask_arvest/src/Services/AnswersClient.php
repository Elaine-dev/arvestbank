<?php

namespace Drupal\arvestbank_ask_arvest\Services;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;

/**
 * A service to facilitate "[24]7 AI" Answers API integration.
 */
class AnswersClient {

  /**
   * Helper to load config.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Config for this module.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $askArvestConfig;

  /**
   * Http client used to call APIs.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new AnswersClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Helper to load load config.
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   */
  public function __construct(ConfigFactory $configFactory, Client $httpClient) {

    // Store config factory for later use.
    $this->configFactory = $configFactory;

    // Load and store config for this module.
    $this->askArvestConfig = $configFactory->get('arvestbank_ask_arvest.settings');

    // Store http client.
    $this->httpClient = $httpClient;

  }

  /**
   * Gets the general rest endpoint from config.
   */
  public function getGeneralRestEndpoint() {

    return $this->askArvestConfig->get('general_rest_api_endpoint');

  }

  /**
   * Gets suggestions from the intellisuggest endpoint.
   *
   * Uses the REST API "IntelliSuggest" endpoint.
   *
   * @param string $userInput
   *   String to provide autocomplete suggestions for.
   *
   * @return array
   *   Result from endpoint json decoded.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function intellisuggestQuery(string $userInput) {

    // Sanitize user input for use in url.
    $userInput = filter_var($userInput, FILTER_SANITIZE_URL);

    // Get intellisuggest endpoint.
    $intellisuggestEndpoint = $this->askArvestConfig->get('intellisuggest_endpoint');

    // Make request.
    try {
      // Make request.
      // The interfaceID and requestType could be made config if needed.
      $request = $this->httpClient->request(
        'GET',
        $intellisuggestEndpoint . '?interfaceID=2&requestType=8&term=' . $userInput
      );
      // Get response body.
      $data = $request->getBody();
      // Decode and return the json.
      return Json::decode($data);
    }
    catch (RequestException $e) {
      // Log error.
      \Drupal::logger('arvestbank_ask_arvest')->error($e->getMessage());
      // Return empty array.
      return [];
    }

  }

  /**
   * Queries the SOAP "ask" endpoint for answers.
   *
   * @param string $question
   *   The question to get answers for.
   */
  public function askQuery(string $question) {

    // Get soap endpoint from config.
    $intelliresponseEndpoint = $this->askArvestConfig->get('intelliresponse_soap_endpoint');

    // Create Soap Client.
    $soapClient = new \nusoap_client($intelliresponseEndpoint);
    $soapClient->soap_defencoding = 'UTF-8';
    $soapClient->decode_utf8 = FALSE;

    // Create object containing arguments.
    $requestArguments = [
      'interfaceId'        => 2,
      'question' => $question,
      'channelId' => '',
      'typeId' => '',
      'sessionId' => '',
    ];

    // Make request and return result.
    return $soapClient->call('ask', $requestArguments, '/com/intelliresponse/search/user', '');

  }

}
