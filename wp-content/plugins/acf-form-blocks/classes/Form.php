<?php
/**
 * Form Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

use ACFFormBlocks\Elements\Form as FormElement;
use ACFFormBlocks\Utilities\Blocks;
use ACFFormBlocks\Utilities\Cache;

/**
 * Class for Forms
 */
class Form {

	/**
	 * Hidden form ID.
	 *
	 * @var string
	 */
	const HIDDEN_FORM_ID = 'acffb_form_id';

	/**
	 * All Input Types.
	 *
	 * @var string[]
	 */
	const ALL_INPUT_TYPES = [
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
		'acf/checkbox',
		'acf/fieldset',
	];

	/**
	 * All Field Types.
	 *
	 * @var string[]
	 */
	const ALL_FIELD_TYPES = [
		'acf/fieldset',
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
		'acf/checkbox',
		'acf/submit',
	];

	/**
	 * All Block Types.
	 *
	 * @var string[]
	 */
	const ALL_BLOCK_TYPES = [
		'acf/form',
		'acf/fieldset',
		'acf/legend',
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
		'acf/checkbox',
		'acf/submit',
	];

	/**
	 * The Form.
	 *
	 * @var FormElement
	 */
	protected FormElement $form;

	/**
	 * @var ?Validation
	 */
	protected ?Validation $validation = null;

	/**
	 * @var ?Submission
	 */
	protected ?Submission $submission = null;

	/**
	 * @var ?Confirmation
	 */
	protected ?Confirmation $confirmation = null;

	/**
	 * @var ?Notification
	 */
	protected ?Notification $notification = null;

	/**
	 * Constructor.
	 *
	 * @param bool $preload_meta Preload meta.
	 */
	public function __construct( FormElement $form, bool $preload_meta = false ) {
		$this->form = $form;

		// Store the fields in cache.
		$this->form->get_all_fields();

		if ( $preload_meta ) {
			$this->preload_meta();
		}
	}

	/**
	 * Get the Form Instance.
	 *
	 * @param mixed  $form
	 * @param string $content
	 * @param array  $context
	 *
	 * @return ?self
	 */
	public static function get_instance( mixed $form = null, string $content = '', array $context = [] ): ?self {
		$form_id = is_string( $form ) ? $form : ( ! empty( $form['block_id'] ) ? $form['block_id'] : null );

		if ( $form_id ) {
			$form_id = self::prefix_id( $form_id );
			$cache   = Cache::get( $form_id );

			if ( $cache ) {
				return $cache;
			}

			if ( is_array( $form ) ) {
				$content = self::get_form_content( $context, $form_id );
				$form    = new self( new FormElement( $form, $content, $context ), true );
				Cache::set( $form->get_form_object()->get_id(), $form, true );
				return $form;
			}
		}

		if ( ! $context ) {
			$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		}

		if ( ! $content ) {
			$content = Form::get_form_content( $context );
		}

		if ( ! $content ) {
			return null;
		}

		$blocks = parse_blocks( $content );

		if ( empty( $blocks ) ) {
			return null;
		}

		$form = self::get_form_block( $blocks, $context, $form_id );

		if ( ! $form ) {
			return null;
		}

		return self::get_instance( $form, $content, $context );
	}

	/**
	 * Get the Form Block Recursively.
	 *
	 * @param array   $blocks Blocks.
	 * @param array   $context Context.
	 * @param ?string $form_id The targeted form ID.
	 *
	 * @return ?array
	 */
	private static function get_form_block( array $blocks, array $context = [], ?string $form_id = null ): ?array {
		$forms = Blocks::get_blocks_by_type( $blocks, 'acf/form' );

		if ( ! $forms ) {
			return null;
		}

		$form = null;

		foreach ( $forms as $block ) {
			$attrs       = $block['attrs'] ?? [];
			$attrs['id'] = acf_get_block_id( $attrs, $context );
			$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

			$form = acf_prepare_block( $attrs );

			// Return the first form if no Form ID.
			if ( ! $form_id ) {
				return $form;
			}

			if ( ! empty( $form['block_id'] ) ) {
				$block_form_id = self::prefix_id( $form['block_id'] );

				if ( $block_form_id === $form_id ) {
					return $form;
				}
			}
		}

		return $form;
	}

	/**
	 * Extract the form from the post content.
	 *
	 * @param array   $context
	 * @param ?string $form_id
	 *
	 * @return string
	 */
	public static function get_form_content( array $context = [], ?string $form_id = null ): string {
		if ( empty( $context['postId'] ) ) {
			$content = get_the_content();
		} else {
			$the_post = get_post( $context['postId'] );
			$content  = $the_post->post_content;
		}

		if ( ! $form_id ) {
			return $content;
		}

		$content = self::replace_patterns( $content );

		$pattern = '/<!-- wp:acf\/form {.*?"block_id":"[^"]*".*?} -->.*?<!-- \/wp:acf\/form -->/s';
		preg_match_all( $pattern, $content, $form_blocks );

		if ( empty( $form_blocks[0] ) ) {
			return $content;
		}

		foreach ( $form_blocks[0] as $form_block ) {
			$block_id = self::unprefix_id( $form_id );
			if ( preg_match( '/<!-- wp:acf\/form {.*?"block_id":"' . preg_quote( $block_id ) . '"/', $form_block ) ) {
				return $form_block;
			}
		}

		return $content;
	}

