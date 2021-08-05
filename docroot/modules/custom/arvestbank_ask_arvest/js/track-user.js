/**
 * @file
 */

Drupal.behaviors.arvestbankUserTracking = {
  attach: function (context, settings) {
    // Make request to our tracking endpoint.
    jQuery.ajax({
      url: '/ask-arvest/track-user',
      dataType: "json",
    });
  }
}
