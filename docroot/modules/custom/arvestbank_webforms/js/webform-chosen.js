/**
 * @file
 * Force chosen on form elements by adding the "webform-chosen" class to the element.
 */

Drupal.behaviors.webformChosen = {
  attach: function (context, settings) {
    jQuery('select.webform-chosen').chosen({
      disable_search_threshold: 20,
      allow_single_deselect: true,
      width: "100%",
    });
  }
};
