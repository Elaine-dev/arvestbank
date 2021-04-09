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

### Base Styles 

### Custom Styles 

### Templates 

### Components
