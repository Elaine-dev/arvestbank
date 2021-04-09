# Site Studio

Documentation specifically to site studio for the Arvest build. 

## Import Assets and Rebuild

- `drush cohesion:import` - Import assets from the API
- `drush cohesion:rebuild` - Site Studio rebuild, this will re-save settings, styles and templates.

## Export and Import

If Acquia Site Studio sync is enabled, you can use the following commands.
- `drush sync:export` - Exports all configuration to the sync folder as a .yml_ file.
- `drush sync:export --filename-prefix=myfilenamehere` - Exports all configuration to the sync folder as a .yml_ file with a custom filename rather than using the website name.
- `drush sync:import --overwrite-all` - Imports all configuration from the sync folder and overwrite existing Site Studio configuration.
- `drush sync:import --keep-all` - Imports all Site Studio configuration from the sync folder and keeps existing Site Studio configuration and only imports new configuration.


## Coding Standards 

### General Rules 

* When using pixel values in fields, don't include `px`. Site Studio will automatically convert numbers without units into rem units based on the base unit size.
* Include the name of any added id and class(es) to the Site Studio object title. This helps everyone understand which objects house id/class-based styles. Example: `Sidebar Column - .sidebar` as the name of the sidebar column container.

### Base Styles 

* Only use base styles for Global selectors and items you do not want a prefix of `.coh-`

### Custom Styles 

* All custom styles will get a prefix of `.coh-`. You can then add this markup to templates.

### Templates 

* If styles are only ever going to be used on the template, put styles inside the template. 


### Components





