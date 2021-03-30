<?php

/**
 * @file
 * Contains calculator_code.page.inc.
 *
 * Page callback for Calculator Code entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Calculator Code templates.
 *
 * Default template: calculator_code.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_calculator_code(array &$variables) {
  // Fetch CalculatorCode Entity Object.
  $calculator_code = $variables['elements']['#calculator_code'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
