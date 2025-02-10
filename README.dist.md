# WordPress Site Starter
This is the README for your new site. Feel free to update any of this info to match your project. 

## Links
-   [Production](#UPDATETHIS)
-   [Staging](#UPDATETHIS)
-   [Development](#UPDATETHIS)

## Requirements
* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative like OrbStack)
* For ACF Pro, create an `auth.json` file in `wp-content/mu-plugins/viget-base/` from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/). (Credentials are in 1Password)

## Setup and Running
To start the local server and build process, run: 

```bash
ddev start
```

This will install the WordPress files, composer packages, npm packages, and start the DDEV server. You will also be ask if you want to import a database or start with a new install. Once the server is started, DDEV will automatically start Vite for local development. 

You are all ready to start working on the site.

Information on developing the theme, styling, and building blocks can be found in the theme [README](wp-content/themes/#UPDATETHIS/README.md).

### Build for production
The deploy script should build the files for production, but if you want to test that out on your local server you can change the DDEV config.yaml `ENVIRONMENT` to `prod` and then `cd` into your custom theme folder and run `ddev npm run build`. This will build the JS and CSS files in the dist folder and out put a manifest file.

## Plugins
* [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)
* *List other Plugins used*

## Commands
The command `ddev start` will automatically start Vite so you should not need to use any `npm` commands. 

```bash
ddev start
ddev rebuild
ddev stop
```
If you do need to run `npm` to troubleshoot something, you need to run it inside of DDEV by running `ddev npm run dev` inside of your custom theme folder.
