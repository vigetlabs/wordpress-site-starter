# Deployment

[← README](../README.md)

Each environment deploys from the branch of the same name. Pushing to the branch (normally by merging a PR) runs `.github/workflows/deploy.yaml`:

| Branch | GitHub environment | WP Engine install |
| --- | --- | --- |
| `dev` | `dev` | `WPE_INSTALL_dev` in `wpengine.conf` |
| `staging` | `staging` | `WPE_INSTALL_staging` |
| `production` | `production` | `WPE_INSTALL_production` |

## What ships

The workflow builds the site on the runner (Composer for the theme and `viget-wp`, Vite for the theme's `dist/`) with `.github/actions/build-site`, the same steps the PR build check runs. It then rsyncs over the SSH Gateway with WP Engine's deploy action in three passes:

1. `wp-content/themes/wp-starter/` with `--delete`, so the install matches the build exactly and old hashed assets are removed.
2. `wp-content/mu-plugins/` without `--delete`. WP Engine keeps its own files there.
3. `wp-content/plugins/` without `--delete`, followed by a page and CDN cache clear.

`.deployignore` lists what never ships (Vite source, Node tooling, `node_modules`). Nothing touches the database or `uploads/`.

Because `plugins/` syncs without `--delete`, removing a plugin from `composer.json` does not remove it from the server. Deactivate and delete it in wp-admin on each environment as well.

## Redeploying

To redeploy a branch without a new commit, run the Deploy workflow from the Actions tab with that branch selected.

## One-time setup

1. **WP Engine:** create a deploy user (not a developer's personal account) with access to every install, add an SSH key for it under *SSH Keys*, and make sure *SSH Gateway* is enabled on each install.
2. **GitHub secret:** `WPE_SSHG_KEY_PRIVATE` holds that user's private key. A repository secret covers all environments.
3. **GitHub environments:** `dev`, `staging` and `production` are created on their first deploy. Add required reviewers to `production` so a merge waits for approval before it goes live. Set the environment variable `SITE_URL` when the public URL differs from `https://<install>.wpenginepowered.com`; it is used for the deployment link and for licensed-plugin activation.
4. **`wpengine.conf`:** fill in the install name for each environment as it is created.
