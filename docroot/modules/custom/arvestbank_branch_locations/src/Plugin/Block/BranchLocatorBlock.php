<?php

namespace Drupal\arvestbank_branch_locations\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides exposed filters in a block.
 *
 * @Block(
 *   id = "branch_locator",
 *   admin_label = @Translation("Branch Locator Form"),
 * )
 */
class BranchLocatorBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Start render array with a container.
    $renderArray = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['branch-locator'],
      ],
    ];

    // Add title to render array.
    $renderArray['title'] = [
      '#markup' => '<div class="branch-locator-label">Atm & Branch Locations</div>',
    ];

    // Add form to render array.
    $renderArray['form'] = [
      '#type' => 'form',
      '#action' => 'https://locations.arvest.com/',
      '#method' => 'GET',
    ];

    // Add text field to form.
    $renderArray['form']['location_search'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'name' => 'loc_q',
        'aria-label' => 'branch location'
      ],
      '#placeholder' => 'City, State or Zip',
    ];

    // Add submit button to form.
    $renderArray['form']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Go'),
    ];

    // Build and return form.
    return $renderArray;

  }

}
