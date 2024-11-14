# WordPress Site Starter

This is a WordPress starter project that includes a basic custom theme, including some essential custom components, and a project setup designed for fast local setup and development.

## Requirements
* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative like OrbStack)
* For ACF Pro, create an `auth.json` file in the project root directory, which can be downloaded from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/).

## Using this Project

To begin using this on a new project, simply call the following command from the root of your project:

```bash
composer create-project viget/wordpress-site-starter .
```

Follow the prompts to set up your project with the desired information. You will be asked:

1. **The name of the project** (`My Project`): This will serve as the name of the WordPress Theme.
2. **The project slug** (`my-project`): This will be used as the directory name for the theme as well as the local DDEV site name.
3. **The text domain** (`my-project`): This will be used for internationalization and localization and defaults to the project slug.
4. **The project Package name** (`MyProject`): This is referenced in the PhpDoc blocks and default project namespace. It defaults to the project name.
5. The function prefix (`my_project_`): This is used to prefix all custom theme functions and defaults to the project slug.

## Automated Setup

Following the series of prompts, the project will be set up with the following:

1. **Composer Dependencies**: All necessary dependencies for WordPress and the theme.
2. **WordPress Core**: The latest version of WordPress will be downloaded.
3. **Local Development Environment**: A DDEV configuration file will be created and the local environment will be started.
4. **Theme Setup**: The theme will be set up with the project name and slug.
5. **ACF Pro**: If an `auth.json` file is present in the project root, ACF Pro will be installed.
6. **`package.json` Dependencies**: All necessary script and style build dependencies for the theme will be installed and initialized.
7. **Cleanup**: Any setup files will be removed and the project will be ready for development.

After the setup is complete, it is recommended to perform your initial commit and push to your project repository.

## Pushing to your Project Repository

After the project is set up, you can run the following commands to add your remote repository and push your initial commit:

```bash
git init
git add .
git commit -m "Initial Commit"
git remote add origin <git@github.com:YOURGITHUB/your-repo.git>
git push -u origin master
```

Be sure to update the origin with the correct remote repository URL (and remove the `<>` brackets).

# Development

The following command will create a directory (`project-name`) and run `composer create-project` using your local repository as the source. Be sure to replace the path with the correct path to your local repository.

Also, in `packages.json`, you can change the name of the branch from `main` to use your current working branch.

**Note:** Be sure to update the paths in `packages.json` as well as the command below to point to the location of your local repository. `~/` will not work, you must use the full path.

```bash
mkdir project-name && cd project-name && composer create-project --repository-url="/root/path/not/relative/path/to/wordpress-site-starter/packages.json" viget/wordpress-site-starter . --stability=dev --remove-vcs --no-install
```

You can quickly remove the project by using:
```bash
ddev delete project-name -O -y && cd ../ && rm -rf project-name
```

## Changelog

### v1.0.11
* Fixed command for checking if WordPress database is installed.

### v1.0.10
* Fixed a bug preventing WordPress Database install.
* Some relatively harmless error message suppression.

### v1.0.9
* Alternative method for identifying elements on dark background
* Cleanup Navigation Container component, moved styles into CSS file.
* Remove "Dark" label from colors prefixed with `dark-`
* Added default logo
* Added Icon Only Button Style
* Added more block support in the Navigation Container component (Site Logo, Buttons, Search, Spacer, and Separator)
* Added new filter to unregister core block styles: `acfbt_unregister_block_styles`
* PostInstall Enhancements
* Improvements to the Color Palette feature
* Moved some core functionality from the theme to the MU Plugin
* Block Icon Support for the Read More Block
* Disable the Template Part wrapper div when using Template parts
* Implemented ACF Blocks Toolkit API function `acfbt()` to access functionality from ACF BT.

### v1.0.8
* Allow `core/search` block to be nested within the Navigation Container block.
* Many updates to deployment
* Removal of `accentColor`
* Removal of all variations of pre-defined gray.

### v1.0.7
* Added Custom Video Player Component.
* Replaced SVG Support with Safe SVG.
* Fixed issue with deployment script.
* Other post-create-project cleanup.

### v1.0.6
* Added 2 new breakpoints: `wp-cols` and `mobile-menu`.
* Added Full Site Editor style support.
* Added Button Icon support for the Post Excerpt block.
* Added Default templates for Archive and Singular posts.
* Fixed duplicate admin notice.
* Fixed a deployment issue where a package was missing.
* Automated the WordPress color palette from the Tailwind config.
* Reorganized some of the source files.
* Updated Dependencies
* Other minor bug fixes, corrections, and code comments.

### v1.0.5
* Fixed issue where WordPress was not installing correctly.
* Fixed issue where Viget WP Composer installation was not working.

### v1.0.4
* Moving to theme colors to vars to make it easy to update the global accent color
* Add more into to the theme README so it is easy for developer to know how to update/edit items
* Fixing placeholder bug, adding phpcs file for bin/composer-scripts
* Fixing button outline border bug
* Adding admin username as an option to the composer post install
* Moving the deleting of themes and plugins to after DB install

### v1.0.3
* Fixed README and packages.json to reference correct package.
* Removed `vendor` directory after create-project is complete.

### v1.0.2
* Restore `packages.json` file.
* Added `composer.lock`

### v1.0.1
* Remove `packages.json` file.

### v1.0.0
* Last attempt to get packagist working.

### v0.1.7
* Another test to get packagist working.

### v0.1.6
* Testing different tagging style.

### v0.1.5
* Styling the Core WordPress blocks.
* Updating default justification on the Custom logo grid block.
* Styling the global unordered list style.

### v0.1.4
* Post Create-Project WordPress Installation
* Post WordPress Installation General Cleanup
* Automatic Theme and Plugin Activation

### v0.1.3
* Fix issue where Vite was crashing web server
* Rearranged a few things/commands to work better.
* Adjustments to comment formatting.
* Added a "wait" to account for slow network traffic.

### v0.1.2
* Better Composer Script Handling
* Improvements to post-create project setup
* Cleaner Initialization of Vite

### v0.1.1
* Composer Script Updates
* Vite/Tailwind/DDEV Updates

### v0.1.0
* Initial Release
