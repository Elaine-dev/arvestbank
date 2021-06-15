<?php

namespace Drupal\arvestbank_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Drupal\Core\Database\Connection;

/**
 * Provides a form to start a media update batch.
 *
 * Path: /admin/arvestbank-core/media-update-batch.
 */
class MediaUpdateBatch extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return 'media_update_batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['#prefix'] = '<p>This batch will update media.</p>';

    // This checks for how many need to be updated - keeps consistent w/batch.
    $results = self::mediaResults();

    // Change the text if the update logic is different.
    $message_text = "This operation will set unpublished media to published.";
    $message_text .= "<br>There are " . count($results) . ' to update.';

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => $message_text,
    ];

    if ($results) {

      $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
          '#type' => 'submit',
          '#value' => 'Proceed',
        ],
      ];

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Initialize the batch.
    $batch = [
      'title' => t('Updating Media Entities'),
      'operations' => [],
      'init_message' => t('The process is starting...'),
      'progress_message' => t('Processed @current out of @total. Estimated time: @estimate.'),
      'error_message' => t('The process has encountered an error.'),
    ];

    // Media to update - see function.
    $results = self::mediaResults();

    // Array of operations to add to the batch.
    $operations = [];
    foreach ($results as $result) {
      $mid = $result->mid;
      $operations[] = [
        [$this, 'updateMedia'], [$mid],
      ];
    }
    $batch['operations'] = $operations;

    // Set the batch and message and form state.
    batch_set($batch);
    \Drupal::messenger()->addMessage('Updated ' . count($results) . ' media entities!');
    $form_state->setRebuild(TRUE);

  }

  /**
   * Updates a media entity.
   *
   * @param int $mid
   *   Media id.
   * @param array $context
   *   Batch context.
   *
   * @throws \Exception
   */
  public function updateMedia(int $mid, array &$context) {

    // Load the media, set to published (or whatever update) and save.
    $media = Media::load($mid);
    $media->set('status', 1);
    $media->save();

    // Something to send back for batch update status.
    $context['results'][] = $media->getName();
    $context['message'] = t('Updating @title', ['@title' => $media->getName()]);

    // Little pause to help keep things from blowin up.
    usleep(100);

  }

  /**
   * Holds the logic for what exactly we are querying for in the media.
   *
   * Set initially to find unpublished media.
   */
  public function mediaResults() {

    $database = \Drupal::database();

    $query = $database->select('media_field_revision', 'r');
    $query->join('media', 'm', 'r.vid = m.vid');
    $query->condition('r.status', '0', '=');
    $query->fields('m', ['mid']);

    return $query->execute()->fetchAll();

  }

}
