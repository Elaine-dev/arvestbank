(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.arvestMobile = {
    attach: function(context, settings) {
      var userAgent = (window.navigator.userAgent || '').toLowerCase();

      var ipad = userAgent.match(/ipad.+?os (\d+)/);
      var iphone = userAgent.match(/iphone(?:.+?os (\d+))?/);
      var ipod = userAgent.match(/ipod.+?os (\d+)/);
      if (ipad !== null || iphone !== null || ipod !== null) {
        window.location.replace(drupalSettings.appleUrl);
      }

      var android = /android/.test(userAgent) && /mobile/.test(userAgent);
      if (android === true) {
        window.location.replace(drupalSettings.googleUrl);
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
