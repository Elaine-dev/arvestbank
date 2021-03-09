# Drupal coding standards 

Drupal coding standards are version-independent and "always-current". \
All new code should follow the current standards, regardless of (core) version. \
Existing code in older versions may be updated, but doesn't necessarily have to be. 
Especially for larger code-bases (like Drupal core), updating the code of a previous version for the current standards may be too huge of a task. \
However, code in current versions should follow the current standards.

## General security guidance
Security is of paramount importance in the Platform. Every line of code must be written with security at the forefront of the developer's mind. All developers must be intimately familiar with and ensure that their custom code complies with the best practices outlined in the following security guidance pages:

* Drupal 8 Writing Secure Code: https://www.drupal.org/node/2489544
* Drupal Writing Secure Code: https://www.drupal.org/writing-secure-code​
* ​https://www.owasp.org/index.php/Top_10_2013-Top_10​
* ​https://www.owasp.org/index.php/AJAX_Security_Cheat_Sheet​
* https://www.owasp.org/index.php/Secure_Coding_Cheat_Sheet​

## IDE Setup Recommendations
* PHPStorm: https://confluence.jetbrains.com/display/PhpStorm/Drupal+Development+using+PhpStorm#DrupalDevelopmentusingPhpStorm-CoderandPHPCodeSnifferIntegration​
* Sublime:  https://www.drupal.org/docs/develop/development-tools/configuring-sublime-text
* Visual Studio: https://www.drupal.org/docs/develop/development-tools/configuring-visual-studio-code

## Drupal Development Guidelines

* No custom code will generate any PHP Warnings or Error messages. Always clear your local watchdog log and monitor the log for PHP Warning messages during development of your feature.
* All acceptance criterial on user stories will be implemented as one or more Behat (or PHPUnit when appropriate) tests 
* All bug tickets will have one or more behat (or phpunit) tests that reproduces the bug and then verifies that it has been resolved.
* All fields on entities will have meaningful and useful help text specified in the help/description field for the field definition. 
* All types, blocks, views, etc. will have meaningful machine names. Never leave a block or a view with a machine name like "block_1".
* All views should have appropriate tags associated with them for filtering the list of views. 
* When setting up content type display settings, any field that is for internal use such as an internal flag or taxonomy categorization should be excluded from all displays of the entity.

## Custom Module Guidelines

