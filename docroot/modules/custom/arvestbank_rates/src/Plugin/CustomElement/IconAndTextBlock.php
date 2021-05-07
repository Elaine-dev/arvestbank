<?php

namespace Drupal\arvestbank_rates\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Site Studio element to show fields from a component.
 *
 * @package Drupal\arvestbank_rates\Plugin\CustomElement
 *
 * @CustomElement(
 *   id = "site_studio_icon_and_text_block_element",
 *   label = @Translation("Icon And Text Block")
 * )
 */
class IconAndTextBlock extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {

    return [
      'dummy-text-field' => [
        'title' => 'A field is required for custom elements, but this element is configured in the component.',
        'type' => 'textfield',
      ],
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function render($element_settings, $element_markup, $element_class) {

    // Instantiate the render array we will return.
    $renderArray = [];

    // Get the uuid of the component instance from the $element class.
    $componentInstanceUuid = trim(
      str_replace(
        [
          'coh-component-instance-',
          'coh-component',
          'contextual-component',
        ],
        '',
        $element_class
      )
    );

    // Load layout entity representing layout canvas values for the parent node.
    $layoutEntityQueryResults = \Drupal::entityQuery('cohesion_layout')
      ->condition('json_values', $componentInstanceUuid, 'CONTAINS')
      ->execute();

    // If we found the layout entity.
    if (count($layoutEntityQueryResults)) {

      // Load the cohesion layout entity.
      $cohesionLayoutEntity = \Drupal::entityTypeManager()
        ->getStorage('cohesion_layout')
        ->load(array_pop($layoutEntityQueryResults));

      // Get the json values for the layout.
      $cohesionLayoutEntityJsonData = json_decode($cohesionLayoutEntity->getJsonValues());

      // If we have field data for this component instance.
      if (isset($cohesionLayoutEntityJsonData->model->$componentInstanceUuid)) {

        // Get component instance field values from json.
        $componentInstanceFieldValues = $cohesionLayoutEntityJsonData->model->$componentInstanceUuid;

        // Add container to render array.
        $renderArray = [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'icon-and-text-block',
            ],
          ],
        ];

        // Define the field keys.
        $mediaFieldKey = '081c32df-d2a2-4692-a5f1-11b2b0282669';
        $wysiwygFieldKey = '31a1c3f0-5dd2-40db-a82f-fe16e991b0e9';

        // If we have a value for the icon field.
        if (
          isset($componentInstanceFieldValues->$mediaFieldKey)
          && $componentInstanceFieldValues->$mediaFieldKey
        ) {
          // Get referenced media uuid.
          $mediaUuid = str_replace(
            [
              '[media-reference:media:',
              ']',
            ],
            '',
            $componentInstanceFieldValues->$mediaFieldKey
          );
          // Load media entity.
          $mediaEntity = \Drupal::service('entity.repository')->loadEntityByUuid('media', $mediaUuid);
          // If we loaded the media entity successfully.
          if ($mediaEntity) {
            // Get and add render array for media entity.
            $renderArray['icon'] = \Drupal::entityTypeManager()
              ->getViewBuilder('media')
              ->view($mediaEntity);
          }
        }

        // If we have a value for the wysiwyg field.
        if (
          isset($componentInstanceFieldValues->$wysiwygFieldKey)
          && isset($componentInstanceFieldValues->$wysiwygFieldKey->text)
          && $componentInstanceFieldValues->$wysiwygFieldKey->text
        ) {

          // Load token service.
          $tokenService = \Drupal::token();

          // Get markup with tokens replaced.
          $wysiwygMarkup = $tokenService->replace($componentInstanceFieldValues->$wysiwygFieldKey->text);

          // Determine if the text had tokens.
          if ($wysiwygMarkup != $componentInstanceFieldValues->$wysiwygFieldKey->text) {
            $replacedTokens = TRUE;
          }

          // Add wysiwyg content run through the token replacement method.
          $renderArray['wysiwyg_content'] = [
            '#type' => 'container',
            '#attributes' => [
              'class' => [
                'icon-and-text-block--wysiwyg-content',
              ],
            ],
            '#markup' => $wysiwygMarkup,
          ];

        }

      }

    }

    // Add cache tag to render array.
    if (isset($cohesionLayoutEntity) && $cohesionLayoutEntity) {

      // Base caching conditions of one day and cohesion_layout entity.
      $renderArray['#cache'] = [
        'tags' => [
          'cohesion_layout:' . $cohesionLayoutEntity->id(),
        ],
        'max-age' => 86400,
      ];

      // If we're showing a media entity.
      if (isset($mediaEntity) && $mediaEntity) {
        // Add a cache tag for the media entity.
        $renderArray['#cache']['tags'][] = 'media:' . $mediaEntity->id();
      }

      // If we replaced tokens.
      if (isset($replacedTokens) && $replacedTokens) {
        // Add a cache tag for the rates config.
        $renderArray['#cache']['tags'][] = 'config:arvestbank_rates.settings';
      }

    }

    // Return the render array.
    return $renderArray;
  }

}
