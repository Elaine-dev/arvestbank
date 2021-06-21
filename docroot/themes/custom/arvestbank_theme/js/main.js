(function ($, Drupal) {
  var regexp = /[®]/;
  $('body :not(script,sup)').contents().filter(function() {
    return this.nodeType === 3 && (regexp.test(this.nodeValue));
  }).replaceWith(function() {
    return this.nodeValue.replace("®", "<sup>&reg;</sup>");
  });
})(jQuery, Drupal);
