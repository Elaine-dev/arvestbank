<?php

namespace Drupal\arvestbank_webtools_api\Services;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Ping Identity Client.
 */
class AzureTokenClient {

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
   * Constructs a new PingIdentityClient object.
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
    $this->webtoolsConfig = $configFactory->get('arvestbank_webtools_api.settings');
    // Store http client.
    $this->httpClient = $httpClient;
  }

  /**
   * Attempts to retrieve a bearer token from the Optimal Blue app.
   *
   * Stored in state var "arvestbank_webtools_api__optimal_blue_bearer_token".
   *
   * @return bool
   *   Indicates weather bearer token was successfully retrieved.
   */
  public function getNewOptimalBlueBearerToken() {

    // Add GET variables to oauth endpoint.
    $requestEndpoint = $this->webtoolsConfig->get('optimal_blue_oauth_endpoint');

    // Build Guzzle options array.
    $postRequestOptions = [
      'verify' => FALSE,
      'headers' => [
        'Content-type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'client_id' => $this->webtoolsConfig->get('optimal_blue_oauth_client_id'),
        'client_secret' => $this->webtoolsConfig->get('optimal_blue_oauth_client_secret'),
        'grant_type' => 'client_credentials',
        'resource' => 'https://marketplaceauth.optimalblue.com/d35ae893-2367-40b5-a9b4-bfab3acb7991',
      ],
    ];

    try {
      $requestTime = time();
      // Make request and get response body contents.
      $response = json_decode($this->httpClient->post(
        $requestEndpoint,
        $postRequestOptions
      )->getBody()->getContents());
    }
    catch (BadResponseException $e) {
      // Log an error.
      $responseCode = $e->getResponse()->getStatusCode();
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errorMessage = 'Error generating bearer token from Optimal Blue. Http response code:'
        . $responseCode . ' Response Body:' . $responseBody;
      \Drupal::logger('arvestbank_webtools_api')->error($errorMessage);
      // Return false.
      return FALSE;
    }


    // If the response has the info we need in the format we expect.
    if (
      isset($response->access_token)
      && $response->access_token
      && isset($response->token_type)
      && $response->token_type == 'Bearer'
      && isset($response->expires_in)
      && $response->expires_in
    ) {
      // Save bearer token.
      \Drupal::state()->set('arvestbank_webtools_api__optimal_blue_bearer_token', $response->access_token);
      // Save bearer token expiration time.
      $bearerTokenExpiration = $requestTime + $response->expires_in;
      \Drupal::state()->set('arvestbank_webtools_api__optimal_blue_bearer_token_expiration', $bearerTokenExpiration);
      // Return true indicating succesful retrieval of bearer token.
      return TRUE;
    }
    // If we got a malformed response.
    else {
      // Log an error indicating the response was malformed.
      $errorMessage = 'Received a malformed Optimal Blue bearer token request response. Response: '
        . print_r($response, 1);
      \Drupal::logger('arvestbank_webtools_api')->error($errorMessage);
      // Return false.
      return FALSE;
    }

  }

}
