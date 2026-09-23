# WordPress Site Starter

This is a WordPress starter project that includes a basic custom theme, including some essential custom components, and a project setup designed for fast local setup and development.

## Requirements

* [Composer](https://getcomposer.org/) - [Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
* [DDEV](https://ddev.readthedocs.io/en/stable/) - [Installation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
* [Docker](https://docs.docker.com/desktop/install/mac-install/) (or compatible container alternative like OrbStack)
* ACF Pro ships with the starter, so no license key is needed to run it. New projects switch it to Composer instead - see [Licensed plugins](#licensed-plugins).

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
5. **The function prefix** (`my_project_`): This is used to prefix all custom theme functions and defaults to the project slug.
6. **Twig templates** (`Enabled`): Disabling this removes Timber/Twig and leaves the blocks rendering through `render.php`.
7. **Media proxy domain** (blank): A domain to serve `wp-content/uploads` from when a file is missing locally, usually the live site. Leave it blank to skip.
8. **Agency branding** (`Viget`): `Viget`, `Custom` or `None`. `Custom` then asks for the agency name and website, which show in the WP Admin footer.

A summary is printed at the end, and answering no to **Does everything look good?** starts the prompts over.

`ddev start` then runs the install wizard, which asks for a database source (install WordPress, or import a file with a search-replace), the site title, tagline and URL, and the admin username, email and password.

## Automated Setup

Following the series of prompts, the project will be set up with the following:

1. **Composer Dependencies**: All necessary dependencies for WordPress and the theme.
2. **WordPress Core**: The latest version of WordPress will be downloaded.
3. **Local Development Environment**: A DDEV configuration file will be created and the local environment will be started.
4. **Theme Setup**: The theme will be set up with the project name and slug.
5. **Licensed plugins**: Any plugin registered in the theme's `extra.licensed-repositories` that `auth.json` has credentials for is switched from the committed copy to a Composer dependency. See [Licensed plugins](#licensed-plugins).
6. **`package.json` Dependencies**: All necessary script and style build dependencies for the theme will be installed and initialized.
7. **Cleanup**: Any setup files will be removed and the project will be ready for development.
8. **Agent Skills Sync**: WordPress Agent Skills are synced after `ddev start` and can be refreshed at any time.

After the setup is complete, it is recommended to perform your initial commit and push to your project repository.

## Licensed plugins

The starter ships ACF Pro in `wp-content/plugins/`, so it runs with no license key. A new project switches licensed plugins over to Composer instead, which keeps them out of the project repo and updatable.

Licensed plugins are registered in the theme's `composer.json` under `extra.licensed-repositories`, keyed by the repository host:

```json
"extra": {
  "licensed-repositories": {
    "connect.advancedcustomfields.com": {
      "name": "ACF Pro",
      "package": "wpengine/advanced-custom-fields-pro",
      "secret": "ACF_LICENSE_KEY"
    }
  }
}
```

* `name` - what the prompt and log output call the plugin.
* `package` - the Composer package the repository serves. Credentials are only required once the package is, so the starter itself never prompts for a key it doesn't need.
* `secret` - the GitHub Actions secret holding the license key for CI builds.
* `password` - optional template for the `auth.json` password, defaulting to `{site_url}`. `{key}` is also available for repositories that expect the license key in both fields.

Three things read that one list:

1. `ddev composer-auth` prompts for any license key `auth.json` is missing and merges it in, leaving the rest of the file alone. `--check` runs on `ddev start` and reports what's missing; `--force` replaces a key that's already there.
2. `composer create-project` requires each registered package that `auth.json` has credentials for, replacing the committed copy.
3. `.github/workflows/build.yaml` writes `auth.json` from the matching secrets before installing, and removes it afterward.

Adding another licensed plugin means adding its `repositories` entry, its `extra.licensed-repositories` entry, and the matching GitHub Actions secret - nothing else.

## Pushing to your Project Repository

After the project is set up, you can run the following commands to add your remote repository and push your initial commit:

```bash
git init -b main
git add .
git commit -m "Initial Commit"
git remote add origin <git@github.com:YOURGITHUB/your-repo.git>
git push -u origin main
```

Be sure to update the origin with the correct remote repository URL (and remove the `<>` brackets).
Information on developing the theme, styling, and building blocks can be found in the theme [README](wp-content/themes/wp-starter/README.md).

## Syncing a remote database

Projects hosted on WP Engine can pull an environment's database into their local install:

```bash
ddev db-sync
```

Install names live in `wpengine.conf`, which ships with them blank. `ddev db-sync` reports which ones are missing and stops. It authenticates with your own SSH key over WP Engine's SSH Gateway, so nothing sensitive is stored in the repo.

`ddev db-sync --check` runs the prerequisite and SSH checks without touching any database. Full usage is documented in `README.dist.md`, which is the README a new project keeps.

## Agent Skills

WordPress Agent Skills sync on every `ddev start`. Run it manually any time to
refresh them:

```bash
ddev agent-skills-sync
```

Optional environment variables for pinning/customizing source:

- `AGENT_SKILLS_REF` (default: `trunk`)
- `AGENT_SKILLS_REPO_URL` (default: `https://github.com/WordPress/agent-skills.git`)
- `AGENT_SKILLS_TARGETS` (default: `codex,vscode,claude,cursor`)

# Development

The following command will create a directory (`project-name`) and run `composer create-project` using your local repository as the source. Be sure to replace the path with the correct path to your local repository.

Also, in `packages.json`, you can change the name of the branch from `main` to use your current working branch.

**Note:** Be sure to update the paths in `packages.json` as well as the command below to point to the location of your local repository. `~/` will not work, you must use the full path.

```bash
mkdir project-name && cd project-name && composer create-project --repository-url="/root/path/not/relative/path/to/wordpress-site-starter/packages.json" viget/wordpress-site-starter . --stability=dev --remove-vcs --no-install
```

You can quickly remove the project by using:

```bash
ddev stop && ddev delete project-name -O -y && cd ../ && rm -rf project-name
```

## Versioning

`packages.json` and `CHANGELOG.md` are the only two places the starter's version is tracked. Both are removed during `composer create-project`.

**The theme stays at `0.1.0`.** That's the version a generated project starts at, so `style.css`, `readme.txt` and `package.json` in `wp-content/themes/wp-starter` never get bumped with a starter release. The project sets its own version from there.

```bash
composer sync-version              # report the current version and verify
node bin/sync-version.mjs 1.1.0    # set a new version in packages.json
```

`node bin/sync-version.mjs --check` runs on every PR. It fails if the theme has drifted off `0.1.0`, or if `CHANGELOG.md` has no entry for the current version.

## License

MIT for the tooling and scaffolding - see [LICENSE](LICENSE). The theme in `wp-content/themes/wp-starter` and the `viget-wp` mu-plugin are GPLv2 or later, since they ship WordPress-derived code.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
