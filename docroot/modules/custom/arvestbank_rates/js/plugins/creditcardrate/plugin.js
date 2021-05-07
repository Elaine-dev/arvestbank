/**
 * @file
 * Inserts card rate tokens into the CKEditor editing area.
 */

(function ($, Drupal, CKEDITOR) {
  // Register the plugin within the editor.
  CKEDITOR.plugins.add('creditcardrate', {

    // Register the icons. They must match command names.
    icons: 'creditcardrate',

    // The plugin initialization logic goes inside this method.
    init: function (editor) {

      // Define the editor command that inserts a timestamp.
      editor.addCommand('insertCreditCardRate', new CKEDITOR.dialogCommand('rateOptionsDialog'));

      // Create the toolbar button that executes the above command.
      editor.ui.addButton('creditcardrate', {
        label: 'Insert Credit Card Rate',
        command: 'insertCreditCardRate',
        toolbar: 'insert'
      });

      CKEDITOR.dialog.add('rateOptionsDialog', this.path + 'dialogs/rateoptions.js');

    }
  });
})(jQuery, Drupal, CKEDITOR);
