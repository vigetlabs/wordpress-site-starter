# WordPress Site Starter

This is a WordPress starter project that includes a basic custom theme, including some essential custom components, and a project setup designed for fast local setup and development.

## Requirements
* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative)
* For ACF Pro, create an `auth.json` file in the project root directory, which can be downloaded from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/).

## Using this Project

To begin using this on a new project, simply call the following command from the root of your project:

```bash
$ composer create-project vigetlabs/wordpress-site-starter
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

# Development

The following command will create a directory (`project-name`) and run `composer create-project` using your local repository as the source. Be sure to replace the path with the correct path to your local repository.

Also, in `packages.json`, you can change the name of the branch from `main` to use your current working branch.

```bash
$ mkdir project-name && cd project-name && composer create-project --repository-url="/Users/briandichiara/Sites/wpstarter.vigetx.com/public_html/packages.json" vigetlabs/wordpress-site-starter . --stability=dev --remove-vcs
```
