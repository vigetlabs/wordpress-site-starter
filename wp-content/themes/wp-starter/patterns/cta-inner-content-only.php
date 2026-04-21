<?php
/**
 * Title: CTA inner (content-only layout)
 * Slug: wp-starter/cta-inner-content-only
 * Categories: cta
 * Description: Core heading, paragraph, and button(s) inside a Group with templateLock contentOnly—mirrors the CTA block’s template.json defaults for curated editing.
 * Inserter: false
 * Viewport width: 1400
 */

?>

<!-- wp:heading {"textAlign":"center","level":2,"placeholder":"Headline Goes Here"} -->
<h2 class="wp-block-heading has-text-align-center"></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Body text goes here..."} -->
<p class="has-text-align-center"></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
	<!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"></a></div>
	<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
