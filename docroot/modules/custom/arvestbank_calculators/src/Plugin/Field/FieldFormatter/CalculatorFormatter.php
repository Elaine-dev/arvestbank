<?php

namespace Drupal\arvestbank_calculators\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Calculator Embed' formatter.
 *
 * @FieldFormatter(
 *   id = "arvestbank_calculator",
 *   label = @Translation("Calculator Embed"),
 *   field_types = {
 *     "list_string"
 *   }
 * )
 */
class CalculatorFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $element = [];

    foreach ($items as $delta => $item) {

      $calc_id = $item->value;

      $element[$delta] = [
        '#theme' => 'arvestbank_calculators',
        '#calc_id' => $calc_id,
      ];

    }

    return $element;

  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return TRUE;
    //dump($field_definition);
    //return $field_definition->getTargetEntityTypeId() === 'media' && $field_definition->getName() === 'field_media_qumu';
  }

}
