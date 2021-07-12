<?php

namespace Drupal\arvestbank_rates\Services;

use Drupal\arvestbank_webtools_api\Services\WebtoolsClient;
use Drupal\Core\Config\ConfigFactory;
use Drupal\arvestbank_revisions\TokenReferenceHelper;

/**
 * Service providing functions relating to deposit rates.
 */
class DepositRatesHelper {

  /**
   * Webtools client to fetch data from webtools API.
   *
   * @var \Drupal\arvestbank_webtools_api\Services\WebtoolsClient
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
   * Stores updated rate values before storing in config.
   *
   * @var array
   */
  private $newRates = [];

  /**
   * Constructor.
   *
   * @param \Drupal\arvestbank_webtools_api\Services\WebtoolsClient $webtoolsClient
   *   Injected webtools client for API connectivity.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Injected config factory.
   * @param \Drupal\arvestbank_revisions\TokenReferenceHelper $tokenReferenceHelper
   *   A service to facilitate getting references to tokens.
   */
  public function __construct(WebtoolsClient $webtoolsClient, ConfigFactory $configFactory, TokenReferenceHelper $tokenReferenceHelper) {
    $this->webtoolsClient = $webtoolsClient;
    $this->ratesConfig = $configFactory->getEditable('arvestbank_rates.settings');
    $this->tokenReferenceHelper = $tokenReferenceHelper;
  }

  /**
   * Fetches from API, stores, triggers revisions on referencing content.
   */
  public function updateDepositRates() {

    // Make request for deposit rates.
    $depositProducts = $this->webtoolsClient->getDepositProductsWithRates();

    // Loop over deposit products.
    foreach ($depositProducts as $depositProduct) {

      // Get formatted product name used for config name for child attributes.
      $formattedProductName = str_replace(' ', '_', strtolower($depositProduct->ProductDescription));

      // Loop over product attributes.
      foreach ($depositProduct as $attributeName => $attributeValue) {
        // If this is not the ProductDescription attribute.
        if ($attributeName != 'ProductDescription') {

          // Get formatted attribute name.
          $formattedAttributeName = str_replace(' ', '_', strtolower($attributeName));

          // Determine config name for this attribute.
          $attributeConfigName = 'deposit_rates__' . $formattedProductName . '__' . $formattedAttributeName;

          // Store new config name and value.
          $this->newRates[$attributeConfigName] = $attributeValue;

        }
      }

    }

    // Get changed tokens.
    $changedTokens = $this->getChangedTokens($this->newRates);

    // Store attributes in config.
    foreach ($this->newRates as $attributeName => $attributeValue) {
      $this->ratesConfig->set($attributeName, $attributeValue)->save();
    }

    // If there are changed tokens.
    if (count($changedTokens)) {
      // Create revisions for nodes referencing changed tokens.
      $this->tokenReferenceHelper->createRevisionsForReferencingNodes($changedTokens, 'Programatic revision to record deposit rate change(s).');
    }

  }

  /**
   * Determines which tokens have changed changed.
   *
   * @param array $newRates
   *   An array of tokens and values to compare against existing token values.
   *
   * @return array
   *   The tokens who's values have changed.
   */
  public function getChangedTokens(array $newRates) {

    // Instantiate array to store tokens that have changed.
    $changedTokens = [];

    // Loop over the new rates and values.
    foreach ($newRates as $newRateKey => $newRateValue) {
      // If the new rate value equals the old one.
      if ($this->ratesConfig->get($newRateKey) !== $newRateValue) {
        // Add to our list of changed tokens.
        $changedTokens[] = '[arvestbank_rates:' . $newRateKey . ']';
      }
    }

    return $changedTokens;

  }

}
