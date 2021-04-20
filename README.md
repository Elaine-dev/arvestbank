# Arvest Bank
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
Use BLT to setup the site with configuration.
   ```
   $ blt setup
   ```
   or pull in a full database with blt sync
   ```
   $ blt sync
   ```
   to pull files run
   ```
   blt sync:files
   ```


6. Create your local.settings.php file
Rsync any site configuration left out of the github repo to your local.setting.php file. You can review the script here: scripts/secret-settings-copy.sh
   ```
   $ cd project_root
   $ cp docroot/sites/default/settings/default.local.settings.php docroot/sites/default/settings/local.settings.php
   $ ./scripts/secret-settings-copy.sh
   ```

7. Log into your site with drush.
Access the site and do necessary work at local.arvestbank.com by running the following commands.
    ```
    $ cd docroot
    $ drush uli
    ```

Note the following properties of this project:
* Primary development branch: Develop
* Local site URL: https://local.arvestbank.com

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
