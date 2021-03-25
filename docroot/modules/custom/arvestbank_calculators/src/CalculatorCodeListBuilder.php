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
    $header['id'] = $this->t('Calculator Code ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\arvestbank_calculators\Entity\CalculatorCode $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.calculator_code.edit_form',
      ['calculator_code' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
