<?php
/**
 * Email Header Template
 *
 * @package ACFFormBlocks
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php bloginfo( 'name' ); ?></title>
	<?php do_action( 'acffb_email_head' ); ?>
</head>
<body class="acffb-email-body">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="center">
				<table width="600" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>
