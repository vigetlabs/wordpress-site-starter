# WordPress Site Starter
This is the README for your new site. Feel free to update any of this info to match your project. 

## Links
-   [Production](#UPDATETHIS)
-   [Staging](#UPDATETHIS)
-   [Development](#UPDATETHIS)

## Requirements
* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative like  OrbStack)
* For ACF Pro, create an `auth.json` file in `wp-content/mu-plugins/viget-base/` from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/). (Credentials are in 1Password)


## Setup and Running
To start the local server and build process. 

```bash
ddev start
```

This will install WordPress files, composer packages, npm packages, and start the DDEV server. Once the server is started, DDEV will automatically start Vite for local development. 

### New Project
If this is the first time setting up this project, once the install is done the site will ask you to: 
1. Select the desired language.
2. Fill out the site information.
3. Then click "Install WordPress"
4. Once WordPress has been set up login with your user information.

### Existing Project
If this is an existing project you can import the local database file then you are prompted. Then provide the path to the file to be imported.

You are all ready to start working on the site.

### Build for production
The deploy script should build the files for production, but if you want to test that out on your local server you can change the DDEV config.yaml `ENVIRONMENT` to `prod` and then `cd` into your custom theme folder and run `ddev npm run build`. This will build the JS and CSS files in the dist folder and out put a manifest file.

## Plugins
* [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)
* *List other Plugins used*

## Commands
```bash
ddev start
ddev rebuild
ddev stop
ddev npm run dev #builds local
ddev npm run build  #builds production
```

In order to run Vite you need to run it inside of DDEV by running `ddev npm run dev` inside of your theme folder.
