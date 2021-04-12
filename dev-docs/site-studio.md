# Site Studio

Documentation specifically to site studio for the Arvest build. 

## Development Changes
1. Use BLT to get the latest Site Studio package. BLT automatically imports and rebuilds the latest Site Studio assets through the [BLT Site Studio plugin](https://github.com/davidtrainer/blt-site-studio).
  ```
  $ blt setup 
  or 
  $ blt sync
  ``` 
2. After making your Site Studio changes, export and commit the Site Studio package.yml.
  ```
  $ drush sync:export
  ```

### Site Studio Drush Commands

If you need finer control of Site Studio assets, use the commands below.
* `drush cohesion:import` - Import assets from the API
* `drush cohesion:rebuild` - Re-save all Site Studio settings, styles and templates.

When Acquia Site Studio sync is enabled, export and import with more granularity:
* `drush sync:export` - Exports all configuration to the sync folder as a .yml_ file.
* `drush sync:export --filename-prefix=myfilenamehere` - Exports all configuration to the sync folder as a .yml_ file with a custom filename rather than using the website name.
* `drush sync:import --overwrite-all` - Imports all configuration from the sync folder and overwrite existing Site Studio configuration.
* `drush sync:import --keep-all` - Imports all Site Studio configuration from the sync folder and keeps existing Site Studio configuration and only imports new configuration.


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






