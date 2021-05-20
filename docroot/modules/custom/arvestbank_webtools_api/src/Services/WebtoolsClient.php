<?php

namespace Drupal\arvestbank_webtools_api\Services;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Drupal\arvestbank_webtools_api\Services\PingIdentityClient;
use Drupal\arvestbank_webtools_api\Services\AzureTokenClient;
use GuzzleHttp\RequestOptions;

/**
 * Ping Identity Client.
 */
class WebtoolsClient {

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
  protected $webtoolsConfig;

  /**
   * Http client used to call APIs.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * PingIdentity client.
   *
   * @var \Drupal\arvestbank_webtools_api\Services\PingIdentityClient
   */
  protected $pingIdentityClient;

  /**
   * Azure Token client.
   *
   * @var \Drupal\arvestbank_webtools_api\Services\PingIdentityClient
   */
  protected $azureTokenClient;

  /**
   * Constructs a new WebtoolsClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Helper to load load config.
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   * @param \Drupal\arvestbank_webtools_api\Services\PingIdentityClient $pingIdentityClient
   *   The ping identity client service.
   * @param \Drupal\arvestbank_webtools_api\Services\AzureTokenClient $azureTokenClient
   *   The azure token client service.
   */
  public function __construct(ConfigFactory $configFactory, Client $httpClient, PingIdentityClient $pingIdentityClient, AzureTokenClient $azureTokenClient) {
    // Store config factory for later use.
    $this->configFactory = $configFactory;
    // Load and store config for this module.
    $this->webtoolsConfig = $configFactory->get('arvestbank_webtools_api.settings');
    // Store http client.
    $this->httpClient = $httpClient;
    // Store ping identity client.
    $this->pingIdentityClient = $pingIdentityClient;
    // Store ping identity client.
    $this->azureTokenClient = $azureTokenClient;
  }

  /**
   * Tests webtools mortgage rates endpoint connectivity.
   */
  public function testMortgageRatesEndpointConnectivity() {

    // Define the test request we want to make.
    $endpoint = $this->webtoolsConfig->get('webtools-mortgage-rates-endpoint');
    $requestOptions = [
      'headers' => [
        'Accept' => 'application/json',
        'Authorization' => '{OATH token: clientid:client secret}'
      ],
    ];

    // Return boolean for success.
    return is_string($this->makeRequest($endpoint, $requestOptions));

  }

  /**
   * Tests webtools form endpoint connectivity.
   */
  public function testFormEndpointConnectivity() {

    // Define the test request we want to make.
    $endpoint = $this->webtoolsConfig->get('webtools-form-endpoint');
    $requestOptions = [
      RequestOptions::JSON => [
        'FormName' => 'Test Form',
        'XMLString' => '<request><meta><meta><name>formName</name><value>test</value></meta></meta></request>',
      ],
    ];

    // Return boolean for success.
    return is_string($this->makeRequest($endpoint, $requestOptions));

  }

  /**
   * Makes a request to save form data.
   *
   * @param array $requestOptions
   *   The Guzzle request options.
   *
   * @return bool|string
   *   Response or FALSE.
   */
  public function makeFormSaveRequest(array $requestOptions) {
    // Get the endpoint to send form data to.
    $endpoint = $this->webtoolsConfig->get('webtools-form-endpoint');
    // Make request and return response.
    return $this->makeRequest($endpoint, $requestOptions);
  }

  /**
   * Helper function to make a webtools request, deals with authentication.
   *
   * @param string $endpoint
   *   The endpoint to make a request to.
   * @param array $requestOptions
   *   Request (Guzzle) options to be merged into the authentication ones.
   *
   * @return bool|string
   *   Response or FALSE.
   */
  public function makeRequest(string $endpoint, array $requestOptions) {

    // Determine endpoint.
    $requestEndpoint = $this->webtoolsConfig->get('webtools-domain') . $endpoint;

    // Ensure we have a valid bearer token.
    $this->pingIdentityClient->ensureValidBearerToken();

    // Build Guzzle options array.
    $postRequestOptions = [
      'verify' => FALSE,
      'headers' => [
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . \Drupal::state()->get('arvestbank_webtools_api__bearer_token'),
        'Content-Type' => 'application/json',
        'X-IBM-Client-Id' => $this->webtoolsConfig->get('ibm-client-id'),
      ],
    ];

    // Merge the passed request options with the authorization ones.
    $postRequestOptions = array_merge($postRequestOptions, $requestOptions);

    try {
      // Make request and get response body contents.
      $response = $this->httpClient->post(
        $requestEndpoint,
        $postRequestOptions
      )->getBody()->getContents();
    }
    catch (BadResponseException $e) {
      $responseCode = $e->getResponse()->getStatusCode();
      $responseBody = $e->getResponse()->getBody()->getContents();
      // Log error.
      \Drupal::logger('arvestbank_webtools_api')->error(
        'Failed to connect to arvest webtools endpoint '
          . $requestEndpoint . '. Server responded with code '
          . $responseCode . ' and the message: <pre>'
          . $responseBody . '</pre>'
      );
      // Indicate we failed to connect to the webtool endpoint.
      return FALSE;
    }

    // Return response.
    return $response;

  }

}
