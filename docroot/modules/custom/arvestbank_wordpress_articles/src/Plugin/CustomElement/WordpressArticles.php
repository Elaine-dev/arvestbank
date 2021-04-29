<?php

namespace Drupal\arvestbank_wordpress_articles\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Site Studio element to embed wordpress articles on a page.
 *
 * @package Drupal\site_studio_webform\Plugin\CustomElement
 *
 * @CustomElement(
 *   id = "site_studio_wordpress_articles_element",
 *   label = @Translation("Wordpress Articles")
 * )
 */
class WordpressArticles extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {

    return [
      'wordpress_endpoint' => [
        'title' => 'Wordpress Endpoint',
        'type' => 'textfield',
        'placeholder' => 'e.g. https://share.arvest.com/feed/json',
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

        // The key for the url field, could be obtained from component config.
        $urlFieldKey = 'afca44d5-3d32-44d3-aaef-d8e7574c26fd';

        // Get the selected Url.
        $componentInstanceUrl = $componentInstanceFieldValues->$urlFieldKey;

        // If we have an endpoint to fetch.
        if ($componentInstanceUrl) {
          // Get wordpress articles service.
          $wordpressArticlesService = \Drupal::service('arvestbank_wordpress_articles.wordpress_articles_service');
          // Get render array to return.
          $renderArray = $wordpressArticlesService->getRenderArray('xxx', $componentInstanceUrl, 2);
        }

      }


    }

    $test = '';

    // Return the render array.
    return $renderArray;
  }

}
