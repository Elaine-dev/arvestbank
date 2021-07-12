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
   * Constructs a new WebtoolsClient object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Helper to load load config.
   * @param \GuzzleHttp\Client $httpClient
   *   A drupal http client factory.
   * @param \Drupal\arvestbank_webtools_api\Services\PingIdentityClient $pingIdentityClient
   *   The ping identity client service.
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
    // Store ping identity client.
    $this->azureTokenClient = $azureTokenClient;
  }

  /**
   * Tests webtools mortgage rates endpoint connectivity.
   */
  public function testMortgageRatesEndpointConnectivity(){

    // Define the test request we want to make.
    $endpoint = $this->webtoolsConfig->get('webtools-mortgage-rates-endpoint');
    $requestOptions = [
      RequestOptions::BODY => $this->getMortgageRatesJsonString(),
    ];

    // Return boolean for success.
    return is_string($this->makeRequest($endpoint, $requestOptions));

  }

  /**
   * Tests webtools deposit rates connectivity.
   */
  public function testDepositRatesEndpointConnectivity() {
    // Make request and return boolean for success.
    return $this->getDepositProductsWithRates() !== FALSE;
  }

  /**
   * Returns deposit products for the region "ARVEST BANK BENTON COUNTY" (101).
   *
   * Arvest Bank has stated that this is representative of all regions.
   */
  public function getDepositProductsWithRates() {

    // Define the test request we want to make.
    $endpoint = $this->webtoolsConfig->get('deposit-rates-endpoint');
    $requestOptions = [
      RequestOptions::JSON => [
        "Request" => [
          'RegionID'  => '101',
          'BranchID'  => 'ALL',
          'ProductID' => 'ALL',
          'Cursor'    => '0',
        ],
      ],
    ];

    // Make request.
    $responseBody = $this->makeRequest($endpoint, $requestOptions);

    // If we got a good response back.
    if (is_string($responseBody)) {
      $responseData = json_decode($responseBody);
      if (isset($responseData->Regions[0]->Products)) {
        return $responseData->Regions[0]->Products;
      }
    }

    // If we didn't return a good response already.
    return FALSE;

  }

  /**
   * Tests webtools form api connectivity.
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
   * @param string $method
   *   Get or post.
   *
   * @return bool|string
   *   Response or FALSE.
   */
  public function makeRequest(string $endpoint, array $requestOptions, string $method = 'post') {

    // If the passed endpoint isn't a full url.
    if (strpos($endpoint, 'http') === FALSE) {
      // Use the webtools domain and endpoint.
      $requestEndpoint = $this->webtoolsConfig->get('webtools-domain') . $endpoint;
    }
    // If the passed endpoint is a full url.
    else {
      // Use the passed url as is.
      $requestEndpoint = $endpoint;
    }

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

      if ($method == 'post') {
        // Make request and get response body contents.
        $response = $this->httpClient->post(
          $requestEndpoint,
          $postRequestOptions
        )->getBody()->getContents();
      }
      elseif ($method == 'get') {
        // Make request and get response body contents.
        $response = $this->httpClient->get(
          $requestEndpoint,
          $postRequestOptions
        )->getBody()->getContents();
      }

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

  /**
   * Returns a static json string we were given to request.
   *
   * @return string
   *   A static json string we were given to request.
   */
  public function getMortgageRatesJsonString() {
    return '{"BorrowerInformation":{"AssetDocumentation":"Verified","DebtToIncomeRatio":"40.0","PledgedAssets":"false","Citizenship":"USCitizen","EmploymentDocumentation":"Verified","FICO":"740","FirstName":"John","LastName":"Doe","FirstTimeHomeBuyer":"true","IncomeDocumentation":"Verified","MonthlyIncome":"6000.00","MonthsReserves":"24","SelfEmployed":"true","WaiveEscrows":"false","MortgageLatesX30":"0","MortgageLatesX60":"0","MortgageLatesX90":"0","MortgageLatesX120":"0","MortgageLatesRolling":"0","Bankruptcy":"Never","Foreclosure":"Never","DisclosureDate":"2017-02-06T22:39:44.908-06:00","ApplicationDate":"2017-02-06T22:39:44.908-06:00"},"LoanInformation":{"LoanPurpose":"Purchase","LienType":"First","AmortizationTypes":["Fixed"],"ARMFixedTerms":["FiveYear"],"AutomatedUnderwritingSystem":"NotSpecified","BorrowerPaidMI":"Yes","Buydown":"None","CashOutAmount":"0.0","DesiredLockPeriod":"0","DesiredPrice":"0.0","DesiredRate":"0.0","ExpandedApprovalLevel":"NotApplicable","FHACaseAssigned":"2017-02-06T06:00:00Z","FHACaseEndorsement":"2017-02-06T06:00:00Z","InterestOnly":"false","BaseLoanAmount":"225000.00","CalculateTotalLoanAmount":"true","SecondLienAmount":"0.0","HELOCDrawnAmount":"0.0","HELOCLineAmount":"0.0","LoanTerms":["ThirtyYear"],"LoanType":"Conforming","PrepaymentPenalty":"None","IncludeLOCompensationInPricing":"YesLenderPaid","CurrentServicer":"NotApplicable"},"PropertyInformation":{"AppraisedValue":"225000.00","Occupancy":"PrimaryResidence","PropertyStreetAddress":"123 ABC Dr.","County":"Collin","State":"TX","ZipCode":"75024","PropertyType":"SingleFamily","CorporateRelocation":"false","SalesPrice":"225000.00","NumberOfStories":"1","NumberOfUnits":"OneUnit","Construction":"false"},"RepresentativeFICO":"740","LoanLevelDebtToIncomeRatio":"40.0","CustomerInternalId":"OBSearch","ExemptFromVAFundingFee":"true","VeteranType":"Active Duty","VADisability":"true","VaFirstTimeUse":"true"}';
  }

}
