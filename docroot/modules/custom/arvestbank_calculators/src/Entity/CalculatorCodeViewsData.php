<?php

namespace Drupal\arvestbank_calculators\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Calculator Code entities.
 */
class CalculatorCodeViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
