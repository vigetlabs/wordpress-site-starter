<?php
/**
 * Block Icons Support
 *
 * Inspired by Nick Diego's Enable Button Icons plugin.
 * @link https://github.com/ndiego/enable-button-icons
 *
 * @package VigetBlocksToolkit
 */

namespace Viget\VigetBlocksToolkit;

use WP_HTML_Tag_Processor;

/**
 * Block Icons Class
 */
class BlockIcons {

	const ICONS_CHECKSUM = 'vgtbt_icons_checksum';

	/**
	 * @var bool
	 */
	private bool $disable_icon_filter = false;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		// Generate cached JSON file.
		$this->generate_json();

		// Enqueue editor assets.
		$this->enqueue_editor_assets();

		// Enqueue block editor assets.
		$this->enqueue_block_editor_assets();

		// Add hooks to render icons on frontend.
		$this->add_render_hooks();
	}

	/**
	 * Get supported blocks for icons.
	 *
	 * @return string[]
	 */
	public function get_supported_blocks(): array {
		return apply_filters(
			'vgtbt_supported_icon_blocks',
			[
				'core/button',
				'core/navigation-link',
				'core/home-link',
				'core/post-excerpt',
				'core/read-more',
				'core/query-pagination-next',
				'core/query-pagination-previous',
			]
		);
	}

	/**
	 * Render icons on the frontend.
	 */
	private function add_render_hooks(): void {
		foreach ( $this->get_supported_blocks() as $block_name ) {
			add_filter(
				"render_block_{$block_name}",
				[ $this, 'render_frontend_icons' ],
				10,
				2
			);
		}
	}

	/**
	 * Get all available icons.
	 *
	 * @param bool $from_file
	 *
	 * @return array
	 */
	public function get_icons( bool $from_file = true ): array {
		if ( $from_file ) {
			$path = $this->get_icons_file_path();
			return file_exists( $path ) ? json_decode( file_get_contents( $path ), true ) : [];
		}

		$icons = [
			'arrow-left'            => [
				'label'       => __( 'Arrow Left', 'viget-blocks-toolkit' ),
				'icon'        => "<svg viewBox='0 0 16 11' xmlns='http://www.w3.org/2000/svg'><polygon points='16 4.7 2.88198758 4.7 6.55900621 1 5.56521739 0 0 5.5 5.56521739 11 6.55900621 10 2.88198758 6.3 16 6.3'></polygon></svg>",
				'defaultLeft' => true,
			],
			'arrow-right'           => [
				'label' => __( 'Arrow Right', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 11' xmlns='http://www.w3.org/2000/svg'><polygon points='0 4.7 13.1180124 4.7 9.44099379 1 10.4347826 0 16 5.5 10.4347826 11 9.44099379 10 13.1180124 6.3 0 6.3'></polygon></svg>",
			],
			'chevron-left'          => [
				'label'       => __( 'Chevron Left', 'viget-blocks-toolkit' ),
				'icon'        => "<svg viewBox='0 0 10 18' xmlns='http://www.w3.org/2000/svg'><polygon points='8.18181818 0 10 1.5 3.03030303 9 10 16.5 8.18181818 18 0 9'></polygon></svg>",
				'defaultLeft' => true,
			],
			'chevron-left-small'    => [
				'label'       => __( 'Chevron Left Small', 'viget-blocks-toolkit' ),
				'icon'        => "<svg viewBox='0 0 7 12' xmlns='http://www.w3.org/2000/svg'><polygon points='5.25057985 0 0 5.99997743 5.25057985 12 7 10.5313252 3.03456266 5.99997743 7 1.46865977'></polygon></svg>",
				'defaultLeft' => true,
			],
			'chevron-right'         => [
				'label' => __( 'Chevron Right', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 10 18' xmlns='http://www.w3.org/2000/svg'><polygon points='1.81818182 0 0 1.5 6.96969697 9 0 16.5 1.81818182 18 10 9'></polygon></svg>",
			],
			'chevron-right-small'   => [
				'label' => __( 'Chevron Right Small', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 7 12' xmlns='http://www.w3.org/2000/svg'><polygon points='1.74942015 0 7 5.99997743 1.74942015 12 0 10.5313252 3.96543734 5.99997743 0 1.46865977'></polygon></svg>",
			],
			'cloud'                 => [
				'label' => __( 'Cloud', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 11' xmlns='http://www.w3.org/2000/svg'><path d='M13.3787444,4.44036697 C13.3787444,1.91743119 11.2663111,0 8.55032538,0 C6.33729999,0 4.42605079,1.41284404 3.92309047,3.33027523 L3.72190634,3.33027523 C1.71006508,3.33027523 0,5.04587156 0,7.16513761 C0,9.28440367 1.71006508,11 3.72190634,11 L12.775192,11 C14.5858492,11 15.9941381,9.48623853 15.9941381,7.66972477 C16.0947301,6.05504587 14.8876254,4.74311927 13.3787444,4.44036697 L13.3787444,4.44036697 Z M12.8757841,9.58715596 L3.82249841,9.58715596 C2.61539365,9.58715596 1.60947301,8.47706422 1.60947301,7.26605505 C1.60947301,6.05504587 2.61539365,4.8440367 3.82249841,4.8440367 L5.13019523,4.8440367 L5.43197142,3.73394495 C5.83433967,2.42201835 7.1420365,1.51376147 8.65091745,1.51376147 C10.4615746,1.51376147 11.9704555,2.82568807 11.9704555,4.44036697 L11.9704555,5.75229358 L13.2781524,5.95412844 C14.0828889,6.05504587 14.6864412,6.86238532 14.6864412,7.7706422 C14.5858492,8.77981651 13.7811127,9.58715596 12.8757841,9.58715596 Z'></path></svg>",
			],
			'cloud-upload'          => [
				'label' => __( 'Cloud Upload', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 11' xmlns='http://www.w3.org/2000/svg'><path d='M13.3,4.4 C13.3,1.9 11.2,-1.24344979e-14 8.5,-1.24344979e-14 C6.3,-1.24344979e-14 4.4,1.4 3.9,3.3 L3.7,3.3 C1.7,3.3 1.95399252e-14,5 1.95399252e-14,7.1 C1.95399252e-14,9.2 1.7,10.9 3.7,10.9 L12.7,10.9 C14.5,10.9 15.9,9.4 15.9,7.6 C16,6 14.8,4.7 13.3,4.4 L13.3,4.4 Z M12.8,9.5 L8.8,9.5 L8.8,7.1 L10,8.3 L11,7.3 L8,4.3 L5,7.3 L6,8.3 L7.2,7.1 L7.2,9.5 L3.7,9.5 C2.5,9.5 1.5,8.4 1.5,7.2 C1.5,6 2.5,4.8 3.7,4.8 L5,4.8 L5.3,3.7 C5.7,2.4 7,1.5 8.5,1.5 C10.3,1.5 11.8,2.8 11.8,4.4 L11.8,5.7 L13.1,5.9 C13.9,6 14.5,6.8 14.5,7.7 C14.5,8.7 13.7,9.5 12.8,9.5 L12.8,9.5 Z'></path></svg>",
			],
			'comment-author-avatar' => [
				'label' => __( 'Comment Author Avatar', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M8,0 C12.418278,0 16,3.581722 16,8 C16,12.418278 12.418278,16 8,16 C3.581722,16 0,12.418278 0,8 C0,3.581722 3.581722,0 8,0 Z M10,10.75 L6,10.75 C5.31,10.75 4.75,11.31 4.75,12 L4.75,13.63 C5.73765837,14.2015236 6.85890165,14.5016718 8,14.5000138 C9.14109835,14.5016718 10.2623416,14.2015236 11.25,13.63 L11.25,12 C11.25,11.31 10.69,10.75 10,10.75 Z M12.5437324,3.35187647 C10.0177861,0.882628059 5.9822139,0.882628059 3.45626762,3.35187647 C0.93032134,5.82112489 0.83872177,9.8556574 3.25,12.437 L3.25,12 C3.25,10.4812169 4.48121694,9.25 5.99999011,9.25 L9.99999011,9.25 C11.5187831,9.25 12.75,10.4812169 12.75,12 L12.75,12.437 C15.1612782,9.8556574 15.0696787,5.82112489 12.5437324,3.35187647 Z M8,4 C9.1045695,4 10,4.8954305 10,6 C10,7.1045695 9.1045695,8 8,8 C6.8954305,8 6,7.1045695 6,6 C6,4.8954305 6.8954305,4 8,4 Z'></path></svg>",
			],
			'download'              => [
				'label' => __( 'Download', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 17' xmlns='http://www.w3.org/2000/svg'><path d='M14,8.3 L13,7.2 L9,11.2 L9,0 L7.5,0 L7.5,11.3 L3,7.2 L2,8.3 L8.2,14.1 L14,8.3 L14,8.3 Z M14.5,12 L14.5,15.5 L1.5,15.5 L1.5,12 L0,12 L0,17 L16,17 L16,12 L14.5,12 Z'></path></svg>",
			],
			'external'              => [
				'label' => __( 'External', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 15 15' xmlns='http://www.w3.org/2000/svg'><path d='M15,0 L8,0 L8,1.5 L12.44,1.5 L6.47,7.47 L7.53,8.53 L13.5,2.56 L13.5,7 L15,7 L15,0 Z M2,1 C0.8954305,1 0,1.8954305 0,3 L0,13 C0,14.1045695 0.8954305,15 2,15 L12,15 C13.1045695,15 14,14.1045695 14,13 L14,10 L12.5,10 L12.5,13 C12.5,13.2761424 12.2761424,13.5 12,13.5 L2,13.5 C1.72385763,13.5 1.5,13.2761424 1.5,13 L1.5,3 C1.5,2.72385763 1.72385763,2.5 2,2.5 L5,2.5 L5,1 L2,1 Z'></path></svg>",
			],
			'external-arrow'        => [
				'label' => __( 'External Arrow', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 12 12' xmlns='http://www.w3.org/2000/svg'><polygon points='12 0 2.15240328 0 2.15240328 2.1101993 8.3985932 2.1101993 0 10.5087925 1.4912075 12 9.8898007 3.6014068 9.8898007 9.84759672 12 9.84759672'></polygon></svg>",
			],
			'help'                  => [
				'label' => __( 'Help', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M8,1.37142857 C5.63183949,1.37142857 3.44356893,2.63482711 2.25948867,4.68571426 C1.07540841,6.73660141 1.07540841,9.26339859 2.25948867,11.3142857 C3.44356893,13.3651729 5.63183949,14.6285714 8,14.6285714 C11.6608589,14.6285714 14.6285713,11.6608589 14.6285713,8 C14.6285713,4.33914113 11.6608589,1.37142857 8,1.37142857 Z M0,8 C0,3.581722 3.581722,0 8,0 C12.418278,0 16,3.581722 16,8 C16,12.418278 12.418278,16 8,16 C3.581722,16 0,12.418278 0,8 Z M8,5.02857143 C8.72564752,5.03138642 9.32350762,5.59898692 9.36397433,6.32351069 C9.40444104,7.04803446 8.87350647,7.67868136 8.15268571,7.76228571 C7.72754286,7.80982857 7.31428571,8.16457143 7.31428571,8.68571429 L7.31428571,9.82857143 L8.68571429,9.82857143 L8.68571429,9.056 C10.0658081,8.69966131 10.9429771,7.34564321 10.704046,5.94045706 C10.465115,4.53527091 9.18971942,3.5472645 7.76941052,3.66709276 C6.34910163,3.78692102 5.25726757,4.97464525 5.25714286,6.4 L6.62857143,6.4 C6.62857143,5.64258091 7.24258091,5.02857143 8,5.02857143 Z M7.31428571,10.7428571 L7.31428571,12.1142857 L8.68571429,12.1142857 L8.68571429,10.7428571 L7.31428571,10.7428571 Z'></path></svg>",
			],
			'info'                  => [
				'label' => __( 'Info', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M8,0 C3.63636364,0 0,3.54545455 0,8 C0,12.3636364 3.54545455,16 8,16 C12.3636364,16 16,12.4545455 16,8 C16,3.63636364 12.3636364,0 8,0 L8,0 Z M8,14.5454545 C4.36363636,14.5454545 1.45454545,11.5454545 1.45454545,8 C1.45454545,4.36363636 4.36363636,1.45454545 8,1.45454545 C11.6363636,1.45454545 14.5454545,4.45454545 14.5454545,8 C14.5454545,11.6363636 11.6363636,14.5454545 8,14.5454545 Z M7.2,12.5454545 L8.8,12.5454545 L8.8,7.09090909 L7.2,7.09090909 L7.2,12.5454545 Z M7.2,5.05454545 L8.8,5.05454545 L8.8,3.45454545 L7.2,3.45454545 L7.2,5.05454545 Z'></path></svg>",
			],
			'lock-outline'          => [
				'label' => __( 'Lock Outline', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 12 17' xmlns='http://www.w3.org/2000/svg'><path d='M11,6.88095238 L9.8,6.88095238 L9.8,3.8452381 C9.8,1.7202381 8.1,0 6,0 C3.9,0 2.2,1.7202381 2.2,3.8452381 L2.2,6.88095238 L1,6.88095238 C0.4,6.88095238 0,7.28571429 0,7.89285714 L0,15.9880952 C0,16.5952381 0.4,17 1,17 L11,17 C11.6,17 12,16.5952381 12,15.9880952 L12,7.89285714 C12,7.28571429 11.6,6.88095238 11,6.88095238 Z M3.8,3.8452381 C3.8,2.63095238 4.8,1.61904762 6,1.61904762 C7.2,1.61904762 8.2,2.63095238 8.2,3.8452381 L8.2,6.88095238 L3.8,6.88095238 L3.8,3.8452381 Z M10.5,15.4821429 L1.5,15.4821429 L1.5,8.39880952 L10.5,8.39880952 L10.5,15.4821429 Z'></path></svg>",
			],
			'login'                 => [
				'label' => __( 'Login', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 14 14' xmlns='http://www.w3.org/2000/svg'><path d='M6.08695652,9.5 L7.20289855,10.6 L10.2463768,7.6 L10.7536232,7.1 L10.1449275,6.5 L7.10144928,3.5 L6.08695652,4.5 L7.8115942,6.2 L0,6.2 L0,7.7 L7.8115942,7.7 L6.08695652,9.5 Z M11.9710145,0 L4.86956522,0 C3.75362319,0 2.84057971,0.9 2.84057971,2 L2.84057971,3.5 L4.36231884,3.5 L4.36231884,2 C4.36231884,1.7 4.56521739,1.5 4.86956522,1.5 L11.9710145,1.5 C12.2753623,1.5 12.4782609,1.7 12.4782609,2 L12.4782609,12 C12.4782609,12.3 12.2753623,12.5 11.9710145,12.5 L4.86956522,12.5 C4.56521739,12.5 4.36231884,12.3 4.36231884,12 L4.36231884,10.5 L2.84057971,10.5 L2.84057971,12 C2.84057971,13.1 3.75362319,14 4.86956522,14 L11.9710145,14 C13.0869565,14 14,13.1 14,12 L14,2 C14,0.9 13.0869565,0 11.9710145,0 Z'></path></svg>",
			],
			'next'                  => [
				'label' => __( 'Next', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 13 12' xmlns='http://www.w3.org/2000/svg'><path d='M1.2,0 L2.22044605e-14,1 L4.5,6 L2.22044605e-14,11 L1.1,12 L6.6,6 L1.2,0 Z M7.2,0 L6.1,1 L10.6,6 L6.1,11 L7.2,12 L12.7,6 L7.2,0 L7.2,0 Z'></path></svg>",
			],
			'previous'              => [
				'label'       => __( 'Previous', 'viget-blocks-toolkit' ),
				'icon'        => "<svg viewBox='0 0 13 12' xmlns='http://www.w3.org/2000/svg'><path d='M11.5,0 L12.7,1 L8.2,6 L12.7,11 L11.6,12 L6.1,6 L11.5,0 Z M5.5,0 L6.6,1 L2.1,6 L6.6,11 L5.5,12 L2.30926389e-14,6 L5.5,0 Z'></path></svg>",
				'defaultLeft' => true,
			],
			'shuffle'               => [
				'label' => __( 'Shuffle', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'><path d='M13.1369889,2.76840729 L11.4221697,1.05557976 L12.4777494,0 L16,3.51527977 L12.4777494,7.03055953 L11.4221697,5.97497977 L13.1379847,4.26215224 L9.96128711,4.26215224 C9.36179747,4.26215224 8.97242796,4.46330989 8.68662476,4.75807556 C8.3789133,5.07574532 8.15086824,5.54677289 7.99452294,6.1353084 C7.86506504,6.62127342 7.77344868,7.15404245 7.69975727,7.63701998 C7.66589905,8.29825107 7.58424099,9.04412772 7.39802079,9.74519201 C7.20582561,10.4631854 6.88716002,11.2060746 6.33447439,11.7766851 C5.76187216,12.3682081 4.98114147,12.7267069 3.98730317,12.7267069 L0,12.7267069 L0,11.232962 L3.98630734,11.232962 C4.58480115,11.232962 4.97516649,11.0318043 5.26096969,10.7370387 C5.56768532,10.4193689 5.79672621,9.94834132 5.95307151,9.35980581 C6.10145018,8.80512852 6.16817078,8.27136366 6.23987054,7.70473642 L6.27870791,7.39602913 C6.32112368,6.84051422 6.41175346,6.28973838 6.54957366,5.7499222 C6.74176884,5.03093297 7.06043443,4.28903965 7.61212423,3.71842908 C8.18572229,3.12690608 8.96645298,2.76840729 9.96029128,2.76840729 L13.1369889,2.76840729 L13.1369889,2.76840729 Z M3.98730317,2.76840729 C4.78396714,2.76840729 5.44121491,2.99744819 5.96701313,3.39378851 C5.68290358,3.82697693 5.45407907,4.29399304 5.28586544,4.78396714 C5.27798753,4.77525513 5.27002047,4.76662415 5.26196552,4.75807556 C4.97616232,4.46330989 4.58579698,4.26215224 3.98730317,4.26215224 L0,4.26215224 L0,2.76840729 L3.98630734,2.76840729 L3.98730317,2.76840729 Z M8.66272484,10.7111471 C8.50239622,11.168233 8.28430945,11.6472272 7.98157715,12.1013257 C8.50637954,12.4966702 9.16462314,12.7267069 9.96128711,12.7267069 L13.1379847,12.7267069 L11.4231655,14.4395344 L12.4787453,15.4951142 L16,11.9798344 L12.4777494,8.46455468 L11.4221697,9.52013444 L13.1379847,11.232962 L9.96128711,11.232962 C9.36179747,11.232962 8.97242796,11.0318043 8.68662476,10.7370387 C8.67858038,10.7284802 8.67061341,10.7198493 8.66272484,10.7111471 L8.66272484,10.7111471 Z'></path></svg>",
			],
			'wordpress'             => [
				'label' => __( 'WordPress', 'viget-blocks-toolkit' ),
				'icon'  => "<svg viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M20,10 C20,4.49 15.51,0 10,0 C4.48,0 0,4.49 0,10 C0,15.52 4.48,20 10,20 C15.51,20 20,15.52 20,10 Z M7.78,15.37 L4.37,6.22 C4.92,6.2 5.54,6.14 5.54,6.14 C6.04,6.08 5.98,5.01 5.48,5.03 C5.48,5.03 4.03,5.14 3.11,5.14 C2.93,5.14 2.74,5.14 2.53,5.13 C4.12,2.69 6.87,1.11 10,1.11 C12.33,1.11 14.45,1.98 16.05,3.45 C15.37,3.34 14.4,3.84 14.4,5.03 C14.4,5.77 14.85,6.39 15.3,7.13 C15.65,7.74 15.85,8.49 15.85,9.59 C15.85,11.08 14.45,14.59 14.45,14.59 L11.42,6.22 C11.96,6.2 12.24,6.05 12.24,6.05 C12.74,6 12.68,4.8 12.18,4.83 C12.18,4.83 10.74,4.95 9.8,4.95 C8.93,4.95 7.47,4.83 7.47,4.83 C6.97,4.8 6.91,6.03 7.41,6.05 L8.33,6.13 L9.59,9.54 L7.78,15.37 Z M17.41,10 C17.65,9.36 18.15,8.13 17.84,5.75 C18.54,7.04 18.89,8.46 18.89,10 C18.89,13.29 17.16,16.24 14.49,17.78 C15.46,15.19 16.43,12.58 17.41,10 Z M6.1,18.09 C3.12,16.65 1.11,13.53 1.11,10 C1.11,8.7 1.34,7.52 1.83,6.41 C3.25,10.3 4.67,14.2 6.1,18.09 Z M10.13,11.46 L12.71,18.44 C11.85,18.73 10.95,18.89 10,18.89 C9.21,18.89 8.43,18.78 7.71,18.56 C8.52,16.18 9.33,13.82 10.13,11.46 L10.13,11.46 Z'></path></svg>",
			],
		];

		if ( $this->disable_icon_filter ) {
			return $icons;
		}

		/**
		 * Filter the available button icons.
		 *
		 * @param array $icons The available button icons.
		 */
		return apply_filters( 'vgtbt_button_icons', $icons );
	}

	/**
	 * Get an icon
	 *
	 * @param string $slug
	 *
	 * @return array
	 */
	public function get_icon( string $slug ): array {
		$icons = $this->get_icons();

		foreach ( $icons as $icon ) {
			if ( $slug === $icon['value'] ) {
				return $icon;
			}
		}

		return [];
	}

	/**
	 * Render icons on frontend.
	 *
	 * @param string $block_content
	 * @param array $block
	 *
	 * @return string
	 */
	public function render_frontend_icons( string $block_content, array $block ): string {
		if ( ! isset( $block['attrs']['icon'] ) ) {
			return $block_content;
		}

		list( $namespace, $block_name ) = explode( '/', $block['blockName'] );

		$icon = $this->get_icon( $block['attrs']['icon'] );

		// Make sure the selected icon exists, otherwise bail.
		if ( empty( $icon ) ) {
			return $block_content;
		}

		$position_left = $block['attrs']['iconPositionLeft'] ?? false;
		$icon_class    = 'has-icon__' . $icon['value'];

		// Append the icon class to the block.
		$p = new WP_HTML_Tag_Processor( $block_content );
		if ( 'core/post-excerpt' === $block['blockName'] ) {
			$query = [
				'tag_name'     => 'p',
				'match_offset' => 2,
				'class_name'   => [ 'wp-block-post-excerpt__more-text' ],
			];
			if ( $p->next_tag( $query ) ) {
				$p->add_class( $icon_class );
			}
		} else {
			if ( $p->next_tag() ) {
				$p->add_class( $icon_class );
			}
		}

		$block_content = $p->get_updated_html();
		$block_content = str_replace( '$', '\$', $block_content );

		$pattern = '/(<a[^>]*>)(.*?)(<\/a>)/i';
		$markup  = sprintf(
			'<span class="wp-block-%s__link-icon has-icon__%s" aria-hidden="true">%s</span>',
			esc_attr( $block_name ),
			esc_attr( $icon['value'] ),
			$icon['icon']
		);

		// Add a wrapper for the Read More link block.
		if ( 'core/read-more' === $block['blockName'] ) {
			$element = '<span class="wp-block-read-more__content">$2</span>';
		} else {
			$element = '$2';
		}

		// Add the SVG icon either to the left of right of the button text.
		return $position_left
			? preg_replace( $pattern, '$1' . $markup . $element . '$3', $block_content )
			: preg_replace( $pattern, '$1' . $element . $markup . '$3', $block_content );
	}

	/**
	 * Enqueue Editor scripts and styles.
	 *
	 * @return void
	 */
	private function enqueue_editor_assets(): void {
		add_action(
			'enqueue_block_editor_assets',
			function () {
				wp_localize_script(
					'vgtbt-editor-scripts',
					'vgtbtIcons',
					[
						'json'            => $this->get_icons(),
						'supportedBlocks' => $this->get_supported_blocks(),
					]
				);

				wp_add_inline_style(
					'vgtbt-editor-styles',
					$this->editor_css()
				);
			},
			20
		);
	}

	/**
	 * Enqueue block styles
	 * (Applies to both frontend and Editor)
	 */
	private function enqueue_block_editor_assets(): void {
		add_action(
			'enqueue_block_editor_assets',
			function (): void {
				foreach ( $this->get_supported_blocks() as $block_name ) {
					wp_enqueue_block_style(
						$block_name,
						[
							'handle' => 'vgtbt-block-styles',
							'src'    => VGTBT_PLUGIN_URL . 'build/style.css',
							'ver'    => VGTBT_VERSION,
							'path'   => VGTBT_PLUGIN_PATH . 'build/style.css',
						]
					);
				}
			},
			20
		);
	}

	/**
	 * Get the CSS for the editor.
	 *
	 * @return string
	 */
	private function editor_css(): string {
		$icons = $this->get_icons();
		$css   = '';
		$selectors = apply_filters(
			'vgtbt_button_icons_editor_css_selectors',
			[
				'.wp-block-button__link',
				'.wp-block-post-excerpt__more-link',
				'.wp-block-navigation-item__content',
			]
		);

		$selectors2 = apply_filters(
			'vgtbt_button_icons_editor_css_selectors2',
			[
				'.wp-block-read-more',
				'.wp-block-query-pagination-next',
				'.wp-block-query-pagination-previous',
			]
		);

		foreach ( $icons as $icon ) {
			$slug    = $icon['value'];
			$content = 'data:image/svg+xml;utf8,' . rawurlencode( $icon['icon'] );

			foreach ( $selectors as $selector ) {
				$css .= ".has-icon__{$slug} $selector::after,";
				$css .= ".has-icon__{$slug} $selector::before,";
			}

			foreach ( $selectors2 as $index => $selector ) {
				$css .= ".has-icon__{$slug}{$selector}::after,";
				$css .= ".has-icon__{$slug}{$selector}::before";
				if ( $index < count( $selectors2 ) - 1 ) {
					$css .= ',';
				} else {
					$css .= '{';
				}
			}

			$css .= 'height: 0.7em;';
			$css .= 'width: 1em;';
			$css .= "mask-image: url( $content );";
			$css .= "-webkit-mask-image: url( $content );";
			$css .= '}' . PHP_EOL;
		}

		// Manually adjust a few of the icons
		$css .= '.button-icon-picker__button.button-icon-picker__icon-chevron-left-small span svg,.button-icon-picker__button.button-icon-picker__icon-chevron-right-small span svg,
	 .button-icon-picker__button.button-icon-picker__icon-external-arrow span svg {max-height: 60%}';
		$css .= '.button-icon-picker__button.button-icon-picker__icon-previous span svg,.button-icon-picker__button.button-icon-picker__icon-next span svg {max-height: 80%}';

		return apply_filters( 'vgtbt_button_icons_editor_css', $css );
	}

	/**
	 * Generate the Icons JSON file.
	 *
	 * @return void
	 */
	private function generate_json(): void {
		add_action(
			'init',
			function () {
				$icons    = $this->get_icons( false );
				$checksum = md5( json_encode( $icons ) );
				$path     = $this->get_icons_file_path( true );

				if ( file_exists( $path ) && $checksum === get_transient( self::ICONS_CHECKSUM ) ) {
					return;
				}

				$json = [];

				foreach ( $icons as $slug => $icon ) {
					if ( ! is_array( $icon ) ) {
						$icon = [ 'icon' => $icon ];
					}

					$default_label = is_numeric( $slug ) ? __( 'Icon', 'viget-blocks-toolkit' ) . ' ' . $slug : ucwords( str_replace( '-', ' ', $slug ) );

					$json[] = [
						'label'       => $icon['label'] ?? $default_label,
						'value'       => $slug,
						'icon'        => $icon['icon'],
						'defaultLeft' => $icon['defaultLeft'] ?? false,
					];
				}

				if ( ! is_dir( dirname( $path ) ) ) {
					wp_mkdir_p( dirname( $path ) );
				}

				if ( file_put_contents( $path, json_encode( $json, JSON_PRETTY_PRINT ) ) ) {
					set_transient( self::ICONS_CHECKSUM, $checksum );
				}
			}
		);
	}

	/**
	 * Get path to the icons JSON file.
	 *
	 * @param bool $write_file
	 *
	 * @return string
	 */
	private function get_icons_file_path( bool $write_file = false ): string {
		$uploads_dir  = wp_get_upload_dir();
		$custom_icons = $uploads_dir['basedir'] . '/viget-blocks-toolkit/icons.json';
		$plugin_icons = VGTBT_PLUGIN_PATH . 'assets/icons.json';

		if ( $write_file ) {
			if ( ! file_exists( $plugin_icons ) ) {
				$this->disable_icon_filter = true;
				return $plugin_icons;
			}

			return $custom_icons;
		}

		$theme_icons = get_stylesheet_directory() . '/viget-blocks-toolkit/icons.json';

		if ( file_exists( $theme_icons ) ) {
			return $theme_icons;
		}

		if ( file_exists( $custom_icons ) ) {
			return $custom_icons;
		}

		return $plugin_icons;
	}
}
