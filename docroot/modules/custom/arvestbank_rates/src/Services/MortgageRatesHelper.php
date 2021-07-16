<?php

namespace Drupal\arvestbank_rates\Services;

use Drupal\Core\File\FileSystem;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\arvestbank_webtools_api\Services\WebtoolsClient;
use Drupal\Core\Config\ConfigFactory;
use Drupal\arvestbank_revisions\TokenReferenceHelper;

/**
 * Service providing functions relating to deposit rates.
 */
class MortgageRatesHelper {

  /**
   * Defines the rate "product" field keys and our name for them as the value.
   *
   * @var array
   */
  private static $rateFields = [
    'Rate' => 'rate',
    'AnnualPercentageRate' => 'apr',
    'TotalPayment' => 'payment',
  ];

  /**
   * The absolute file path to the dir with request data.
   *
   * @var string
   */
  private $requestDataDirectory;

  /**
   * The webtools Client to make requests.
   *
   * @var Drupal\arvestbank_webtools_api\Services\WebtoolsClient
   */
  private $webtoolsClient;

  /**
   * Editable rates config used for tokens.
   *
   * @var \Drupal\Core\Config\Config
   */
  private $ratesConfig;

  /**
   * A service to facilitate getting references to tokens.
   *
   * @var Drupal\arvestbank_revisions\TokenReferenceHelper
   */
  private $tokenReferenceHelper;

  /**
   * MortgageRatesHelper constructor.
   */
  public function __construct(FileSystem $fileSystem, ModuleHandler $moduleHandler, WebtoolsClient $webtoolsClient, ConfigFactory $configFactory, TokenReferenceHelper $tokenReferenceHelper) {
    // Get root-relative module path.
    $relativeModulePath = $moduleHandler->getModule('arvestbank_rates')->getPath();
    // Set the request data directory variable.
    $this->requestDataDirectory = $fileSystem->realpath($relativeModulePath) . '/mortgageRequestData';
    // Store the webtools client for later use.
    $this->webtoolsClient = $webtoolsClient;
    // Store editable config for arvestbank rates.
    $this->ratesConfig = $configFactory->getEditable('arvestbank_rates.settings');
    // Store token reference helper service.
    $this->tokenReferenceHelper = $tokenReferenceHelper;
  }

  /**
   * Fetches mortgage info, parses it, and stores resulting tokens.
   */
  public function updateMortgageRates() {

    // Get the requests we know how to make.
    $possibleRequests = $this->getPossibleRequests();

    // Instantiate array to store the rates that changed.
    $changedTokens = [];

    // Loop over the requests we know how to make.
    foreach ($possibleRequests as $requestDataFileName) {
      // Get contents of the data file.
      $jsonRequestData = file_get_contents($this->requestDataDirectory . '/' . $requestDataFileName);
      // Make request.
      $response = $this->webtoolsClient->makeMortgageRatesRequest($jsonRequestData);
      // If the response was a success.
      if ($response) {
        // Json decode the response.
        $response = json_decode($response);
        // If we have "products" (loan types) returned.
        if (isset($response->Products) && count($response->Products)) {
          // Loop over products.
          foreach ($response->Products as $product) {
            // If this is the product we're looking for.
            if ($product->ProductCode . '.json' == $requestDataFileName) {
              // Loop over the attributes we want to make tokens for.
              foreach ($this::$rateFields as $rateFieldSourceIndex => $rateFieldDrupalName) {
                // Get and format product name.
                $formatted_product_name = str_replace(' ', '_', strtolower($product->ProductName));
                // Determine our token name.
                $tokenName = 'mortgage_rates__' . $formatted_product_name . '__' . $rateFieldDrupalName;
                // If this value doesn't match the current one.
                if ($this->ratesConfig->get($tokenName) !== $product->$rateFieldSourceIndex) {
                  // Add to our list of changed tokens.
                  $changedTokens[] = '[arvestbank_rates:' . $tokenName . ']';
                  // Save value to config.
                  $this->ratesConfig->set($tokenName, $product->$rateFieldSourceIndex)->save();
                }
              }
            }
          }

          // If we had tokens change.
          if (count($changedTokens)) {
            $this->tokenReferenceHelper->createRevisionsForReferencingNodes($changedTokens, 'Programatic revision to record mortgage rate change(s).');
          }

        }
      }
    }


  }

  /**
   * Test connectivity to the mortgage rates API.
   */
  public function testConnectivity() {

    // Get possible requests.
    $possibleRequests = $this->getPossibleRequests();

    // If we have requests we can make.
    if (count($possibleRequests)) {
      // Get contents of first request data file.
      $jsonRequestData = file_get_contents($this->requestDataDirectory . '/' . $possibleRequests[0]);
      // Make request with contents of that first file.
      return $this->webtoolsClient->makeMortgageRatesRequest($jsonRequestData);
    }

    // Return FALSE if we didn't return TRUE yet.
    return FALSE;

  }

  /**
   * Returns the possible requests based on contents of mortgageRequestData dir.
   */
  public function getPossibleRequests() {

    // Get request data directory contents.
    $requestDataDirectoryContents = scandir($this->requestDataDirectory);

    // Instantiate array for our possible requests.
    $possibleRequests = [];

    // If there are any files in our request data directory.
    if (count($requestDataDirectoryContents) > 2) {
      // Loop over (non "." and "..") directory contents.
      for ($i = 2; $i < count($requestDataDirectoryContents); $i++) {
        // Add file to our list of possible requests.
        $possibleRequests[] = $requestDataDirectoryContents[$i];
      }
    }

    return $possibleRequests;

  }

}
