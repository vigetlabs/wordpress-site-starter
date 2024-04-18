# WordPress Site Starter
To use this, clone the WordPress Site Starter into the new project repo. Update this to have info about things to note how to be successful using the starter. 

## Requirements
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative)
* For ACF Pro, create an `auth.json` file in `wp-content/mu-plugins/viget-base/` from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/). (Credentials are in 1Password)

## Setup and Running

Download and install WordPress core files
`ddev wp core download`

Start local server
`ddev start`

TODO add more info here once we have the full setup completed. 

*We may want to use our Viget plugin to have this be in the CLI*
1. Open a browser and navigate to [local site](https://wpstarter.ddev.site).
2. Select English as the language.
3. Fill out the site information.
4. Then click "Install WordPress"
5. Once WordPress has been set up login with your user information.


## Block Documentation
