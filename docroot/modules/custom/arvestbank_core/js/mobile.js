(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.arvestMobile = {
    attach: function(context, settings) {
      var userAgent = (window.navigator.userAgent || '').toLowerCase();

      var ipad = userAgent.match(/ipad.+?os (\d+)/);
      var iphone = userAgent.match(/iphone(?:.+?os (\d+))?/);
      var ipod = userAgent.match(/ipod.+?os (\d+)/);
      var android = /android/.test(userAgent) && /mobile/.test(userAgent);
      if (ipad !== null || iphone !== null || ipod !== null) {
        if (window.location.herf !== drupalSettings.appleUrl) {
          window.location.replace(drupalSettings.appleUrl);
        }
      }
      else if (android === true) {
        if (window.location.herf !== drupalSettings.googleUrl) {
          window.location.replace(drupalSettings.googleUrl);
        }
        window.location.replace(drupalSettings.googleUrl);
      }
      else {
        if (window.location.herf !== drupalSettings.fallbackUrl) {
          window.location.replace(drupalSettings.fallbackUrl);
        }
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
