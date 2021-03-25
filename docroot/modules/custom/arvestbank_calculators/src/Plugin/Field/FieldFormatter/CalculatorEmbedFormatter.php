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
 *     "entity_reference"
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

      // Get the calculator code from the custom entity.
      $calc_id = $item->getEntity()->field_calculator->referencedEntities()[0]->getName();

      $element[$delta] = [
        '#theme' => 'arvestbank_calculators_embed',
      ];

    }

    if (!empty($element)) {
      // Attach libraries and pass calc_id through to JS via drupalSettings.
      $element['#attached'] = [
        'library' => [
          'arvestbank_calculators/calculator_script',
          'arvestbank_calculators/calculator_script_embed',
          'arvestbank_calculators/calculator_style',
        ],
        'drupalSettings' => [
          'arvestbank_calculators' => [
            'calc_id' => $calc_id,
          ],
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
