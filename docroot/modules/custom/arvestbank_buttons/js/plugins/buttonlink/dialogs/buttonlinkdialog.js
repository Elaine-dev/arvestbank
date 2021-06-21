/**
 * @file
 */

CKEDITOR.dialog.add('buttonLinkDialog', function (editor) {

  return {
    title: 'Enter Button Text',
    minWidth: 400,
    minHeight: 200,
    contents: [
      {
        id: 'tab-basic',
        label: 'Button Text',
        elements: [
          {
            type: 'html',
            html: '<h3>Please select a link to use this feature.</h3>'
          },
        ]
      },
    ],

    // Show dialog event.
    onShow: function () {

      // If selection includes an a tag.
      if (editor.getSelectedHtml().getHtml().includes('<a')) {
        // Create a dom object to inject.
        var buttonDomObject = editor.document.createElement('span');
        // Set the text of the dom object.
        buttonDomObject.setHtml(editor.getSelectedHtml().getHtml());
        // Add the button class.
        buttonDomObject.addClass('coh-style-link-arrow-button');
        // Insert our new token object.
        editor.insertElement(buttonDomObject);
        // Close dialog.
        CKEDITOR.dialog.getCurrent().hide();
      }

      // If the selection does not include an a tag.
      else {
        // Hide cancel button.
        document.getElementById(this.getButton('cancel').domId).style.display = 'none';
      }

    },

    // Accept dialog event.
    onOk: function () {}

  };
});
