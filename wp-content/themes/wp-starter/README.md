# WP Site Starter

This is a custom block starter theme built by [Viget](https://www.viget.com/). Update this file as needed for your custom theme.

## Front-End Stack

- [Vite](https://vitejs.dev/)
- [AlpineJS](https://alpinejs.dev/)
- [Tailwind](https://tailwindcss.com/)

## Theme.json

The `theme.json` holds many of the core WordPress theme settings. The `theme.json` is built using several JS files in the `/src/theme-json/` directory. Vite builds all of these files and exports a `theme.json` for both `dev` and `build`. Do not edit directly `theme.json` as it will be overwritten on build.

Several of the Tailwind variables are pulled into `theme.json` and Tailwind should be used as the primary method for styling. If you need to, you can add more Tailwind variables for custom styling in `theme.json`.

The files that create the `theme.json` can be used to apply custom settings for blocks, global theme styles, or define custom templates. Here are a few references:

- [Global Settings & Styles](https://developer.wordpress.org/block-editor/how-to-guides/themes/global-settings-and-styles/)
- [Theme.json reference](https://developer.wordpress.org/block-editor/reference-guides/theme-json-reference/theme-json-living/)
- [Global Styles & theme.json](https://fullsiteediting.com/lessons/global-styles/)

## Custom Blocks 🧱

Blocks are built using ACF and core WordPress blocks. Styles for the blocks are located within the block folders.

### Inner block templates (`template.json`)

Many blocks ship a [`template.json`](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/nested-blocks-inner-blocks/) file next to `block.json`. That file defines the **default inner block template** (for example, which core Heading, Paragraph, and Button blocks appear inside a CTA).

**Important:** that template is applied when the block is first inserted (or when the inner area is still empty). After the editor saves the page, WordPress stores the full nested block tree in the database. **Updating `template.json` in the theme does not retroactively change existing blocks** on already-saved posts. For sitewide structural updates you can use one or more of:

- **Synced patterns** — the theme ships a prototype CTA inner pattern and a versioned `wp_block` (see below and [`docs/block-structure-strategy.md`](docs/block-structure-strategy.md)).
- **Migrations** — use `wp wpstarter blocks migrate` with a custom migration when you need to rewrite saved markup.
- **Server-rendered layout** — move stable structure into `render.php` / Twig so updates deploy with the theme without touching post content.

See [`docs/block-structure-strategy.md`](docs/block-structure-strategy.md) for a per-block recommendation table.

### Synced CTA inner pattern (prototype)

- **Unsynced pattern (inserter):** `patterns/cta-inner-content-only.php` — Group with `templateLock: contentOnly` and the same core blocks as the CTA `template.json`, useful as a curated starting point or reference markup.
- **Synced `wp_block` (Library):** on theme switch or when an editor loads wp-admin, the theme ensures a published synced pattern titled **“WP Starter: CTA inner (synced)”** exists. When you change its markup in [`inc/synced-patterns.php`](inc/synced-patterns.php), bump `WPSTARTER_SYNCED_CTA_INNER_VERSION` so the next admin request or CLI run refreshes the pattern for all references.
- **WP-CLI:** `wp wpstarter patterns sync` creates or updates the synced pattern; add `--force` to rewrite content regardless of the stored version option.

### Block content migrations (WP-CLI)

Scaffold command (list migrations, run a placeholder, dry-run):

```bash
wp wpstarter blocks migrate --dry-run
wp wpstarter blocks migrate --migration=example_placeholder --post-type=post,page
```

Implement real transforms inside [`inc/cli/wpstarter-cli.php`](inc/cli/wpstarter-cli.php) using `parse_blocks()` and `serialize_blocks()` when you change inner structure.

* Accordion
* Alert Banner
* Breadcrumbs
* CTA
* Image w/Caption
* Logo Grid
* Navigation Container
* Page Header
* Text & Icon Cards
* Text & Image
* Video Embed
* Video Player

### Creating New Blocks

The theme includes [plop](https://plopjs.com/) which will auto-generate a new block based on the options you input.
To create a new block, run:

```bash
ddev npm run plop block
```

You will be asked a few questions:

* __What is the block name?__ - *This can be whatever you want.*
* __What is your theme's text domain?__ - *This is the text domain of your theme, which is usually the same as the theme folder name. If you are unsure of what this is, you can look at the `Text Domain:` value in your theme's `style.css` file.*
* __Do you need block styles?__ - *Adds custom class names for the block - [See Block Styles](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/)*
* __Do you need block variations?__ - *Adds custom block variations - [See Block Variations](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/)*

Once your block is created, it is recommended you set the block's icon in `block.json`. You can pick from one of [WordPress's Dashicons](https://developer.wordpress.org/resource/dashicons/), or use your own custom SVG file.

## Customizing Theme 🎨

### Fonts

Fonts are pulled in by [typography.js](src/theme-json/settings/typography.js). Update the `src` to point to the font files in `/src/fonts/` directory. For more info on setting up WordPress fonts, check out [fullsiteediting](https://fullsiteediting.com/lessons/creating-theme-json/#h-typography).

The default font settings can be found in [_index.js](src/theme-json/styles/_index.js).

### Colors

You have access to all of [Tailwind's colors](https://tailwindcss.com/docs/customizing-colors), but feel free to create your own custom colors in the [Tailwind config](src/styles/tailwind/colors.css).

Tailwind colors are automatically added to `theme.json` via [settings/color.js](src/theme-json/settings/color.js) and are available to use in WordPress. Some colors are automatically prefixed with `dark-` to identify automated style adjustments.

### Spacing

The default spacing is fluid, meaning that it is larger on desktops and smaller on mobile screens.

| Class | Min | Max |
|-------|-----|-----|
| `.fluid-xs` | `2px` | `16px` |
| `.fluid-sm` | `20px` | `40px` |
| `.fluid-md` | `32px` | `64px` |
| `.fluid-lg` | `56px` | `112px` |
| `.fluid-xl` | `96px` | `160px` |
| `.fluid-2x` | `144px` | `240px` |

Adjust the spacing as needed from the [spacing.css](src/styles/tailwind/spacing.css) config file.

### Buttons

WordPress button styles are normally built in the `theme.json`, but because there is a limitations on hover/focus customizations on button variants, all the buttons style are built in Tailwind.

The base button styles are found in [styles/core-blocks/buttons.css](src/styles/core-blocks/buttons.css). By default, there are two  built-in button classes:

| Button Classes       |
|----------------------|
| `.btn-default`       |
| `.btn-outline`       |

#### Adding more Buttons Styles

If you need to add more button styles, you can [register](https://developer.wordpress.org/reference/functions/register_block_style/) a new block style on the `core/button` inside of [blocks.php](inc/blocks.php):

```php
register_block_style(
	'core/button',
	[
		'name'  => 'icon-only',
		'label' => __( 'Icon Only', 'theme-slug' ),
	]
);
```

This will attach a class to the block in the pattern of `is-style-[name]`. Once you have the new button style registered you add the Tailwind style in `/src/styles/core-blocks/buttons.css`. It is recommended that you create descriptive button styles and not generic styles like "primary" or "secondary".

#### Custom Button Icons

See Viget Blocks Toolkit for more information.

### Navigation

The navigation has been set up to be fully accessible and is built using [Alpine](https://alpinejs.dev/) with a base set of [styles](src/styles/core-blocks/navigation.css).

## Troubleshooting

### Editor Fonts

The WP Editor requires WOFF2 fonts. TTF/OTF fonts will load on render for templates/pages, but will not display correctly in the Editor view unless they’re in WOFF2 format.

### Editor Styles

The Editor loads the generated Tailwind output, which means you may need to run `ddev npm run build` to generate CSS used by the Editor.

### Disconnected Template Parts

If you edit a template part in the CMS (like the Header), WordPress will use the database version, overriding the template file in the theme. To reset to the code version:

1. Open the template part in the Editor.
2. Click the part name at the top of the screen to bring up the Command Palette.
3. Type ‘Reset’ and select the command. This will remove DB modifications and reset to the code version.
4. Alternatively, you can go to WP Admin > Appearance > Editor, locate the template, click the 3-dot menu on next to the name, then click Reset.

### Trying to find a value in `block.json`

Not all the properties are completely documented. If you’re having trouble, check out the [block schema](https://github.com/WordPress/gutenberg/blob/trunk/schemas/json/block.json) in the main WP repository.
