<?php

namespace Drupal\arvestbank_calculators;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Calculator Code entities.
 *
 * @ingroup arvestbank_calculators
 */
class CalculatorCodeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    $header['field_calculator_code_descript'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\arvestbank_calculators\Entity\CalculatorCode $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.calculator_code.edit_form',
      ['calculator_code' => $entity->id()]
    );
    $row['field_calculator_code_descript'] = $entity->get('field_calculator_code_descript')->getValue()[0]['value'];
    return $row + parent::buildRow($entity);
  }

}
