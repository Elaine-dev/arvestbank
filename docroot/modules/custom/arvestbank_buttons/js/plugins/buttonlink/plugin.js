/**
 * @file
 * Inserts a button into the CKEditor editing area.
 */

(function ($, Drupal, CKEDITOR) {

  // Register the plugin within the editor.
  CKEDITOR.plugins.add('buttonlink', {

    // Register the icons. They must match command names.
    icons: 'ButtonLink',

    // The plugin initialization logic goes inside this method.
    init: function (editor) {

      // Define the editor command that inserts a timestamp.
      editor.addCommand('openButtonLinkDialog', new CKEDITOR.dialogCommand('buttonLinkDialog'));

      // Create the toolbar button that executes the above command.
      editor.ui.addButton('ButtonLink', {
        label: 'Style Link as Button',
        command: 'openButtonLinkDialog',
        toolbar: 'insert'
      });

      CKEDITOR.dialog.add('buttonLinkDialog', this.path + 'dialogs/buttonlinkdialog.js');

    }
  });
})(jQuery, Drupal, CKEDITOR);
