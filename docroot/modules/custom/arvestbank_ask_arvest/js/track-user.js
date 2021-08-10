/**
 * @file
 */

Drupal.behaviors.arvestbankUserTracking = {
  attach: function (context, settings) {
    if (!jQuery("div").hasClass("coh-style-specialty-debit-cards-view")) {
      jQuery("[data-drupal-selector='edit-search']").click(function(){
        // Make request to our tracking endpoint.
        ajaxEndpoint();
      });
    }
    else {
      // Make request to our tracking endpoint.
      ajaxEndpoint();
    }
    
    function ajaxEndpoint() {
      jQuery.ajax({
        url: '/ask-arvest/track-user',
        dataType: "json",
      });
    }
  }
}
