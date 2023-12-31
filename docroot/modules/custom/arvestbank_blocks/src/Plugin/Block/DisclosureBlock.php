<?php

namespace Drupal\arvestbank_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'DisclosureBlock' block.
 *
 * @Block(
 *  id = "disclosure_block",
 *  admin_label = @Translation("Disclosure Block"),
 * )
 */
class DisclosureBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Initialize the return render array.
    $build = [];

    // Initialize disclosure text variable.
    $disclosure_id_default = 0;
    $disclosure_text = '';

    // Lookup a term named "Default" to set the default.
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['name' => 'Default', 'vid' => 'disclosures']);
    $term = reset($term);
    if (!empty($term)) {
      // We have the term, may as well grab this text.
      $disclosure_id_default = $term->id();
      $disclosure_text = $term->get('description')->getValue()[0]['value'];
    }

    // Get the current entity with the helper function.
    $entity = \Drupal::service('arvestbank_blocks.service')->getRouteEntity();

    // Check for Node.
    if ($entity instanceof Node) {
      // Check that it has the correct field.
      if ($entity->hasField('field_disclosure')) {
        // Grab the term id.
        if (!empty($entity->get('field_disclosure')->getValue()[0]['target_id'])) {
          $disclosure_id = $entity->get('field_disclosure')->getValue()[0]['target_id'];
          // Grab the term text, only if it is different from the default.
          if ($disclosure_id != $disclosure_id_default) {
            $disclosure_term = Term::load($disclosure_id);
            if (!empty($disclosure_term)) {
              $disclosure_text = $disclosure_term->get('description')->getValue()[0]['value'];
            }
          }
        }
      }
    }

    // If there is disclosure text add it to the markup.
    if (!empty($disclosure_text)) {
      $build['disclosure_block'] = [
        '#prefix' => '<div class="coh-style-footer-styles"><div class="text-align-center">',
        '#suffix' => '</div></div>',
        '#markup' => $disclosure_text,
      ];
    }

    // Return the build render array.
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return 86400;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // With this when your node change your block will rebuild.
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      // Initialize return ID.
      $nid = 0;
      // If there is node add its cachetag.
      if ($node instanceof Node) {
        $nid = $node->id();
      }
      elseif (intval($node)) {
        $nid = $node;
      }
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $nid]);
    }
    else {
      // Return default tags instead.
      return parent::getCacheTags();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    // Block should be cached on a per page (route) level.
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
