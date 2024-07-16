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
			$cache = Cache::get( $form_id );

			if ( $cache ) {
				return $cache;
			}

			if ( is_array( $form ) ) {
				$form = new self( new FormElement( $form, $content, $context ), true );
				Cache::set( $form->get_form_object()->get_id(), $form, true );
				return $form;
			}
		}

		if ( ! $content ) {
			$content = get_the_content();
		}

		if ( ! $content ) {
			return null;
		}

		$blocks = parse_blocks( $content );

		if ( empty( $blocks ) ) {
			return null;
		}

		if ( ! $context ) {
			$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		}

		$form = self::get_form_block( $blocks, $context );

		if ( ! $form ) {
			return null;
		}

		return self::get_instance( $form, $content, $context );
	}

	/**
	 * Get the Form Block Recursively.
	 *
	 * @param array $blocks Blocks.
	 * @param array $context Context.
	 *
	 * @return ?array
	 */
	private static function get_form_block( array $blocks, array $context = [] ): ?array {
		$forms = Blocks::get_blocks_by_type( $blocks, 'acf/form' );

		if ( ! $forms ) {
			return null;
		}

		// Return first form block.
		$block = $forms[0];

		$attrs       = $block['attrs'] ?? [];
		$attrs['id'] = acf_get_block_id( $attrs, $context );
		$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

		return acf_prepare_block( $attrs );
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
	 * Get the form element form.
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
	private function preload_meta(): void {
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
