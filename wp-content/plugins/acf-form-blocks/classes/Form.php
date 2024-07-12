<?php
/**
 * Form Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

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

	const ALL_INPUT_TYPES = [
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
	];

	const ALL_FIELD_TYPES = [
		'acf/fieldset',
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
		'acf/submit',
	];

	const ALL_BLOCK_TYPES = [
		'acf/form',
		'acf/fieldset',
		'acf/legend',
		'acf/input',
		'acf/select',
		'acf/radios',
		'acf/textarea',
		'acf/submit',
	];

	/**
	 * The Form.
	 *
	 * @var Elements\Form
	 */
	protected Elements\Form $form;

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
	public function __construct( Elements\Form $form, bool $preload_meta = false ) {
		$this->form = $form;

		if ( $preload_meta ) {
			// $block = acf_add_block_meta_values( $block, $post_id );
			$this->preload_meta();
		}

		$this->update_cache();
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
			$this->update_cache();
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
			$this->update_cache();
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
			$this->update_cache();
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
			$this->update_cache();
		}

		return $this->notification;
	}
}
