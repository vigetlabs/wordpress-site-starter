<?php
/**
 * Block: Alert Banner
 *
 * @global array $block
 *
 * @package WPStarter
 */

use function WPStarter\AlertBanner\dismiss_button;
use function WPStarter\AlertBanner\get_id;

$id    = get_id( $block ); // phpcs:ignore
$inner = [ // phpcs:ignore
	'template' => $block['template'] ?? [],
];
$attrs = [ // phpcs:ignore
	'id' => $id,
];

if ( ! is_admin() ) {
	$attrs['x-data'] = '{ ' . $id . ': $persist(true) }'; // phpcs:ignore
	$attrs['x-show'] = $id; // phpcs:ignore
}
?>
<section <?php block_attrs( $block, '', $attrs ); ?>>
	<div class="acf-block-inner__container">
		<?php inner_blocks( $inner ); ?>
	</div>
	<?php dismiss_button( $id ); ?>
</section>
