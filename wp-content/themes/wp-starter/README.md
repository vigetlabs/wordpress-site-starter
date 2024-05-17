# WP Site Starter

Custom block theme built by Viget.

## Theme.json
The `theme.json` holds a lot of the core WordPress theme settings. The `theme.json` is build using several js files in `/src/theme-json`, Vite builds all of these files and exports a `theme.json` for both `dev` and `build`. Do not edit directly `theme.json` as it will be over written on build. 

Several of the Tailwind variables are pulled in and Tailwind should be used as the primary way to style elements. If you need to, you can pull in more Tailwind variable for custom styling in `theme.json`.

## Custom Blocks
Blocks are build using ACF and core WordPress blocks. Styles for the blocks are in `src/styles/blocks`.

* Accordion
* Alert Banner
* CTA
* Image Caption
* Logo Grid
* Text Icon Cards
* Text Image
* Video Embed
* *List other custom Blocks*
