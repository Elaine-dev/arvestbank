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
        starOn	: '/modules/custom/arvestbank_ask_arvest/js/raty-lib/images/big_blue.png',
        click: function (score, evt) {

          // Get rest endpoint from drupalSettings.
          var restEndpoint = drupalSettings.arvestbank_ask_arvest.json_endpoint;

          // Add variables to the restEndpoint.
          var requestUrl =
            restEndpoint
            + '?interfaceID=2'
            + '&sessionId='
            + '&requestType=RatingRequest'
            + '&responseID='
            + '&uuid=' + jQuery(this).attr('data-id')
            + '&rating=' + score
            + '&source='
            + '&question=';
          // @todo make call to send rating to [24]7.ai
        }

      });
    });

  }

}