* All custom module names on Core platform will be prefixed with "projectname" and for all site specific custom modules the prefix will be based on naming convention as per brands.
* All custom profiles will be placed in the directory: /profiles/custom/ 
* All custom modules should be as narrowly focused/single responsibility as is practical.
* All custom modules should have a name that indicates that modules purpose, responsibility or area of focus.
* Generally speaking, the *.module file in custom modules should either be empty or contain just hook implementations. Any support functions or actual logic should be contained in classes that are leveraged by the module.
* Custom modules should not appear in the "Available Updates" listing page. Custom modules can be hidden from this list by implementing hook_update_projects_alter (https://api.drupal.org/api/drupal/core!modules!update!update.api.php/function/hook_update_projects_alter/8.2.x)
* This hook should be implemented in the profile i.e. platformprofile
* You would add a line to platformprofile_update_projects_alter();

## Views Guidelines

* In most cases, all view displays should have the advanced query option "Use Secondary Server" checked.
** This tells views to retrieve the view content from a secondary/replica MySQL server if one exists which improves the performance and scalability of the site by reducing the load on the primary OLTP MySQL server.
* All view displays should have a properly namespaced ID and/or machine name.
* Views generally speaking should include one or more administrative tags which makes it easier to locate them on the views administration screen.
* The cache settings on all views should be set to "Tag Based"
* All views must explicitly check access permission. At a minimum, the view should check that the user has the permission "View published content".
** This prevents the view from being accessible to non-administrative users if the site is in offline mode.

## JavaScript Coding Standards

As of Drupal 8, we use ESLint to make sure our JavaScript code is consistent and free from syntax error and leaking variables and that it can be properly minified. ESLint is able to detect errors and potential problems in JavaScript code. Ths ESLint tool is a node.js module and is integrated into a number of IDEs.
* ​http://eslint.org/docs/user-guide/getting-started - Installation.
* ​http://eslint.org/docs/user-guide/configuring - Configuration. Use the configuration files noted below.
* http://eslint.org/docs/user-guide/command-line-interface - CLI Usage
* ​http://eslint.org/docs/user-guide/integrations - IDE Support.
* The configuration files ship with Drupal 8 core and can be used from there.
* .eslintrc - http://cgit.drupalcode.org/drupal/tree/core/.eslintrc?h=8.1.x​
* .eslintignore - http://cgit.drupalcode.org/drupal/tree/.eslintignore?h=8.1.x
* The following set of configuration options has been agreed upon. (See ESLint change notice.) Configuration is improved when possible, always use the latest stable ESLint version.

## JavaScript Coding Guidelines
Custom JavaScript files should be defined to use the ECMAScript Strict mode. (See https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Strict_mode)
`"use strict";`

## PHP Coding Standards
The latest in Drupal PHP Coding standards can be read here. https://www.drupal.org/coding-standards Keeping your code up to standards is a learned skill. There are free tools, such as PHP Code Sniffer, at your disposal has support for Drupal coding standards. This tool works as a stand alone tool or can also be used in conjunction with your IDE such as PHP Storm. If your IDE doesn't support CodeSniffer you may also use the Coder module
* ​https://www.drupal.org/node/1419988 - How to install Code Sniffer with Drupal support - BLT comes with this out of the box.
* ​https://github.com/squizlabs/PHP_CodeSniffer - Project Code


## Differences in PHP7
Make sure you are aware of the changes between PHP 5.x and PHP7. There are several things that could be done in PHP5 that either work differently in PHP7 or don't work at all in PHP7. See the following references:
* ​http://www.phpclasses.org/blog/post/357-PHP-7-Migration-Guide-Part-1-10-Backwards-Incompatibile-Changes.html​
* ​http://www.phpclasses.org/blog/post/359-PHP-7-Migration-Guide-Part-2-19-New-Exciting-Features-In-PHP-7.html​
* http://www.phpclasses.org/blog/post/374-PHP-7-Migration-guide-Part-3-11-Changed-Functions-and-21-New-Functions.html​
* ​http://www.phpclasses.org/blog/post/380-PHP-7-Migration-Guide-Part-4-New-Classes-Interfaces-and-New-Global-Constants.html​

## Specific Guidelines
* All custom code files should include the PHP7 strict type handling declaration:
`declare(strict_types = 1);`
* All method parameters should specify a type hint.
* Only hook implementations should be in a custom module's *.module file. All other support functions should be in class files within the module.
* Avoid using deprecated functions or libraries whenever possible. If the function or library's documentation recommends an alternative approach, that approach should be used.
* Do not do variable assignments within a conditional check.
* Example of not allowed variable assignment inside of conditional. (Don't do this)
```
if ($my_var = $this->someFunction()) {
  // Do something.
}
```
* Do not leave commented out/dead code in your code files.
* Avoid leaving TODO comments in your code files. Either deal with the TODO as part of your work or add/update a github ticket with the necessary TODO action if it cannot be completed as part of your work.
* Avoid "magic numbers" (see http://www.c-sharpcorner.com/UploadFile/b7dc95/writing-clean-code-and-magic-numbers/)
* Always validate external input preferring a whitelist validation approach over a blacklist validation approach (see https://www.owasp.org/index.php/Input_Validation_Cheat_Sheet#White_List_Input_Validation)
* Detect, handle and log likely error/failure scenarios.
* Class files within modules generally should throw an exception that can be caught at a higher level (class files generally shouldn't know anything about the containing Drupal application except what is passed into them)
* Catch exceptions and log them to the watchdog.
* Evaluate exceptions and return an appropriate response to the user if the exception is non-fatal.
* Avoid nested function calls. Example (don't do this):
* `$my_var = $this->aFunction($this->anotherFunction());`
* Avoid function calls within conditionals. Example (don't do this):
```
if ($this->myFunction()) {
 // Do something.
 }
``` 
* Code copied into your custom module from another place must be updated to fully conform to the project coding standards and best practices.
* Configuration exports should never include sensitive information such as API Keys, Authentication tokens, usernames, or passwords.
* Custom code should never include sensitive information such as API keys, authentication tokens, usernames, or passwords.
* The only exception to this is for automated tests and mock services which may use dummy/test information that does not match any existing system authentication.
* All service endpoints exposed by the application must be properly versioned
* Don't change the datatype of a variable once it's defined. Example (don't do this):
```
$value = FALSE;
$value = $node->get('field_body')->value;
```

Variables within methods should include a type hint to facilitate code analysis if the variable type is not automatically determinable. \
Example of type hinting a method variable. 
```
 /* @var Drupal\Core\Entity\FieldableEntityInterface $node */
 $node = $variables['elements']['#node'];
```

## Developer Tools for ensuring coding standards
One of the ways to keep a track of coding standards is to configure the Local development IDE / tool to check for Coding standards while writing a piece of code or during compilation. Examples of one such Editors is :

* PHPStorm : PHPstorm provides a configuration to choose the pre-defined coding standard installed in the system or manually select the coding standard for each and every aspect while coding.
* Reference for setting up editors with Drupal coding standards : https://confluence.jetbrains.com/display/PhpStorm/Drupal+Development+using+PhpStorm​
* Netbeans Integration : https://www.drupal.org/node/1420008​
* Sublime Integration : https://www.drupal.org/docs/develop/development-tools/configuring-sublime-text​

