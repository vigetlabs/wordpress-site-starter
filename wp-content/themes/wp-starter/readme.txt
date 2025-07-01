=== WP Starter ===
Contributors: Viget, briandichiara
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Custom block theme built by Viget.

== Changelog ==
= 0.1.1 =
* Adds more variables to Tailwind config to prevent issues with font sizes and padding not scaling properly.
* Removes a few opinionated styles and attributes from components - Spacing/padding should be done in the CSS.
* Fixes a twig syntax error with the Breadcrumbs component.
* Removes unused Footer Stacked Navigation template part.
* Removes Tailwind classes from template files - Should be done in the CSS
* Adjusts block spacing
* Adds font style smoothing
* Removes default padding from Core Cover block
* Adds blank CSS files for Home, Header, and Footer, which are commonly used on every project.
* Adds automated gradient support from Tailwind to WordPress
* Removes/Fixes default spacing from `theme.json`
* Removes/Fixes default Font Sizes from `theme.json`
* Adds new `Medium` font size.
* Fixes issue when inline/highlight text is overridden on dark backgrounds.
* Updates the theme.json schema to `6.8`
* Adds more font family options in Tailwind that exist in `theme.json`
* Enables auto-reload of theme `php` files in Vite.
* Fixes a bug in the Skip Cropping JS file that prevents the "Load More" button from working in the Media Library
* Modifies the `admin.css` and `admin-styles.css` files to use CSS variables to make tweaking admin colors a little easier.
* Adds nginx Media Proxy to load `wp-content/uploads` files missing locally from a remote URL (typically the live site)
* Adds Branding options to remove/change the Viget branding in the WP Admin footer.
* Updates Repo version of ACF to latest (`6.4.2`)

= 0.1.0 =
* Pre-release

== Credits ==

accordion by Moch Rizki Eko Waluyo from <a href="https://thenounproject.com/browse/icons/term/accordion/" target="_blank" title="accordion Icons">Noun Project</a> (CC BY 3.0)
grid by Wren Pollard from <a href="https://thenounproject.com/browse/icons/term/grid/" target="_blank" title="grid Icons">Noun Project</a> (CC BY 3.0)
text by SUBAIDA from <a href="https://thenounproject.com/browse/icons/term/text/" target="_blank" title="text Icons">Noun Project</a> (CC BY 3.0)
text by Smarty from <a href="https://thenounproject.com/browse/icons/term/text/" target="_blank" title="text Icons">Noun Project</a> (CC BY 3.0)

== Recommended Plugins ==

Safe SVG
https://wordpress.org/plugins/safe-svg/
Safely upload SVGs
