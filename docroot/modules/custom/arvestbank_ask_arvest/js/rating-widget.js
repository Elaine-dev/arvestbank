/**
 * @file
 */

Drupal.behaviors.arvestbankRatingWidget = {
  attach: function (context, settings) {

  // Loop over rating widgets on page.
  jQuery('.rating-widget').each(function (i,node) {

    // Make div a "raty" widget.
    jQuery(node).raty({
        hints: ['Poor', 'Not so much', 'Adequate', 'Good', 'Excellent!'],
        starOff : '/modules/custom/arvestbank_ask_arvest/js/raty-lib/images/big_grey.png',
        starOn : '/modules/custom/arvestbank_ask_arvest/js/raty-lib/images/big_blue.png',
        click: function (score, evt) {

          // Define the path for our rating endpoint.
          let requestUrl = '/ask-arvest/rate-answer/' + jQuery(this).attr('data-id') + '/' + score;

          // Get url params.
          // phpcs:disable
          params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v})
          // phpcs:enable

          // If we have a question pass it to the rating endpoint.
          if (typeof params.search != 'undefined') {
            requestUrl += '?search=' + params.search;
          }

          // If we have a suggestion pass it to the rating endpoint.
          if (typeof params.suggestion != 'undefined') {
            requestUrl += '&suggestion=' + params.suggestion;
          }

          // Make request to our rating endpoint.
          jQuery.ajax({
            url: requestUrl,
            dataType: "json",
            context: this,
          }).done(function () {
            // Show thank you text.
            jQuery(this).siblings('.rating-help-text').html('Thank You');
          });

        }

      });
    });

  }

}
