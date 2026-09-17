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
* For ACF Pro, create an `auth.json` file in `wp-content/themes/wp-starter/` from the [ACF Website](https://www.advancedcustomfields.com/my-account/view-licenses/).

## Setup and Running
To start the local server and build process, run:

```bash
ddev start
```

This will install the WordPress files, composer packages, npm packages, and start the DDEV server. You will also be ask if you want to import a database or start with a new install. Once the server is started, DDEV will automatically start Vite for local development and sync WordPress Agent Skills.

You are all ready to start working on the site.

Information on developing the theme, styling, and building blocks can be found in the theme [README](wp-content/themes/#UPDATETHIS/README.md).

### Build for production
The deploy script should build the files for production, but if you want to test that out on your local server you can change the DDEV config.yaml `ENVIRONMENT` to `prod` and then `cd` into your custom theme folder and run `ddev npm run build`. This will build the JS and CSS files in the dist folder and out put a manifest file.

## Plugins
* [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)
* *List other Plugins used*

## Commands
The command `ddev start` will automatically start Vite and sync WordPress Agent Skills.

```bash
ddev start
ddev db-sync
ddev agent-skills-sync
ddev rebuild
ddev stop
```
If you do need to run `npm` to troubleshoot something, you need to run it inside of DDEV by running `ddev npm run dev` inside of your custom theme folder.

### Pulling a remote database to your local
A fresh `ddev start` gives you an empty WordPress install. To work with real content, run:

```bash
ddev db-sync
```

That asks which WP Engine environment to pull from, snapshots your current local database, exports the remote one over the SSH Gateway, imports it, rewrites URLs, deactivates any plugins listed in `wpengine.conf`, and flushes caches.

Set the install names in `wpengine.conf` first - `ddev db-sync` tells you which ones are missing.

**One-time prerequisite:** add your public SSH key to your WP Engine account (User Portal, your profile, *SSH Keys*) and make sure *SSH Gateway* is enabled for the environment. Key propagation can take 30-45 minutes. This is your personal key, separate from the Git Push key the deploy workflow uses - a key that works for `git.wpengine.com` does *not* automatically work for the gateway. Check it with:

```bash
ddev db-sync --check
```

Flags:

```bash
ddev db-sync dev --yes        # non-interactive: pick the environment, skip the prompt
ddev db-sync --check          # run the prerequisite/SSH checks only
ddev db-sync --no-snapshot    # skip the pre-import snapshot of your local database
ddev db-sync --keep-dump      # leave the downloaded dump in .db-sync/ (gitignored)
ddev db-sync --no-compress    # transfer uncompressed, if remote gzip misbehaves
WPE_SSH_KEY=~/.ssh/other_key ddev db-sync   # use a non-default SSH key
```

The dump is written to a temp directory and deleted on exit unless you pass `--keep-dump`. Nothing is written to `wpengine.conf` and no credentials are stored - authentication is your SSH key.

### Agent Skills

`ddev start` runs `ddev agent-skills-sync` automatically. You can also run it manually at any time to refresh to the latest skillpack.

Optional environment variables for pinning/customizing source:

- `AGENT_SKILLS_REF` (default: `trunk`)
- `AGENT_SKILLS_REPO_URL` (default: `https://github.com/WordPress/agent-skills.git`)
- `AGENT_SKILLS_TARGETS` (default: `codex,vscode,claude,cursor`)
