<?php

namespace Drupal\arvestbank_revisions;

use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Service class to find nodes that reference tokens.
 */
class TokenReferenceHelper {

  /**
   * Creates a new revision for nodes that contain passed token(s).
   *
   * @param array $tokens
   *   Machine names of token(s) to find refs to.
   * @param string $revisionMessage
   *   The revision message for created revisions.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createRevisionsForReferencingNodes(array $tokens, $revisionMessage) {

    // If an array was passed.
    if (is_array($tokens)) {
      // Get deduped nodes with a given token.
      $referencingNodes = $this->getNodesReferencingTokens($tokens);
      // Create new revision for nodes.
      $this->createNewRevisionForNodes($referencingNodes, $revisionMessage);
    }

  }

  /**
   * Gets a deduplicated list of nodes with references to given tokens.
   *
   * @param array $tokens
   *   The tokens for which to get references for.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The nodes referencing the given tokens.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNodesReferencingTokens(array $tokens) {

    // Query to get nodes that have (even nested) references to the tokens.
    $nodesWithReferencesQuery = \Drupal::entityQuery('node');

    // Add content type condition to query.
    $this->addContentTypeConditionToEntityQuery($nodesWithReferencesQuery);

    // Contains condition group, to contain one sub-condition-group per token.
    $outerFieldOrConditionGroup = $nodesWithReferencesQuery->orConditionGroup();

    // Loop over tokens.
    foreach ($tokens as $token) {
      // Get field or-condition-group.
      $fieldOrConditionGroup = $this->getFieldOrConditionGroup($nodesWithReferencesQuery, $token);
      // Add field or-condition-group to outer contains condition group.
      $outerFieldOrConditionGroup->condition($fieldOrConditionGroup);
    }

    // Add contains condition group to query.
    $nodesWithReferencesQuery->condition($outerFieldOrConditionGroup);

    // Execute Query.
    $nodesWithReferencesQueryResult = $nodesWithReferencesQuery->execute();

    // Load and return nodes with matching tokens.
    return \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nodesWithReferencesQueryResult);

  }

  /**
   * Creates revisions for nodes.
   *
   * @param array $nodes
   *   An array of nodes to create revisions for.
   * @param string $revisionMessage
   *   The message to use for created revisions.
   */
  private function createNewRevisionForNodes(array $nodes, string $revisionMessage) {
    // Loop over nodes with references.
    foreach ($nodes as $referencingNode) {
      // Save node, creating revision with updated rate in field_rendered_node.
      $referencingNode->setNewRevision(TRUE);
      // This is needed to show up in revision list
      // because of https://www.drupal.org/project/diff/issues/2882334
      $referencingNode->set('revision_translation_affected', TRUE);
      $referencingNode->setRevisionLogMessage($revisionMessage);
      $referencingNode->setRevisionCreationTime(REQUEST_TIME);
      $referencingNode->setRevisionUserId(1);
      $referencingNode->save();
    }
  }

  /**
   * Gets a condition group.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query to add a condition group to.
   * @param string $searchString
   *   The string to search for.
   *
   * @return \Drupal\Core\Entity\Query\ConditionInterface
   *   A condition group.
   */
  private function getFieldOrConditionGroup(QueryInterface &$query, $searchString) {
    // Create and return condition group.
    return $query->orConditionGroup()
      ->condition('body', $searchString, 'CONTAINS')
      ->condition('field_alert_block_body', $searchString, 'CONTAINS')
      ->condition('field_slide_one_cta_text', $searchString, 'CONTAINS')
      ->condition('field_slide_two_cta_text', $searchString, 'CONTAINS')
      ->condition('field_slide_three_cta_text', $searchString, 'CONTAINS')
      ->condition('field_slide_four_cta_text', $searchString, 'CONTAINS')
      ->condition('field_layout_canvas.entity:cohesion_layout.json_values', $searchString, 'CONTAINS')
      ->condition('field_layout_.entity:cohesion_layout.json_values', $searchString, 'CONTAINS');
  }

  /**
   * Adds entity type condition to the given query.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query to append content type condition to.
   */
  private function addContentTypeConditionToEntityQuery(QueryInterface &$query) {
    $query->condition('type',
      [
        'page',
        'landing_page',
        'article_education_article',
        'associate',
        'calculators',
        'stage_page',
        'campaign_page',
      ],
      'IN'
    );
  }

}
