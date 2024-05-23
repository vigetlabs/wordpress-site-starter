# WP Site Starter

This is a custom block theme built by Viget. It is meant to be a starting place then customize and build out your theme. Please update this file to reflect your custom built theme. 

## Build Stack
- [Vite](https://vitejs.dev/)
- [AlpineJS](https://alpinejs.dev/)
- [Tailwind](https://tailwindcss.com/)

## Theme.json
The `theme.json` holds a lot of the core WordPress theme settings. The `theme.json` is build using several js files in `/src/theme-json`, Vite builds all of these files and exports a `theme.json` for both `dev` and `build`. Do not edit directly `theme.json` as it will be over written on build. 

Several of the Tailwind variables are pulled in and Tailwind should be used as the primary way to style elements. If you need to, you can pull in more Tailwind variable for custom styling in `theme.json`.

## Custom Blocks 🧱
Blocks are build using ACF and core WordPress blocks. Styles for the blocks are in `src/styles/blocks`.

* Accordion
* Alert Banner
* CTA
* Image Caption
* Logo Grid
* Text Icon Cards
* Text Image
* Video Embed

## Customizing Theme 🎨
### Fonts
Fonts are pulled in by [typography.js](/src/theme-json/settings/typography.js). Update the `src` to pull in the font files in `/src/fonts`. For more info on setting up WordPress fonts check out [fullsiteediting](https://fullsiteediting.com/lessons/creating-theme-json/#h-typography).

### Colors
TBA

### Spacing
To adjust the spacing you can edit them in `tailwind.config.js` under `spacing > fluid`. The `fluid` spacing is getting pull into `/src/theme-json/settings/spacing.js` and being used as the spacing for both margin and padding in Gutenberg.

### Buttons
WordPress buttons are normally all built in the `theme.json` but because there is a limitations on hover/focus for variants all the buttons style are build in Tailwind and CSS.
Tailwind button plugin in `plugins-tailwind/buttons.js` has `contained`, `outline`, and both light and dark version. And will be where you will update and style all of the buttons on the site. Those button styles are getting applied to the HTML in `/src/styles/core-blocks/buttons.css`. 

### Navigation
Has been set up to be fully accessible and is built using AlpineJS and the styles are set in CSS. You can edit the JS in `/src/components/dropdown.js` and the CSS can be edited in `/src/styles/core-blocks/navigation.css`
