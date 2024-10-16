# WP Site Starter

This is a custom block theme built by Viget. It is meant to be a starting place then customize and build out your theme. Please update this file to reflect your custom built theme. 

## Frontend Stack
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

### Creating New Blocks
The theme is set up with [plop](https://plopjs.com/) which will auto generate a new block based on the options you input. 
In order build a new block run:

```
ddev npm run plop block
```

It will ask you a few question:
* __What is the block name?__ - *This can be whatever you want.*
* __What is the slug for your theme?__ - *This would your theme slug. If you are unsure of what that is, you can look at `textdomain:` in side of any `block.json` files.*
* __Pick a WordPress icon for the block__ - *Icons are from [WordPress Icons](https://developer.wordpress.org/resource/dashicons/) and you can change the icon if you don't see one you want.*
* __Do you need block styles?__ - *Adds the option for adding a class to the block’s wrapper - [Block Styles](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/)*
* __Do you need block variations?__ - *Adds the option for a block variant - [Block Variations](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/)*

## Customizing Theme 🎨
### Fonts
Fonts are pulled in by [typography.js](/src/theme-json/settings/typography.js). Update the `src` to pull in the font files in `/src/fonts`. For more info on setting up WordPress fonts check out [fullsiteediting](https://fullsiteediting.com/lessons/creating-theme-json/#h-typography).

The default font settings can be found in [_index.js](https://github.com/vigetlabs/wordpress-site-starter/blob/main/wp-content/themes/wp-starter/src/theme-json/styles/_index.js).

### Colors
You have access to all of [Tailwind's colors](https://tailwindcss.com/docs/customizing-colors) but feel free to create your own custom colors in the Tailwind config. 
The theme comes with one accent color. The color name can be set in the top of the Tailwind config and the color are set in config theme. 
Colors are pulled from Tailwind into `/src/theme-json/settings/color.js` to be used in Gutenberg and WordPress. 
Prefix the Gutenberg color slug by adding `dark-` to flag that color as a dark enough for text, buttons to change color to dark mode.  

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

To adjust the spacing you can edit them in `tailwind.config.js` under `spacing > fluid`. The `fluid` spacing is getting pull into `/src/theme-json/settings/spacing.js` and being used as the spacing for both margin and padding in Gutenberg.

### Buttons
WordPress button styles are normally built in the `theme.json` but because there is a limitations on hover/focus for button variants all the buttons style are build in Tailwind.

The Tailwind button plugin is in `plugins-tailwind/buttons.js` and has `default`, `outline`, and both light and dark version. In that file (`buttons.js`) is where you will update and style all of the buttons on the site. Those button styles are getting applied to the HTML in `/src/styles/core-blocks/buttons.css`. 
If you have need to apply the buttons style to the mark up you can add one of these button classes.

| Button Classes       |
|----------------------|
| `.btn-default`       |
| `.btn-default-light` |
| `.btn-outline`       |
| `.btn-outline-light` |
| `.btn-text`          |


### Navigation
The navigation has been set up to be fully accessible and is built using [Alpine](https://alpinejs.dev/) and the styles are set in CSS. You can edit the JS in `/src/components/dropdown.js` and the CSS in `/src/styles/core-blocks/navigation.css` if you need to customize the navigation. 