	/**
	 * Replace the pattern with the pattern content.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function replace_patterns( string $content ): string {
		preg_match_all( '/<!-- wp:block {.*?"ref":(\d+).*?} \/-->/s', $content, $patterns );

		if ( ! $patterns ) {
			return $content;
		}

		foreach ( $patterns[1] as $pattern_id ) {
			$pattern = Blocks::get_pattern( intval( $pattern_id ), false );

			if ( ! $pattern ) {
				continue;
			}

			$content = preg_replace( '/<!-- wp:block {.*?"ref":' . preg_quote( intval( $pattern_id ) ) . '.*?} \/-->/', $pattern, $content );
		}

		return $content;
	}

	/**
	 * Ensure Block ID is prefixed.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public static function prefix_id( string $id ): string {
		if ( ! str_starts_with( $id, 'acf_form_' ) ) {
			$id = 'acf_form_' . $id;
		}

		return $id;
	}

	/**
	 * Remove the prefix from the Block ID.
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public static function unprefix_id( string $id ): string {
		if ( str_starts_with( $id, 'acf_form_' ) ) {
			$id = str_replace( 'acf_form_', '', $id );
		}

		return $id;
	}

	/**
	 * Get all forms.
	 *
	 * @return Form[]
	 */
	public static function get_all_forms(): array {
		$search = new \WP_Query(
			[
				'post_type'      => 'any',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				's'              => 'acf/form',
			]
		);

		$forms = [];

		while ( $search->have_posts() ) {
			$search->the_post();
			$content = get_the_content();

			if ( ! $content ) {
				continue;
			}

			$blocks = parse_blocks( $content );

			if ( empty( $blocks ) ) {
				continue;
			}

			$context     = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
			$form_blocks = Blocks::get_blocks_by_type( $blocks, 'acf/form' );

			foreach ( $form_blocks as $block ) {
				$attrs       = $block['attrs'] ?? [];
				$attrs['id'] = acf_get_block_id( $attrs, $context );
				$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

				$form_block = acf_prepare_block( $attrs );
				$form       = self::get_instance( $form_block, $content, $context );

				if ( ! $form ) {
					continue;
				}

				$forms[] = $form;
			}
		}
		wp_reset_postdata();

		return $forms;
	}

	/**
	 * Get the form object.
	 *
	 * @return Elements\Form
	 */
	public function get_form_object(): Elements\Form {
		return $this->form;
	}

	/**
	 * Get the form block element.
	 *
	 * @return array
	 */
	public function get_form_element(): array {
		return $this->form->get_form();
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
				$meta = $this->get_field_meta();
				$name = ( $hidden ? '_' : '' ) . $name;

				if ( isset( $meta[ $post_id ] ) ) {
					if ( isset( $meta[ $post_id ][ $name ] ) ) {
						return $meta[ $post_id ][ $name ];
					}
					return '__return_null';
				}

				return $null;
			},
			5,
			4
		);
	}

	/**
	 * Get the field meta.
	 *
	 * @return array
	 */
	private function get_field_meta(): array {
		$meta = [
			$this->get_form_object()->get_acf_id() => $this->block['data'] ?? [],
		];

		$fields = $this->form->get_fields();
		foreach ( $fields as $field ) {
			$field_block = $field->get_block();
			if ( empty( $field_block['data'] ) ) {
				continue;
			}

			foreach ( $field_block['data'] as $key => $value ) {
				$meta[ $field->get_name() ][ $key ] = $value;
			}
		}

		return $meta;
	}

	/**
	 * Update the cache.
	 *
	 * @return void
	 */
	public function update_cache(): void {
		Cache::set( $this->get_form_object()->get_id(), $this );
	}

	/**
	 * Get the form confirmation.
	 *
	 * @return Confirmation
	 */
	public function get_confirmation(): Confirmation {
		if ( null === $this->confirmation ) {
			$this->confirmation = new Confirmation( $this );
		}

		return $this->confirmation;
	}

	/**
	 * Get the form submission.
	 *
	 * @return Submission
	 */
	public function get_submission(): Submission {
		if ( null === $this->submission ) {
			$this->submission = new Submission( $this );
		}

		return $this->submission;
	}

	/**
	 * Get the form validation.
	 *
	 * @return Validation
	 */
	public function get_validation(): Validation {
		if ( null === $this->validation ) {
			$this->validation = new Validation( $this );
		}

		return $this->validation;
	}

	/**
	 * Get the form notification.
	 *
	 * @return Notification
	 */
	public function get_notification(): Notification {
		if ( null === $this->notification ) {
			$this->notification = new Notification( $this );
		}

		return $this->notification;
	}
}
