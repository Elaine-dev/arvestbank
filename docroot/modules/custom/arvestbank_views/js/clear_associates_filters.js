(function ($) {
  Drupal.behaviors.associates = {
    attach: function (context, settings) {
      // Reset other exposed filters when one is selected/changed.
      $(document).once('associates-ajax').ajaxComplete(function (e, xhr, settings) {
        $('select[name="title"]').change(function() {
          $('select[name="field_location_value"]').val('').trigger("chosen:updated");
        });
        $('select[name="field_location_value"]').change(function() {
          $('select[name="title"]').val('').trigger("chosen:updated");
        });
      });
    }
  }
})(jQuery);
