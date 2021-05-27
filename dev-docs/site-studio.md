# Site Studio

Documentation for the Site Studio Arvest build. 

## Before Development

  ```
  $ blt sync
  ``` 

## After Development

Export your Site Studio packages to commit your changes. Create a new package or edit an existing package in the admin UI.

### New Package
1. Navigate to 'Sync packages' in the Admin UI: /admin/cohesion/sync/packages
2. Click 'Add package' button: admin/cohesion/sync/packages/add
3. Add helpful title and description.
    - Title: `<SITE STUDIO ITEM TYPE> <ITEM TITLE> `. Example: `Component One Column Layout`
    - Machine name: (Automatic - adjust if necessary). Example: `pack_component_one_column_layout`
    - Description: 
      `A reusuable component for content with a 1 column layout.`
      `Updated: <YOUR NAME> <CURRENT DATE>`
4. Scroll to 'Package requirements' and select the Site Studio entity to include in the package.
5. Click 'Build package' button. 
6. Open 'Package contents' and only include the single Site Studio item you're exporting. If you have more than one item, create or edit a package for each one.
7. Click 'Save package' button.
8. Click 'Export package as file' button beside specific package. 
9. Rename the downloaded file from `cohesion_sync_package_(pack_component_one_column_layout)_component-one-columnn-layout.package.yml`
  to 
  `cohesion_sync_package_component-one-columnn-layout.package.yml_` Ensure your file has the trailing underscore.
10. Place your file in your Site Studio config split directory:
  `arvestbank/config/site_studio_sync/`
11. Edit the package .yml file to verify it contains no dependencies. It should look similar to:
  ```
-
  type: cohesion_sync_package
  export:
    uuid: 28ee2d06-24c4-45d9-9edf-2eb9bd19d899
    langcode: en
    status: true
    dependencies: {  }
    id: pack_component1_column_layout
    label: 'Component one column layout'
    description: "A reusuable component 1 column content layouts.\r\nAuthor: Aaron Parkening / Last update: 5/6/2021"
    excluded_entity_types: '[]'
    settings: '{"20b1e0a1-bcdf-4d30-ab99-3f44e192a74b":{"type":"cohesion_component","items":{"20b1e0a1-bcdf-4d30-ab99-3f44e192a74b":{"type":"cohesion_component"}},"checked":true}}'
-
  type: cohesion_component
  export:
    uuid: 20b1e0a1-bcdf-4d30-ab99-3f44e192a74b
    langcode: en
    status: true
    dependencies: {  }
    id: cpt_1_column_layout_component
    label: '1 column layout'
    json_values: '{{ [json string] }}'
        json_mapper: '{}'
    last_entity_update: entityupdate_0030
    locked: false
    modified: true
    selectable: true
    category: cpt_cat_layout_components
    preview_image: ''
    has_quick_edit: true
    entity_type_access: {  }
    bundle_access: {  }
    twig_template: component--cohesion-cpt-1-column-layout-component
    weight: 0
  ```
12. Add config file(s) to git and commit.

### Existing package

1. Navigate to 'Sync packages' in the Admin UI: /admin/cohesion/sync/packages
2. Edit the package that will contain the updated Site Studio item.
3. Update the description with your name and today's date: `Updated: <YOUR NAME> <CURRENT DATE>`
4. Follow the steps above from step 7.


## Deployment Steps for Site Studio Packages 
1. After code is merged go to the production site and go to https://arvestbank.prod.acquia-sites.com/admin/cohesion/sync/import
2. Import all package updates from the code base
3. packages must have 777 perms so check the perms first
4. sometimes you have to go into the style/component/template and hit resave or the styles wont show up
5. sometimes the file wont import `ssh arvestbank.prod@arvestbank.ssh.prod.acquia-sites.com`, navigate to /mnt/tmp/arvestbank and `rm -rf cohesion_sync_package_*`


## More Resources
- Partial configuration export documentation:
https://sitestudiodocs.acquia.com/6.5/user-guide/partial-configuration-export


### Site Studio Drush Commands

If you need finer control of Site Studio assets, use the commands below.
* `drush cohesion:rebuild` - Re-save all Site Studio settings, styles and templates.


## Coding Standards 

### General Rules 

* Only include numbers when using pixel values in fields -- don't include `px`. Site Studio will automatically convert numbers without units into rem units based on the _Site Studio > Website Settings > Base Unit Settings_ size.
* Include the name of any added id and class(es) to the Site Studio object title. This helps everyone understand which objects house id/class-based styles. Example: `Sidebar Column - .sidebar` as the name of the sidebar column container.

### Base Styles 

* Only use base styles for Global selectors and any items you do not want to include a prefix of `.coh-`.

### Custom Styles 

* All custom styles will get a prefix of `.coh-`. You can then add this markup to templates.
* Create a custom style when styling can be used elsewhere. Otherwise simply use the styles tab in the template, component, etc. to add in-line styles. (In-line styles aren't as performant as cached Custom Styles css.)

### Templates and Components

* When using an element with an available custom style, an easy way to encorporate the style is to edit the element and scroll down to 'Custom Style' to select the available style. If 'Custom Style' isn't an available option in the element, select the 'Properties' button and check 'Settings' > 'Custom Style'.
* When building a component, ensure it has help text in the component form builder, even if there are no editable fields. This gives context to future developers, site builders, and content authors. 
  * Several Helpers exist in the `Form builder helpers` category to jumpstart the form builder process.






