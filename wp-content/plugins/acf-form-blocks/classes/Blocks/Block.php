<?php
/**
 * Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Blocks;

/**
 * Class for Block Actions
 */
class Block {

	/**
	 * The block name(s).
	 *
	 * @var array
	 */
	protected array $block_names;

	/**
	 * The form object.
	 *
	 * @var ?Form
	 */
	protected ?Form $form = null;

	/**
	 * The field object.
	 *
	 * @var ?Field
	 */
	protected ?Field $field = null;

	/**
	 * The ACF Block array.
	 *
	 * @var array
	 */
	protected array $block = [];

	/**
	 * The WordPress block array.
	 *
	 * @var array
	 */
	protected array $wp_block = [];

	/**
	 * Block constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names ) {
		if ( ! is_array( $block_names ) ) {
			$block_names = [ $block_names ];
		}

		$this->block_names = $block_names;

		// Template redirect actions.
		$this->template_redirect();

		// Filter the block during render
		$this->render_block();
	}

	/**
	 * Get the Block array
	 *
	 * @return array
	 */
	public function get_block(): array {
		if ( empty( $this->block ) ) {
			$context     = [ 'postType' => get_post_type(), 'postId' => get_the_ID() ];
			$this->block = Blocks::prepare_acf_block( $this->wp_block, $context );
		}

		return $this->block;
	}

	/**
	 * Get the WP Block array
	 *
	 * @return array
	 */
	public function get_wp_block(): array {
		return $this->wp_block;
	}

	/**
	 * Get the block data.
	 *
	 * @param string $selector Field selector.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	public function get_block_data( string $selector, mixed $default = null ): mixed {
		$this->preload_meta();

		$value = get_field( $selector );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = get_field( $selector, $this->get_acf_id() );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		if ( ! empty( $this->wp_block['attrs']['data'][ $selector ] ) ) {
			return $this->wp_block['attrs']['data'][ $selector ];
		}

		return $default;
	}

	/**
	 * Get the block ACF ID
	 *
	 * @return string
	 */
	public function get_acf_id(): string {
		return get_block_id( $this->get_block(), true );
	}

	/**
	 * Template redirect.
	 *
	 * @return void
	 */
	private function template_redirect(): void {
		add_action(
			'template_redirect',
			function() {
				if ( ! $this->form ) {
					$posted_form = $_REQUEST[ Form::HIDDEN_FORM_ID ] ?? null;
					if ( ! $posted_form ) {
						return;
					}

					$this->form = Form::get_instance( $posted_form );

					// Skip if we don't have a form.
					if ( ! $this->form ) {
						return;
					}
				}

				$this->do_template_redirect();
			}
		);
	}

	/**
	 * Get the Form Object
	 *
	 * @return ?Form
	 */
	public function get_form(): ?Form {
		return $this->form;
	}

	/**
	 * Filter the block during render.
	 *
	 * @return void
	 */
	private function render_block(): void {
		add_filter(
			'render_block',
			function ( string $block_content, array $block ): string {
				if ( ! in_array( $block['blockName'], $this->block_names, true ) ) {
					return $block_content;
				}

				$this->wp_block = $block;

				$this->preload_meta();

				return $this->render( $block_content );
			},
			10,
			2
		);
	}

	/**
	 * Preload the meta data.
	 *
	 * @return void
	 */
	public function preload_meta(): void {
		add_filter(
			'acf/pre_load_metadata',
			function ( $null, $post_id, $name, $hidden ) {
				$name = ( $hidden ? '_' : '' ) . $name;
				return $this->wp_block['data'][ $name ] ?? $null;
			},
			5,
			4
		);
	}

	/**
	 * Do template redirect.
	 *
	 * @return void
	 */
	public function do_template_redirect(): void {
		// Do nothing.
	}

	/**
	 * Apply the render filter.
	 *
	 * @param string $block_content The block content.
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		return $block_content;
	}
}
