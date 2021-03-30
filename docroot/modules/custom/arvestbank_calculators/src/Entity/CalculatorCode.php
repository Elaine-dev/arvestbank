<?php

namespace Drupal\arvestbank_calculators\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Calculator Code entity.
 *
 * @ingroup arvestbank_calculators
 *
 * @ContentEntityType(
 *   id = "calculator_code",
 *   label = @Translation("Calculator Code"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\arvestbank_calculators\CalculatorCodeListBuilder",
 *     "views_data" = "Drupal\arvestbank_calculators\Entity\CalculatorCodeViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\arvestbank_calculators\Form\CalculatorCodeForm",
 *       "add" = "Drupal\arvestbank_calculators\Form\CalculatorCodeForm",
 *       "edit" = "Drupal\arvestbank_calculators\Form\CalculatorCodeForm",
 *       "delete" = "Drupal\arvestbank_calculators\Form\CalculatorCodeDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\arvestbank_calculators\CalculatorCodeHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\arvestbank_calculators\CalculatorCodeAccessControlHandler",
 *   },
 *   base_table = "calculator_code",
 *   translatable = FALSE,
 *   admin_permission = "administer calculator code entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/calculator_code/{calculator_code}",
 *     "add-form" = "/admin/structure/calculator_code/add",
 *     "edit-form" = "/admin/structure/calculator_code/{calculator_code}/edit",
 *     "delete-form" = "/admin/structure/calculator_code/{calculator_code}/delete",
 *     "collection" = "/admin/structure/calculator_code",
 *   },
 *   field_ui_base_route = "calculator_code.settings"
 * )
 */
class CalculatorCode extends ContentEntityBase implements CalculatorCodeInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setDescription(t('The code for this calculator.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Calculator Code is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
