/**
 * @file
 * Inserts phone number tokens into the CKEditor editing area.
 */

(function ($, Drupal, CKEDITOR) {
  // Register the plugin within the editor.
  CKEDITOR.plugins.add('phonenumber', {

    // Register the icons. They must match command names.
    icons: 'phonenumber',

    // The plugin initialization logic goes inside this method.
    init: function (editor) {

      // Define the editor command that inserts a timestamp.
      editor.addCommand('insertPhoneNumber', new CKEDITOR.dialogCommand('phoneNumberOptionsDialog'));

      // Create the toolbar button that executes the above command.
      editor.ui.addButton('phonenumber', {
        label: 'Insert Phone Number',
        command: 'insertPhoneNumber',
        toolbar: 'insert'
      });

      CKEDITOR.dialog.add('phoneNumberOptionsDialog', this.path + 'dialogs/phonenumberoptions.js');

    }
  });
})(jQuery, Drupal, CKEDITOR);
