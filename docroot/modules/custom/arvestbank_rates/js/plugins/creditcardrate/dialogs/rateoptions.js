/**
 * @file
 */

CKEDITOR.dialog.add('rateOptionsDialog', function (editor) {

  console.log(editor.config.creditcardrate_tokens);

  return {
    title: 'Select a Rate to Insert',
    minWidth: 400,
    minHeight: 200,
    contents: [
      {
        id: 'tab-basic',
        label: 'Select a Rate to Insert',
        elements: [
          {
            id: 'rateselect',
            label: 'Select a Rate to Insert',
            type: "select",
            items: editor.config.creditcardrate_tokens,
            validate: CKEDITOR.dialog.validate.notEmpty("Rate selection cannot be empty.")
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
      var tokenText = dialog.getValueOf('tab-basic', 'rateselect');
      // Get machine name for the token.
      tokenText = '[arvestbank_rates:' + tokenText.toLowerCase().replaceAll(" - ", "__") + ']';
      tokenText = tokenText.replaceAll(" ", "_");
      // Set the text of the token dom object.
      tokenDomObject.setText(tokenText);
      // Add a class in case we need it later.
      jQuery(tokenDomObject).addClass("card-rate-token");
      // Insert our new token object.
      editor.insertElement(tokenDomObject);
    }

  };
});
