<?php

namespace Drupal\arvestbank_calculators\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'Calculator Embed' formatter.
 *
 * @FieldFormatter(
 *   id = "arvestbank_calculators_embed",
 *   label = @Translation("Calculator Embed"),
 *   field_types = {
 *     "list_string"
 *   }
 * )
 */
class CalculatorEmbedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $element = [];

    foreach ($items as $delta => $item) {

      $calc_id = $item->value;

      $element[$delta] = [
        '#theme' => 'arvestbank_calculators_embed',
        '#calc_id' => $calc_id,
      ];

    }

    if (!empty($element)) {
      $element['#attached'] = [
        'library' => [
          'arvestbank_calculators/calculator_script',
          'arvestbank_calculators/calculator_style',
        ],
      ];
    }

    return $element;

  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    return $field_definition->getTargetEntityTypeId() === 'node'
      && $field_definition->getTargetBundle() === 'calculators'
      && $field_definition->getName() === 'field_calculator';
  }

}
