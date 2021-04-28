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
3. Add helpful title and description. Example:
    - Title: `Component One Column Layout `
    - Machine name: `pack_component_one_column_layout`
    - Description: 
      `A reusuable component for content with a 1 column layout.`

      `Author: <YOUR NAME> / Last update: <CURENT DATE>`
4. Scroll down to 'Package requirements' and select the Site Studio and Drupal entity to include in the package.
5. Click 'Build package' button. 
6. Open 'Package contents' and remove all dependencies.
7. Click 'Save package' button, which will trigger a download.
8. Rename the downloaded file from `cohesion_base_styles_(heading_1)_heading-1.package.yml`
  to 
  `cohesion_base_styles_heading_1.package.yml_` Ensure your file has the trailing underscore.
9. Place your file in your Site Studio config split directory:
  `arvestbank/config/site_studio_sync/cohesion_base_styles.heading_1.yml_`
10. Edit the package .yml file to remove dependencies. It should look similar to:
  ```
  type: cohesion_base_styles
  export:
    uuid: d5d047c7-041b-4cf5-9969-27c0352e7695
    langcode: en
    status: true
    dependencies: {  }
    id: heading_1
    label: 'Heading 1'
    json_values: '{{ [json string] }}'
    last_entity_update: entityupdate_0030
    locked: false
    modified: true
    selectable: false
    custom: null
  ```
11. Add config file(s) to git and commit.

### Existing package

1. Navigate to 'Sync packages' in the Admin UI: /admin/cohesion/sync/packages
2. Edit the package that will contain the updated code.
3. Update the description with your name and today's date: `Author: <YOUR NAME> / Last update: <CURENT DATE>`
4. `Author: <YOUR NAME> / Last update: <CURENT DATE>`
5. Click 'Save package' button, which will trigger a download.
6. Rename the downloaded file from `cohesion_base_styles_(heading_1)_heading-1.package.yml`
  to 
  `cohesion_base_styles_heading_1.package.yml_` Ensure your file has the trailing underscore.
7. Place your file in your Site Studio config split directory:
  `arvestbank/config/site_studio_sync/cohesion_base_styles.heading_1.yml_`
8. Edit the package .yml file to remove dependencies. It should look similar to:
  ```
  type: cohesion_base_styles
  export:
    uuid: d5d047c7-041b-4cf5-9969-27c0352e7695
    langcode: en
    status: true
    dependencies: {  }
    id: heading_1
    label: 'Heading 1'
    json_values: '{{ [json string] }}'
    last_entity_update: entityupdate_0030
    locked: false
    modified: true
    selectable: false
    custom: null
  ```
9. Add config file(s) to git and commit.



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

### Templates 

* Keep styles inside a template if they won't be used elsewhere. This helps with maintainability and portability.


### Components






