<?php

namespace Drupal\arvestbank_careers\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'CareersBlock' iframe from AppOne.
 *
 * @Block(
 *  id = "careers_block",
 *  admin_label = @Translation("Careers Block"),
 * )
 */
class CareersBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    // Initialize the return render array.
    $build = [];

    // This key determines if the user is an associate or not.
    $associate = \Drupal::request()->query->get('a');

    // Check for key, it should only be "y" or "n".
    // If no key or not valid, display modal with y/n choice.
    if (empty($associate) || !in_array($associate, ['y', 'n'])) {

      // This will fire off the modal on page load, and add styles.
      $build['#attached'] = [
        'library' => [
          'arvestbank_careers/careers_associate_modal',
        ],
      ];

    }

    // We have a valid associate parameter to pass to AppOne.
    else {

      // Base URL for the careers iframe.
      $iframe_src = 'https://www.appone.com/branding/reqtemplate/default.asp?servervar=ArvestBank2.appone.com&Internal=';

      // Pass this variable based off of the careers associate modal.
      if ($associate === 'y') {
        $iframe_src .= 'yes';
      }
      else {
        $iframe_src .= 'no';
      }

      // Render array is an iframe tag.
      $build[] = [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'src' => $iframe_src,
          'frameborder' => 0,
          'scrolling' => FALSE,
          'allowtransparency' => TRUE,
          'width' => '100%',
        ],
      ];

    }

    // Return the build render array.
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    // No caching on this - fresh content for each visitor.
    return 86400;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    // Block should be cached per path per query string.
    return Cache::mergeContexts(parent::getCacheContexts(), [
      'url.path',
      'url.query_args',
    ]);
  }

}
