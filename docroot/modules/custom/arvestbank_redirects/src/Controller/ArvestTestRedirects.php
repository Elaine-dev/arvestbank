<?php

namespace Drupal\arvestbank_redirects\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller class to implement Arvestbank Test Redirects.
 */
class ArvestTestRedirects extends ControllerBase {

  /**
   * The ArvestTestRedirects service.
   *
   * @var \Drupal\arvestbank_redirects\ArvestTestRedirectsService
   */
  protected $arvestTestRedirectsService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\arvestbank_redirects\Controller\ArvestTestRedirects $controller */
    $controller = parent::create($container);
    $controller->configFactory = $container->get('config.factory');
    $controller->languageManager = $container->get('language_manager');
    $controller->loggerFactory = $container->get('logger.factory');
    $controller->arvestTestRedirectsService = $container->get('arvestbank_redirects.settings_service');
    return $controller;
  }

  /**
   * Redirects requests to /shop/my-account to the correct Etiya URL.
   *
   * @return \Drupal\Core\Routing\TrustedRedirectResponse
   *   The redirect response.
   */
  public function redirectToMyAccount() {
    $config = $this->configFactory->get('dxp_shop.settings');
    if (($my_account_url = $this->shopSettingsService->get('my_account_url')) &&
      UrlHelper::isValid($my_account_url)) {
      $status_code = 301;
    }
    else {
      // If no URL configured, or URL is invalid, log a warning and redirect to
      // home page.
      $this->loggerFactory->get('dxp_shop')->warning('No URL configured for My Account.');
      $my_account_url = Url::fromRoute('<front>', [], [
        'language' => $this->languageManager->getCurrentLanguage(),
      ])->toString();
      $status_code = 302;
    }

    return TrustedRedirectResponse::create($my_account_url, $status_code)
      ->addCacheableDependency($config);
  }

}
