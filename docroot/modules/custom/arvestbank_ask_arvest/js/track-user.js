/**
 * @file
 */

Drupal.behaviors.arvestbankUserTracking = {
  attach: function (context, settings) {

    jQuery("[data-drupal-selector='edit-search']").click(function(){
      // Make request to our tracking endpoint.
      jQuery.ajax({
        url: '/ask-arvest/track-user',
        dataType: "json",
      });
    });
  }
}
