# My Project
This repository consists of the Arvest Bank Drupal 9 with Site Studio build.

# Getting Started
This project is based on BLT, an open-source project template and tool that enables building, testing, and deploying Drupal installations following Acquia Professional Services best practices. While this is one of many methodologies, it is our recommended methodology.

1. Review the [Required / Recommended Skills](https://docs.acquia.com/blt/developer/skills/) for working with a BLT project.
2. Ensure that your computer meets the minimum installation requirements (and then install the required applications). See the [System Requirements](https://docs.acquia.com/blt/install/).
** At this time it use `composer version 1`

3. Request access to organization that owns the project repo in GitHub (if needed).
4. Fork the project repository in GitHub.
5. Request access to the Acquia Cloud Environment for your project (if needed).
6. Setup a SSH key that can be used for GitHub and the Acquia Cloud (you CAN use the same key).
    1. [Setup GitHub SSH Keys](https://help.github.com/articles/adding-a-new-ssh-key-to-your-github-account/)
    2. [Setup Acquia Cloud SSH Keys](https://docs.acquia.com/acquia-cloud/ssh/generate)
7. Clone your forked repository. By default, Git names this "origin" on your local.
    ```
    $ git clone git@github.com:<account>/arvestbank.git
    ```
8. To ensure that upstream changes to the parent repository may be tracked, add the upstream locally as well.
    ```
    $ git remote add upstream git@github.com:acquia-pso/arvestbank.git
    ```

----
# Setup Local Environment.

BLT provides an automation layer for testing, building, and launching Drupal 8 applications. For ease when updating codebase it is recommended to use  Drupal VM. If you prefer, you can use another tool such as Docker, [DDEV](https://docs.acquia.com/blt/install/alt-env/ddev/), [Docksal](https://docs.acquia.com/blt/install/alt-env/docksal/), [Lando](https://docs.acquia.com/blt/install/alt-env/lando/), (other) Vagrant, or your own custom LAMP stack, however support is very limited for these solutions.
1. Install Composer dependencies.
After you have forked, cloned the project and setup your blt.yml file install Composer Dependencies. (Warning: this can take some time based on internet speeds.)
    ```
    $ composer install
    ```
2. Setup VM.
Setup the VM with the configuration from this repositories [configuration files](#important-configuration-files).

    ```
    $ vagrant up
    ```

3. Setup a local blt alias.
If the blt alias is not available use this command outside and inside vagrant (one time only).
    ```
    $ composer run-script blt-alias
    ```

4. SSH into your VM.
SSH into your localized Drupal VM environment automated with the BLT launch and automation tools.
    ```
    $ vagrant ssh
    ```

5. Setup a local Drupal site with an empty database.
Use BLT to setup the site with configuration.  If it is a multisite you can identify a specific site.
   ```
   $ blt setup
   ```
   or
   ```
   $ blt setup --site=[sitename]
   ```


6. Create your local settings.php file 
Rsync API key variables to your local.setting.php file. You can review the script here: scripts/secret-settings-copy.sh
   ```
   $ cd project_root
   $ cp /docroot/sites/example.settings.local.php docroot/sites/settings.local.php 
   $ ./secret-settings-copy.sh
   ```

7. Log into your site with drush.
Access the site and do necessary work at #LOCAL_DEV_URL by running the following commands.
    ```
    $ cd docroot
    $ drush uli
    ```

---
## Other Local Setup Steps

1. Set up frontend build and theme.
By default BLT sets up a site with the lightning profile and a cog base theme. You can choose your own profile before setup in the blt.yml file. If you do choose to use cog, see [Cog's documentation](https://github.com/acquia-pso/cog/blob/8.x-1.x/STARTERKIT/README.md#create-cog-sub-theme) for installation.
See [BLT's Frontend docs](https://docs.acquia.com/blt/developer/frontend/) to see how to automate the theme requirements and frontend tests.
After the initial theme setup you can configure `blt/blt.yml` to install and configure your frontend dependencies with `blt setup`.

2. Pull Files locally.
Use BLT to pull all files down from your Cloud environment.

  ```
  $ blt drupal:sync:files
  ```

3. Sync the Cloud Database.
If you have an existing database you can use BLT to pull down the database from your Cloud environment.
   ```
   $ blt sync
   ```

---
# To start developing every time 

1. Pull from the github repository 
```
git pull upstream develop
```

2. Create a new feature branch from develop
```
git checkout -b arvestbank-000-feature-branch
```

3. Install Composer dependencies.
After you have forked, cloned the project and setup your blt.yml file install Composer Dependencies. (Warning: this can take some time based on internet speeds.)
    ```
    $ composer install
    ```
5. Setup VM or docker local
Setup the VM with the configuration from this repositories [configuration files](#important-configuration-files).

    ```
    $ vagrant up
    ```
    
5. SSH into your VM or docker local.
SSH into your localized Drupal VM environment automated with the BLT launch and automation tools.
    ```
    $ vagrant ssh
    ```

6. Setup a local Drupal site with an empty database. The blt-cohesion composer package will run all necessary site studio commands. 
Use BLT to setup the site with configuration.
   ```
   $ blt setup
   ```
   or  If it is a multisite you can identify a specific site.
   ```
   $ blt setup --site=[sitename]
   ```

7. Log into your site with drush.
Access the site and do necessary work at #LOCAL_DEV_URL by running the following commands.
    ```
    $ cd docroot
    $ drush uli
    ```
    


### To Create a Pull Request. 

1. After you make changes inside your local drupal site. Export your configuration from the database to your configuration. 
 Export your drupal config changes if you have them. 
 ```
 drush cex
```
To export Site studio configuration to your site studio package run the following command.
 ```
drush sync:export
```

2. commit your changes and push your changes to your origin repository. 
```
git status
git add -p
git commit -m"JIRA-000: Committing new changes to site studio."
git push --set-upstream origin jira-000-new-site-studio-change
```

3. Navigate to Github and open a pull request against the upstream. Assign a person on your team to review.
  


# Resources

Additional [BLT documentation](https://docs.acquia.com/blt/) may be useful. You may also access a list of BLT commands by running this:
```
$ blt
```

Note the following properties of this project:
* Primary development branch: Develop
* Local site URL: http://local.arvestbank.com

## Working With a BLT Project
BLT projects are designed to instill software development best practices (including git workflows). \
Our BLT Developer documentation includes an [example workflow](https://docs.acquia.com/blt/developer/dev-workflow/).

### Important Configuration Files
BLT uses a number of configuration (`.yml` or `.json`) files to define and customize behaviors. Some examples of these are:

* `blt/blt.yml` (formerly blt/project.yml prior to BLT 9.x)
* `blt/local.blt.yml` (local only specific blt configuration)
* `box/config.yml` (if using Drupal VM)
* `drush/sites` (contains Drush aliases for this project)
* `composer.json` (includes required components, including Drupal Modules, for this project)
