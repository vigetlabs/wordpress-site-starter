<?php
/**
 * Form Element Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use VigetFormBlocks\Admin\EmailTemplate;
use VigetFormBlocks\Admin\Integration;
use VigetFormBlocks\Admin\Submission;
use VigetFormBlocks\Form as FormObject;
use VigetFormBlocks\Utilities\Blocks;
use Exception;
use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Form Elements
 */
class Form {

	/**
	 * Block data.
	 *
	 * @var array
	 */
	protected array $block;

	/**
	 * The Form ID.
	 *
	 * @var ?string
	 */
	protected ?string $form_id = null;

	/**
	 * The Form Name.
	 *
	 * @var ?string
	 */
	protected ?string $form_name = null;

	/**
	 * The Pattern ID.
	 *
	 * @var ?int
	 */
	protected ?int $pattern_id = null;

	/**
	 * The Form markup from Gutenberg.
	 *
	 * @var string
	 */
	protected string $markup = '';

	/**
	 * The Form context.
	 *
	 * @var array
	 */
	protected array $context = [];

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected array $fields = [];

	/**
	 * Constructor.
	 *
	 * @param array $block Block data.
	 * @param string $markup Form markup.
	 * @param array $context Form context.
	 */
	public function __construct( array $block, string $markup = '', array $context = [] ) {
		$this->block   = $block;
		$this->markup  = $markup;
		$this->context = $context;

		if ( ! empty( $block['blockId'] ) ) {
			$this->form_id = FormObject::prefix_id($block['blockId'] );
		}

		if ( ! empty( $block['pattern_id'] ) ) {
			$this->pattern_id = intval( $block['pattern_id'] );
		}
	}

	/**
	 * Get the block attributes.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = [
			'method' => $this->get_method(),
			'action' => '#' . $this->get_id_attr(),
			'id'     => $this->get_id_attr()
		];

		if ( $this->has_field_type( 'input', 'file' ) ) {
			$attrs['enctype'] = 'multipart/form-data';
		}

		if ( is_admin() ) {
			$attrs['autocomplete'] = 'off';
		}

		return $attrs;
	}

	/**
	 * Get the form.
	 *
	 * @return array
	 */
	public function get_form(): array {
		return $this->block;
	}

	/**
	 * Get the form markup.
	 *
	 * @return string
	 */
	public function get_form_markup(): string {
		return $this->markup;
	}

	/**
	 * Get the form context.
	 *
	 * @return array
	 */
	public function get_form_context(): array {
		return $this->context;
	}

	/**
	 * Get the ID attribute.
	 *
	 * @return string
	 */
	public function get_id_attr(): string {
		if ( ! empty( $this->block['anchor'] ) ) {
			return $this->block['anchor'];
		}

		if ( empty( $this->block['blockId'] ) ) {
			return get_block_id( $this->block, true );
		}

		return FormObject::prefix_id( $this->block['blockId'] );
	}

	/**
	 * Get the actual form ID (unique/constant).
	 *
	 * @return string
	 */
	public function get_id(): string {
		if ( empty( $this->block['blockId'] ) ) {
			return get_block_id( $this->block, true );
		}

		return FormObject::prefix_id( $this->block['blockId'] );
	}

	/**
	 * Get the block ACF ID
	 *
	 * @return string
	 */
	public function get_acf_id(): string {
		return get_block_id( $this->block );
	}

	/**
	 * Get the form name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		if ( $this->form_name ) {
			return $this->form_name;
		}

		if ( $this->get_pattern_id() ) {
			$pattern_title = get_the_title( $this->get_pattern_id() );
			if ( $pattern_title ) {
				return $pattern_title;
			}
		}

		if ( $this->get_form_meta( 'name' ) ) {
			return $this->get_form_meta( 'name' );
		}

		if ( ! in_array( get_post_type(), [ EmailTemplate::POST_TYPE, Integration::POST_TYPE, Submission::POST_TYPE ], true ) ) {
			return get_the_title(); // Use the current post title.
		}

		return __( 'Untitled', 'viget-form-blocks' );
	}

	/**
	 * Get the form name with a unique identifier.
	 *
	 * @return string
	 */
	public function get_unique_name(): string {
		$short_id = substr( $this->get_id(), -5 );
		return $this->get_name() . ' (...' . $short_id . ')';
	}

	/**
	 * Set the form name.
	 *
	 * @param ?string $form_name
	 *
	 * @return void
	 */
	public function set_name( ?string $form_name = null ): void {
		if ( ! $form_name ) {
			$form_name = $this->get_name();
		}

		$this->form_name = $form_name;
	}

	/**
	 * Get the form action.
	 *
	 * @return string
	 */
	public function get_method(): string {
		return $this->get_form_data( 'method' ) ?: 'post';
	}

	/**
	 * Get the fields from the page content.
	 *
	 * @param string $content
	 * @param array  $context
	 *
	 * @return Field[]
	 */
	public function get_fields( string $content = '', array $context = [] ): array {
		$all_fields   = $this->get_all_fields( $content, $context );
		$input_fields = [];

		foreach ( $all_fields as $field ) {
			if ( in_array( $field->get_block_name( true ), FormObject::ALL_INPUT_TYPES  ) ) {
				$input_fields[ $field->get_id() ] = $field;
			}
		}

		return $input_fields;
	}

