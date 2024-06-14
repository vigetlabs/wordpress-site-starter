<?php
/**
 * Parts Kit Wrapper Template
 *
 * @global string $parts_kit_title
 * @global string $parts_kit_url
 *
 * @package VigetPartsKit
 */

?>
<html lang="en">
<head>
	<title><?php echo esc_html( $parts_kit_title ); ?></title>
</head>
<body>
<script type="module" src="https://unpkg.com/@viget/parts-kit@^0/lib/parts-kit.js"></script>
<parts-kit config-url="<?php echo esc_url( $parts_kit_url ) ?>"></parts-kit>
</body>
</html>
