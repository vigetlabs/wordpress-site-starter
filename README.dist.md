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

Run `ddev start` to start the site. This will install WordPress files, composer packages, npm packages, and start the DDEV server. Once the server is started DDEV will automatically start Vite for local development. 

If this is the first time this project has been set up: 
1. Select the desired language.
2. Fill out the site information.
3. Then click "Install WordPress"
4. Once WordPress has been set up login with your user information.

If this is an existing project, get an database download from production, staging or another team member. Steps to download a database are different depending on the hosting server. 

Once you have the database file import the database into DDEV using:
```bash
ddev import-db --file=FILENAME.sql.gz
```
Then login using the username/password for the user on that database.

You are all ready to start working on the site.

### Build for production
The deploy script should build the files for production, but if you want to test that out on your local server you can change the DDEV config.yaml `ENVIRONMENT` to `prod` and then run `ddev npm run build`. This will build the JS and CSS files in the dist folder and out put a manifest file.

### Theme.json
The `theme.json` holds a lot of the core WordPress theme settings. The `theme.json` is build using several js files in `/src/theme-json`, Vite builds all of these files and exports a `theme.json` for both `dev` and `build`. Do not edit directly `theme.json` as it will be over written on build. 

Several of the Tailwind variables are pulled in and Tailwind should be used as the primary way to style elements. If you need to, you can pull in more Tailwind variable for custom styling in `theme.json`.

## Plugins
* [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)
* *List other Plugins used*

## Custom Blocks
Builds are build using ACF and core WordPress blocks. Styles for the blocks are in `src/styles/blocks`.

* Accordion
* Alert Banner
* CTA
* Image Caption
* Logo Grid
* Text Icon Cards
* Text Image
* Video Embed
* *List other custom Blocks*

## Commands

```bash
ddev start
ddev rebuild
ddev stop
ddev npm run dev #builds local
ddev npm run build  #builds production
```

In order to run Vite you need to run it inside of DDEV by running `ddev npm run dev` inside of your theme folder.
