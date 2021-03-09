# Config Split

Config Split is the "standard" Configuration Management Strategy provided by BLT. BLT does support other options for configuration management (or none at all). See [Configuration Management](configuration-management.md)).

For additional details, please review the [Managing Configuration with Config Split](https://support.acquia.com/hc/en-us/articles/360024009393) article on the Acquia Knowledge Base.

# Example Scenarios

For the sake of this tutorial, let's assume that we have a "kitchen sink" site that requires all of these types of splits. It is a multisite application and it will be hosted in multiple environments, which must share some default configuration between all sites. It also must allow some features (like a blog) to be enabled on some sites and not others.

# Default configuration

Let's start out by exporting the default configuration for the application. This is the configuration that will be imported for your application by default, even if no splits are defined or active. It will be shared by all sites using this application.

For the sake of this tutorial, let's focus on one particular configuration setting: `system.performance`. This controls caching and aggregation settings for Drupal core.

1.  Navigate to `/admin/config/development/performance` and enable caching and aggregation
1. `drush en config_split -y`
1. `drush config-export -y`
1. `drush cr`

This will populate `../config/default` with all configuration for the site.  `../config/default/system.performance.yml` should now exist and contain the following configuration (this is a partial representation):

        cache:
          page:
            max_age: 3600
        css:
          preprocess: true
          gzip: true
        js:
          preprocess: true
          gzip: true

### Test overriding and reverting

You can test the process of importing configuration by:

1. Navigating to `/admin/config/development/performance`
2. Disabling caching and aggregation
3. Executing `drush config-import`

You should then find that caching and aggregation have been re-enabled, congruent with the previously exported configuration.

This is the simplest use case for the configuration management system.

# Environment split

By default, we want caching and CSS and JavaScript aggregation enabled on all of our environments. However, we would like the local environment to be an exception to this. Caching and aggregation should be disabled on local machines to expedite the development process.

To accomplish this, we will create a "local" configuration split. The command `blt recipes:config:init:splits` will create this and your other environment splits for you automatically. To create the "local" split manually, do the following:

1. `mkdir -p ../config/envs/local`
1. Navigate to `/admin/config/development/configuration/config-split/add`
1. In the UI, fill in the following fields and then save:
    * label: Local
    * folder: ../config/envs/local
    * Conditional Split > Configuration items > Select "system.performance"
1. `drush config-export -y`. This will export the configuration definition for the split itself,  which is stored in `config/default/config_split.config_split.local.yml`. The file should contain the following settings:

        uuid: ...
        langcode: en
        status: true
        dependencies: {  }
        id: local
        label: Local
        folder: ../config/envs/local
        module: {  }
        theme: {  }
        blacklist: {  }
        graylist:
          - system.performance
        graylist_dependents: true
        graylist_skip_equal: true
        weight: 0

1. `drush cr`. Doing this will allow configuration split to recognize that the local split is active. We rely on BLT to designate this split as active on local machines via a [settings.php include](https://github.com/acquia/blt/blob/10.x/settings/config.settings.php#L22).

With your "local" split ready, continue:

1.  Navigate to `/admin/config/development/performance` and disable caching and aggregation.
1. `drush csex`. Because the local split is active, this will export the local split  `system.performance` settings to `../config/envs/local/system.performance.yml`. It should contain the following configuration:

        cache:
        page:
          max_age: 0
        css:
        preprocess: false
        gzip: false
        js:
        preprocess: false
        gzip: false

#### Supported environment splits

BLT has built-in support for the following environment splits:

| Split    | Environment                                  | File path               |
|----------|----------------------------------------------|-------------------------|
| local    | any non-Acquia, non pipelines environment    | ../config/envs/local    |
| ci       | Acquia Pipelines                             | ../config/envs/ci       |
| ode      | Acquia ODE                                   | ../config/envs/ode      |
| dev      | Acquia Dev                                   | ../config/envs/dev      |
| stage    | Acquia Staging                               | ../config/envs/stage    |
| prod     | Acquia Prod                                  | ../config/envs/prod     |
| ah_other | any Acquia environment not listed above      | ../config/envs/ah_other |

However, BLT will only mark these splits as enabled _if they exist_. It will not create the splits for you.

#### A few notes regarding the settings

- The folder is relative to the drupal docroot.
- We set active to zero because we don't want configuration management to manage whether this split is active. Instead, we will rely on BLT to enable this split, when appropriate, via a [settings.php include](https://github.com/acquia/blt/blob/10.x/settings/config.settings.php#L22).  If you are using BLT, this should already be loaded for you as a consequence of including `blt.settings.php` in your `settings.php` file. However, you may override this logic by setting `$split` in `settings.php` prior to including `blt.settings.php`.
- You may see that even on your local environment, after running `drush config-import`, the local configuration split has a status of "active (overwritten)". This is normal and does not indicate a problem. The mere fact that it is active is an override of the exported `active: 0` setting in the split itself. It does not necessarily indicate that the configuration which the split controls is actually overridden.

## Enabling a feature split

Let's assume that you would like to enable the blog split for multisite Site 2. To enable the blog feature:

- Visit `/admin/config/development/configuration/config-split` and enable the Blog split.
- Import configuration for site2 via `drush config-import --uri=site2`.

### Issues with this approach

- The status of the feature split is not managed via configuration management. Therefore, you must enable the split by using the user interface even on a production environment.
- It can be very difficult to identify all of the configuration that a given feature should encompass.
- It is not entirely possible to disentangle a single feature from all related configuration. For instance, we may segment the node configuration and fields for the "Blog" feature. However, `config/default/search_api.index.content.yml` and `config/default/views.view.search.yml` still contain references to `blog_entry` in their exported configuration. This is not necessarily problematic, but it feels a little messy.

## Creating a site split

1. Execute `mkdir -p ../config/dev` to ensure that we have the following configuration directories:
  - config/sync
  - config/dev
1. Create a split for dev:

        status: false
        label: Site 2
        folder: ../config/dev
        blacklist: {  }
        graylist: {  }
        graylist_dependents: true
        graylist_skip_equal: true
        weight: 0

# Misc

### Exporting to a split that is not active

When developing locally, we often need to export to a split other than local. For instance, we may want to change some of the configuration in the dev split. There are a few ways to do this:

 - Manually modify the configuration files. This is the simplest and most straightforward, but it can be very time-consuming.
 - Use the `drush config-split-export [split]` command to export to a specific split. For instance, to export the current configuration on your local machine to the dev split, he would execute `drush config-split-export dev`

### Conflicting configuration

**Higher weight takes precedence**

Where possible, you should avoid exporting the same configuration (with different values) to multiple splits. However, this is sometimes desirable. When two splits define the same configuration be split with the higher weight will take precedence. This is somewhat counterintuitive, as the common Drupal convention is for elements with lesser weights to take precedence.

**Multiple splits blacklist the same configuration**

If you would like to export configuration that is blacklisted in more than one split, then you will need to use the `drush config-split-export [split]`  commands and specify the split to which you would like configuration to be exported.

### Terminology

**Complete Split (blacklist)**

Blacklisted splits are blacklisted from `config/default`. If a given split is active, and that Split defines a configuration setting in its blacklist, that configuration setting will not be exported to `config/default` when `drush config-export` is executed:

- Exported to split
- *Not* exported to default configuration

**Conditional Split (graylist)**

Graylist splits allow a given configuration setting to be exported to both the default configuration and also to a split's configuration (overriding default when active):

- Exported to split
- Also exported to default configuration

Graylists may also be used for configuration that's intended to be "unlocked" in production (such as webforms). If you need to customize this behavior, you can use the graylist functionality described in [this blog post](https://blog.liip.ch/archive/2017/04/07/advanced-drupal-8-cmi-workflows.html).

### Development settings

To disable the plug-in discovery cache, add the following to your local.settings.php file:
 `$settings['cache']['bins']['discovery'] = 'cache.backend.null';`

This will obviate the need to clear caches in order to register a status change in a configuration split.

# Resources

* [blog post by Jeff Geerling](https://www.jeffgeerling.com/blog/2017/adding-configuration-split-drupal-site-using-blt-and-acquia-cloud)
* [Configuration split](https://www.drupal.org/project/config_split)
* [Configuration ignore](https://www.drupal.org/project/config_ignore)
s
