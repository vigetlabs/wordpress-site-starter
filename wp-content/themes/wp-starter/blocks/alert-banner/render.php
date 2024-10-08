<?php
/**
 * Block: Alert Banner
 *
 * @global array $block
 *
 * @package WPStarter
 */

$id    = 'alert' . $block['block_id'] ?? $block['id'];
$inner = [
	'template' => $block['template'] ?? [],
];
$attrs = [];

if ( ! is_admin() ) {
	$attrs['x-data'] = '{ ' . $id . ': $persist(true) }';
	$attrs['x-show'] = $id;
}
?>
<section <?php block_attrs( $block, '', $attrs ); ?>>
	<div class="acf-block-inner__container">
		<?php inner_blocks( $inner ); ?>
	</div>
	<?php alert_banner_dismiss_button( $id ); ?>
</section>
