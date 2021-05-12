<?php

namespace Drupal\arvestbank_webtools_api\Services;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Drupal\arvestbank_webtools_api\Services\PingIdentityClient;

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
   * Constructs a new WebtoolsClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Helper to load load config.
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   * @param \Drupal\arvestbank_webtools_api\Services\PingIdentityClient $pingIdentityClient
   *  The ping identity client service.
   */
  public function __construct(ConfigFactory $configFactory, Client $httpClient, PingIdentityClient $pingIdentityClient) {
    // Store config factory for later use.
    $this->configFactory = $configFactory;
    // Load and store config for this module.
    $this->webtoolsConfig = $configFactory->get('arvestbank_webtools_api.settings');
    // Store http client.
    $this->httpClient = $httpClient;
    // Store ping identity client.
    $this->pingIdentityClient = $pingIdentityClient;
  }

  /**
   * Tests webtools connectivity.
   */
  public function testConnectivity() {

    // Determine endpoint.
    $requestEndpoint = $this->webtoolsConfig->get('webtools-domain') . '/external/Prod/CommonServices/v0';

    // Ensure we have a valid bearer token.
    $this->pingIdentityClient->ensureValidBearerToken();

    // Build Guzzle options array.
    $postRequestOptions = [
      'verify' => FALSE,
      'headers' => [
        'accept' => 'application/json',
        'authorization' => 'Bearer ' . \Drupal::state()->get('arvestbank_webtools_api__bearer_token'),
        'content-type' => 'application/json',
        'x-ibm-client-id' => $this->webtoolsConfig->get('ibm-client-id'),
      ],
    ];

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
    }

    $test = '';

  }

}
