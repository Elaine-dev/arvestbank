<?php

namespace Drupal\arvestbank_phone_numbers\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler showing phone references.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("phone_references")
 */
class PhoneReferences extends FieldPluginBase {

  /**
   * Function for querying.
   *
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {

    // Get the node for this phone number.
    $phoneNumberNode = $values->_entity;

    // Get token reference helper service.
    $tokenReferenceHelper = \Drupal::service('arvestbank_revisions.token_reference_helper');

    // Get references to this phone number.
    $nodesWithTokensForThisPhoneNumber = $tokenReferenceHelper->getNodesWithReferencesToTokenGroup('arvestbank_phone_numbers:nid=' . $phoneNumberNode->id());

    $references = [];

    // Loop over referencing nodes.
    foreach ($nodesWithTokensForThisPhoneNumber as $nodeWithTokensForThisPhoneNumber) {
      $references[] = [
        '#markup' =>
        '<a href="/node/' . $nodeWithTokensForThisPhoneNumber->id() . '">'
        . $nodeWithTokensForThisPhoneNumber->getTitle()
        . '</a><br/>',
      ];
    }

    // Add a value for none.
    if (!count($references)) {
      $references[] = [
        '#markup' => '- none -',
      ];
    }

    return $references;
  }

}
