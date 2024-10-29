<?php
/**
 * Video Player Block
 *
 * @package WPStarter
 */

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'responsive-embeds' );
	}
);

// Enable the YT JSON API
add_filter(
	'embed_oembed_html',
	function ( string $code ): string {
		return preg_replace(
			'@embed/([^"&]*)@',
			'embed/$1?si=BSrdaVGKXA6hb2GO&enablejsapi=1&html5=1&mute=0',
			$code
		);
	}
);

add_filter(
	'acfbt_button_icons',
	function ( array $icons ): array {
		$block_dir       = dirname( __FILE__ );
		$play_icon_path = $block_dir . '/images/video-play-button.svg';

		return array_merge([
			'video-play-button' => [
				'label'       => __( 'Video Play Button', 'wp-starter' ),
				'icon'        => file_get_contents( $play_icon_path ),
				'defaultLeft' => false,
			],
		], $icons );
	},
	9
);
