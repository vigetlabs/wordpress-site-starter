# WordPress Site Starter
To use this, clone the WordPress Site Starter into the new project repo. Update this to have info about things to note how to be successful using the starter. 

## Requirements
* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative)
* For ACF Pro, create an `auth.json` file in `wp-content/mu-plugins/viget-base/` from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/). (Credentials are in 1Password)

## Setup and Running

Download and install WordPress core files if this is the first time running DDEV
`ddev wp core download`

Start local developement server
`bin/start`

1. Open a browser and navigate to [local site](https://wpstarter.ddev.site).
2. Select English as the language.
3. Fill out the site information.
4. Then click "Install WordPress"
5. Once WordPress has been set up login with your user information.

If you need to build production files. Change the DDEV config.yaml `ENVIRONMENT`
to `prod` and then run `bin/build`. Which will build the JS and CSS files in the dist folder and out put a manifest file. 

## Blocks
Builds are build using ACF and core WordPress blocks. Styles for the blocks are in `src/styles/blocks`.

- Accordion
- Alert Banner
- CTA
- Image Caption
- Logo Grid
- Text Icon Cards
- Text Image
- Video Embed

## Commands

```
ddev npm run dev #builds local files
ddev npm run build  #builds production files
ddev npm run json-theme #builds the theme.json
```

In order to run Vite you need to run it inside of DDEV by running `ddev npm run dev` inside of your theme folder. 
