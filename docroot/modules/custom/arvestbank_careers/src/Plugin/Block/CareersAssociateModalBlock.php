<?php

namespace Drupal\arvestbank_careers\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

/**
 * Provides a 'Careers Associate Modal' Block.
 *
 * @Block(
 *   id = "careers_associate_modal_block",
 *   admin_label = @Translation("Careers Associate Modal Block"),
 * )
 */
class CareersAssociateModalBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // This is the modal controller.
    $link_url = Url::fromRoute('careers_associate_modal.modal');

    // Add basic options to the modal.
    $link_url->setOptions([
      'attributes' => [
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(['width' => 400]),
      ],
    ]);

    // All we need to return is the library to fire the modal and add style.
    return [
      '#attached' => [
        'library' => ['core/drupal.dialog.ajax'],
      ],
    ];

  }

}
