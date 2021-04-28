<?php

namespace Drupal\arvestbank_browsertest\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class BrowserTestController.
 *
 * Renders the browsertest to the screen.
 */
class BrowserTestController extends ControllerBase {

  /**
   * Render method.
   *
   * @return array
   *   Returns render array for the browser test.
   */
  public function render(): array {

    // Grab the referer.
    // Should this be set locally?  see: current /functions_content.php.
    $referer = $_SERVER['HTTP_REFERER'] ?? NULL;
    if (empty($referer)) {
      $referer = 'none / unknown';
    }

    // Set the default text, basically JS is disabled.  This will get
    // overwritten if JS IS enabled.
    $default_text = 'You must enable javascript for this test to function properly.';

    $return = [
      '#type' => 'markup',
      '#markup' => '<h1>Browser Diagnostics</h1><div id="browsertest">' . $default_text . '</div>',
      '#attached' => [
        'library' => [
          'arvestbank_browsertest/browsertest_scripts',
        ],
        'drupalSettings' => [
          'arvestbank_browsertest' => [
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? NULL,
            'client' => $_SERVER['HTTP_CLIENT_IP'] ?? NULL,
            'forwarded' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? NULL,
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? NULL,
            'referer' => $referer ?? NULL,
          ],
        ],
      ],
    ];

    return $return;

  }

}