	/**
	 * Get all the fields from the page content.
	 *
	 * @param string $content
	 * @param array  $context
	 *
	 * @return Field[]
	 */
	public function get_all_fields( string $content = '', array $context = [] ): array {
		if ( ! empty( $this->fields ) ) {
			return $this->fields;
		}

		if ( ! $context ) {
			$context = $this->get_form_context() ?: [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		}

		if ( ! $content ) {
			$content = $this->get_form_markup() ?: FormObject::get_form_content( $context, $this->get_form_id() );
		}

		if ( ! $content ) {
			return [];
		}

		$blocks = parse_blocks( $content );

		if ( empty( $blocks ) ) {
			return [];
		}

		$this->fields = $this->prepare_field_blocks( $blocks, $context );

		return $this->fields;
	}

	/**
	 * Update the field context manually.
	 *
	 * @return void
	 */
	public function update_field_context(): void {
		$fields  = $this->get_all_fields();
		$context = [
			'acffb/formId' => $this->get_id(),
		];

		foreach ( $fields as $field ) {
			$field->set_context( $context );
		}
	}

	/**
	 * Check if the form has a field of a specific type.
	 *
	 * @param string $field_type
	 * @param string $sub_type
	 *
	 * @return bool
	 */
	public function has_field_type( string $field_type, string $sub_type = '' ): bool {
		$types = $this->get_fields_by_type( $field_type, $sub_type );

		return count( $types ) > 0;
	}

	/**
	 * Get all fields by type
	 *
	 * @param string $field_type
	 * @param string $sub_type
	 *
	 * @return array
	 */
	public function get_fields_by_type( string $field_type, string $sub_type = '' ): array {
		$fields = $this->get_fields();

		if ( ! str_starts_with( $field_type, 'acf/' ) ) {
			$field_type = 'acf/' . $field_type;
		}

		$types = [];

		foreach ( $fields as $field ) {
			if ( $field->get_block_name( true ) === $field_type ) {
				if ( ! $sub_type || $sub_type === $field->get_type() ) {
					$types[ $field->get_id() ] = $field;
				}
			}
		}

		return $types;
	}

	/**
	 * Extract fields from the blocks array.
	 *
	 * @param array $blocks
	 * @param array $context
	 *
	 * @return array
	 */
	private function prepare_field_blocks( array $blocks, array $context ): array {
		$fields   = [];
		$filtered = Blocks::get_blocks_by_type( $blocks, FormObject::ALL_FIELD_TYPES );

		foreach ( $filtered as $block ) {
			if ( empty( $block['attrs'] ) ) {
				continue;
			}

			$attrs       = $block['attrs'];
			$attrs['id'] = acf_get_block_id( $attrs, $context );
			$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

			$attrs['wp_block'] = $block;

			$block_name = str_replace( '/', '_', $attrs['name'] );
			$prefix     = 'acf_form' === $block_name ? $block_name : 'acf_field';
			$block_id   = $prefix . '_' . $attrs['blockId'];

			$fields[ $block_id ] = Field::factory( $attrs, $attrs );
		}

		return $fields;
	}

	/**
	 * Get the form data.
	 *
	 * @param string $selector Field selector.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get_form_data( string $selector, mixed $default = null ): mixed {
		$value = get_field( $selector, $this->get_acf_id() );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		// Not sure why this is all of sudden necessary.
		if ( isset( $this->block['data'][ $selector ] ) ) {
			return $this->block['data'][ $selector ];
		}

		return $default;
	}

	/**
	 * Get the form metadata.
	 *
	 * @param string $key Meta key.
	 *
	 * @return mixed
	 */
	public function get_form_meta( string $key ): mixed {
		if ( empty( $this->block['metadata'][ $key ] ) ) {
			return null;
		}

		return $this->block['metadata'][ $key ];
	}

	/**
	 * Get the form template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return ( new Template() )
			->add( ( new Block( 'acf/input' ) ) )
			->add( ( new Block( 'acf/submit', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) )
		)->get();
	}

	/**
	 * Get a field by its ID.
	 *
	 * @param string $field_id
	 *
	 * @return ?Field
	 */
	public function get_field_by_id( string $field_id ): ?Field {
		$fields = $this->get_all_fields();

		if ( array_key_exists( $field_id, $fields ) ) {
			return $fields[ $field_id ];
		}

		return null;
	}

	/**
	 * Should we save the form data?
	 *
	 * @return bool
	 */
	public function save_data_enabled(): bool {
		return boolval( $this->get_form_data( 'save_data', true ) );
	}

	/**
	 * Get the Form ID.
	 *
	 * @return ?string
	 */
	public function get_form_id(): ?string {
		return $this->form_id;
	}

	/**
	 * Get the pattern ID if any.
	 *
	 * @return ?int
	 */
	public function get_pattern_id(): ?int {
		return $this->pattern_id;
	}
}
