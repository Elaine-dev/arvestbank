<?php

namespace Drupal\arvestbank_revisions;

/**
 * Service class to find nodes that reference tokens.
 */
class TokenReferenceHelper {

  /**
   * Creates a new revision for nodes that contain a token in given group.
   *
   * @param string $token_group
   *   The machine name of the token group to look for references of.
   *   Can also contain a specific token ie arvestbank_phone_numbers:nid=6966.
   * @param string $revision_message
   *   The revision message for created revisions.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createRevisionsForReferencingNodes($token_group, $revision_message) {

    // Get nodes with references to tokens in given group.
    $referencingNodes = $this->getNodesWithReferencesToTokenGroup($token_group);

    // Loop over nodes with references.
    foreach ($referencingNodes as $referencingNode) {
      // Save node, creating revision with updated rate in field_rendered_node.
      $referencingNode->setNewRevision(TRUE);
      // This is needed to show up in revision list
      // because of https://www.drupal.org/project/diff/issues/2882334
      $referencingNode->set('revision_translation_affected', TRUE);
      $referencingNode->setRevisionLogMessage($revision_message);
      $referencingNode->setRevisionCreationTime(REQUEST_TIME);
      $referencingNode->setRevisionUserId(1);
      $referencingNode->save();
    }

  }

  /**
   * Returns nodes with references.
   *
   * @param string $token_group
   *   The machine name of a token group to get references for.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Nodes with tokens in given token group.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNodesWithReferencesToTokenGroup($token_group) {

    // Get pages that have a token in given group in body copy.
    $basicPagesWithTokenQuery = \Drupal::entityQuery('node');
    $fieldConditionGroup = $basicPagesWithTokenQuery->orConditionGroup()
      ->condition('body', '[' . $token_group, 'CONTAINS')
      ->condition('field_alert_block_body', '[' . $token_group, 'CONTAINS')
      ->condition('field_slide_one_cta_text', '[' . $token_group, 'CONTAINS')
      ->condition('field_slide_two_cta_text', '[' . $token_group, 'CONTAINS')
      ->condition('field_slide_three_cta_text', '[' . $token_group, 'CONTAINS')
      ->condition('field_slide_four_cta_text', '[' . $token_group, 'CONTAINS');
    $basicPagesWithTokenQuery
      ->condition('type',
        [
          'page',
          'landing_page',
          'article_education_article',
          'associate',
          'calculators',
          'stage_page',
        ],
        'IN'
      )
      ->condition($fieldConditionGroup);
    $basicPagesWithTokenResults = $basicPagesWithTokenQuery->execute();

    // Get pages that have a token in group in a component instance.
    $campaignPagesWithTokenQuery = \Drupal::entityQuery('node');
    $fieldConditionGroup = $campaignPagesWithTokenQuery->orConditionGroup()
      ->condition('field_layout_canvas.entity:cohesion_layout.json_values', '[' . $token_group, 'CONTAINS')
      ->condition('field_layout_.entity:cohesion_layout.json_values', '[' . $token_group, 'CONTAINS');
    $campaignPagesWithTokenQuery
      ->condition('type', ['campaign_page', 'landing_page', 'page'], 'IN')
      ->condition($fieldConditionGroup);
    $campaignPagesWithTokenResults = $campaignPagesWithTokenQuery->execute();

    // Combine result arrays.
    $nodesWithRates = array_merge($basicPagesWithTokenResults, $campaignPagesWithTokenResults);

    // Load and return the nodes with rates.
    return \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nodesWithRates);

  }

}
