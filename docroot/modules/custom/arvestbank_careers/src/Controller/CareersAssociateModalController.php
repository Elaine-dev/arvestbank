<?php

namespace Drupal\arvestbank_careers\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class CareersAssociateModalController for the careers associate modal.
 *
 * @package Drupal\arvestbank_careers\Controller
 */
class CareersAssociateModalController extends ControllerBase {

  /**
   * Returns the contents of the careers modal "are you an associate" choice.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function modal(): AjaxResponse {

    // Set some basic options for the modal.
    $options = [
      'dialogClass' => 'modal-careers-associate',
      'width' => '355px',
    ];

    // Initialize the Ajax response.
    $response = new AjaxResponse();

    // Build the HTML for the modal.
    $modal_html = '<h2>Are you a current Arvest associate?</h2>';
    $modal_html .= '<div class="modal-links">';
    $modal_html .= '<a href="/careers/apply?a=y" class="modal-btn">Yes</a>';
    $modal_html .= '<a href="/careers/apply?a=n" class="modal-btn">No</a>';
    $modal_html .= '</div>';

    // This adds the modal to the ajax response.
    $response->addCommand(new OpenModalDialogCommand(NULL, $modal_html, $options));

    // Return the Ajax response.
    return $response;

  }

}
