<?php

namespace Drupal\arvestbank_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;

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

    $form['update_action'] = [
      '#title' => 'Update Action:',
      '#type' => 'select',
      '#options' => [
        '' => '--- select ---',
        'publish' => 'Set unpublished media to published',
        'alttags' => 'Fill in missing alt and title tags',
      ],
      '#ajax' => [
        'callback' => '::updateAction',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'update-action-wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Please Wait...'),
        ],
      ],
    ];

    // Change the text if the update logic is different.
    $message_text = "Select an action to apply to media:";

    $form['action'] = [
      '#prefix' => '<div id="update-action-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['action']['message'] = [
      '#type' => 'markup',
      '#markup' => $message_text,
    ];

    $form['action']['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => 'Proceed',
      ],
    ];

    return $form;

  }

  /**
   * Returns information about the action to be preformed.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return mixed
   *   return.
   */
  public function updateAction(array &$form, FormStateInterface $form_state) {

    if ($selectedAction = $form_state->getValue('update_action')) {

      // This checks for how many need to be updated - keeps consistent w/batch.
      $results = self::mediaResults($selectedAction);

      if ($results) {
        $message_text = "This operation will set unpublished media to published.";
        $message_text .= "<br>There are " . count($results) . ' to update.';
        $form['action']['message']['#markup'] = $message_text;
      }

    }

    return $form['action'];

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

    // Get the action value.
    $selectedAction = $form_state->getValue('update_action');

    // Media to update - see function.
    $results = self::mediaResults($selectedAction);

    // Array of operations to add to the batch.
    $operations = [];
    foreach ($results as $result) {
      $mid = $result->mid;
      $operations[] = [
        [$this, 'updateMedia'], [$mid, $selectedAction],
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
   * @param string $action
   *   Action to take.
   * @param array $context
   *   Batch context.
   *
   * @throws \Exception
   */
  public function updateMedia(int $mid, string $action, array &$context) {

    // Load the media, set to published (or whatever update) and save.
    $media = Media::load($mid);

    switch ($action) {

      case "publish":
        $media->set('status', 1);
        break;

      case "alttags":
        $alt_text = $this::getAltFromName($media->getName());
        $image_array = $media->get('field_acquiadam_asset_image')->getValue();
        $image_array[0]['alt'] = $alt_text;
        $image_array[0]['title'] = $alt_text;
        $media->set('field_acquiadam_asset_image', $image_array);
        break;

    }

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
   *
   * @param string|null $action
   *   Action to take.
   *
   * @return mixed
   *   Query of results.
   */
  public function mediaResults(string $action = NULL) {

    $database = \Drupal::database();

    switch ($action) {

      case "publish":
        $query = $database->select('media_field_revision', 'r');
        $query->join('media', 'm', 'r.vid = m.vid');
        $query->condition('r.status', '0', '=');
        $query->fields('m', ['mid']);
        break;

      case "alttags":
        $query = $database->select('media__field_acquiadam_asset_image', 'i');
        $query->condition('i.field_acquiadam_asset_image_alt', 'NULL', 'IS NULL');
        $query->addField('i', 'entity_id', 'mid');
        break;

    }

    // Use this for testing.
    $query->range(0, 100);

    return $query->execute()->fetchAll();

  }

  /**
   * Converts a filename to a friendly name for alt and title.
   *
   * @param string $name
   *   Filename passed in.
   *
   * @return string
   *   Friendly name.
   */
  public function getAltFromName(string $name) : string {

    // Take off the extension.
    $name_ar = explode('.', $name);
    array_pop($name_ar);

    // Turn it back into a string, any other .'s will now be spaces.
    $name = implode(' ', $name_ar);

    // Strip out other characters.
    $name = str_replace(['-', '_'], ' ', $name);

    // Turn back into an array.
    $name_ar = explode(' ', $name);

    // Loop through name array and capitalize each part.
    $name_ar_caps = [];
    foreach ($name_ar as $name_piece) {
      $name_ar_caps[] = ucfirst($name_piece);
    }

    // And send it back to a string.
    $name = implode(' ', $name_ar_caps);

    return $name;

  }

}
