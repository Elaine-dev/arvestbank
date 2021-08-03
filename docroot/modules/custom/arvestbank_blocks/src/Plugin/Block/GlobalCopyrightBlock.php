<?php

namespace Drupal\arvestbank_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\Markup;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a custom block for a global copyright.
 *
 * @Block(
 *  id = "global_copyright_block",
 *  admin_label = @Translation("Global Copyright Block"),
 * )
 */
class GlobalCopyrightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Initialize the return render array.
    $build = [];

    // Outer container for the copyright.
    $build['copyright'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['coh-style-footer-styles'],
      ],
    ];

    // Markup text with dynamic date.
    $build['copyright']['text'] = [
      '#markup' => 'Copyright &copy; ' . date('Y') . '  Arvest Bank. All Rights Reserved.',
      '#prefix' => '<div class="coh-style-float-left">',
      '#suffix' => '</div>',
    ];

    // These are the default icons.
    $icons = [
      '<img alt="Member FDIC" height="23" src="/sites/default/files/acquiadam_assets/fdic-logo.png" width="36" loading="lazy" typeof="foaf:Image">',
      '<img alt="Equal Housing Lender" height="33" src="/sites/default/files/acquiadam_assets/housing-logo2.png" width="32" loading="lazy" typeof="foaf:Image">',
    ];

    // Switch out the icons on campaign landing pages.
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      if ($node instanceof Node && $node->getType() == 'campaign_page') {
        $icons = [
          '<img src="/sites/default/files/acquiadam/images/fdic-grey.png" width="43" height="41" alt="Member FDIC" title="Fdic Grey" loading="lazy" typeof="foaf:Image">',
          '<img src="/sites/default/files/acquiadam/images/nhl-grey.png" width="39" height="41" alt="Equal Housing Lender" loading="lazy" typeof="foaf:Image">',
        ];
      }
    }

    // Add icons to the render array.
    $build['copyright']['icons'] = [
      '#markup' => implode($icons),
      '#prefix' => '<div class="coh-style-float-right">',
      '#suffix' => '</div>',
    ];

    // Return the build render array.
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    // Block should be cached on a per page (route) level.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
