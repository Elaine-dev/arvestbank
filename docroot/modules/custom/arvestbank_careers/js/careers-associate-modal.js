// Loading this js will load the careers associate modal on page load.
(function ($, Drupal, drupalSettings) {
  $(document).ready(function () {
    var ajaxSettings = {
      url: '/careers/apply/associate/modal'
    };
    var myAjaxObject = Drupal.ajax(ajaxSettings);
    myAjaxObject.execute();
  });
})(jQuery, Drupal, drupalSettings);
