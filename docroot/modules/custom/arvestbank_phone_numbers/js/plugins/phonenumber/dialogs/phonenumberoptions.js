/**
 * @file
 */

CKEDITOR.dialog.add('phoneNumberOptionsDialog', function (editor) {

  return {
    title: 'Select a Phone Number to Insert',
    minWidth: 400,
    minHeight: 200,
    contents: [
      {
        id: 'tab-basic',
        label: 'Select a Phone Number to Insert',
        elements: [
          {
            id: 'phonenumberselect',
            label: 'Select a Phone Number to Insert',
            type: "select",
            items: editor.config.phonenumber_tokens,
            validate: CKEDITOR.dialog.validate.notEmpty("Phone number selection cannot be empty.")
          },
        ]
      },
    ],

    onOk: function () {
      // Get this dialog.
      var dialog = this;
      // Create a dom object to inject.
      var tokenDomObject = editor.document.createElement('span');
      // Calculate text for token.
      var selectedToken = dialog.getValueOf('tab-basic', 'phonenumberselect');
      // Set the text of the token dom object.
      tokenDomObject.setText(selectedToken);
      // Add a class in case we need it later.
      jQuery(tokenDomObject).addClass("phone-number-token");
      // Insert our new token object.
      editor.insertElement(tokenDomObject);
    }

  };
});
