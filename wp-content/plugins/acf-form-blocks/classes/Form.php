<?php
/**
 * Form Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

use ACFFormBlocks\Admin\EmailTemplate;
use ACFFormBlocks\Admin\Integration;
use ACFFormBlocks\Elements\Form as FormElement;
use ACFFormBlocks\Meta\Confirmation as ConfirmationMeta;
use ACFFormBlocks\Meta\Form as FormMeta;
use ACFFormBlocks\Meta\IP;
use ACFFormBlocks\Meta\Meta;
use ACFFormBlocks\Meta\Notifications;
use ACFFormBlocks\Meta\PostID;
use ACFFormBlocks\Meta\RequestMethod;
use ACFFormBlocks\Meta\URL;
use ACFFormBlocks\Meta\UserAgent;
use ACFFormBlocks\Utilities\Blocks;
use ACFFormBlocks\Utilities\Cache;

/**
 * Class for Forms
 */
class Form {

	/**
	 * All Forms.
	 *
	 * @var Form[]
	 */
	private static array $all_forms = [];

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
		'acf/label',
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
	 * @var Meta[]
	 */
	protected array $registered_meta = [];

	/**
	 * Has the form been initialized.
	 *
	 * @var bool
	 */
	private bool $initialized = false;

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

		if ( $this->initialized ) {
			return;
		}

		$this->init_integrations();

		$this->initialized = true;
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
		$form_id = is_string( $form ) ? self::prefix_id( $form ) : ( ! empty( $form['blockId'] ) ? $form['blockId'] : null );

		if ( $form_id ) {
			$cache = Cache::get( $form_id );

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
			$post_id   = ! empty( $_GET['post'] ) ? intval( $_GET['post'] ) : get_the_ID();
			$post_type = get_post_type( $post_id );
			$context   = [ 'postId' => $post_id, 'postType' => $post_type ];

			if ( EmailTemplate::POST_TYPE === $post_type ) {
				$template_form_id = get_field( '_acffb_form_id', $post_id );

				if ( $template_form_id && $form_id !== $template_form_id ) {
					return self::find_form( $template_form_id );
				}

				if ( $form_id ) {
					$lookup = self::find_form( $form_id );
					if ( $lookup ) {
						return $lookup;
					}
				}

				$context = [];
			}
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

		if ( 1 === count( $forms ) ) {
			return Blocks::prepare_acf_block( $forms[0], $context );
		}

		$form = null;

		foreach ( $forms as $block ) {
			$form = Blocks::prepare_acf_block( $block, $context );

			// Return the first form if no Form ID.
			if ( ! $form_id ) {
				return $form;
			}

			if ( ! empty( $form['blockId'] ) ) {
				$block_form_id = self::prefix_id( $form['blockId'] );

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
			$content  = $the_post?->post_content;
		}

		if ( ! $form_id ) {
			return $content ?? '';
		}

		$content = self::replace_patterns( $content );

		$pattern = '/<!-- wp:acf\/form {.*?"blockId":"[^"]*".*?} -->.*?<!-- \/wp:acf\/form -->/s';
		preg_match_all( $pattern, $content, $form_blocks );

		if ( empty( $form_blocks[0] ) ) {
			return $content;
		}

		foreach ( $form_blocks[0] as $form_block ) {
			$block_id = self::unprefix_id( $form_id );
			if ( preg_match( '/<!-- wp:acf\/form {.*?"blockId":"' . preg_quote( $block_id ) . '"/', $form_block ) ) {
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
		if ( ! empty( self::$all_forms ) ) {
			return self::$all_forms;
		}

		$search = new \WP_Query(
			[
				'post_type'      => 'any',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				's'              => 'acf/form',
			]
		);

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
				$form_block = Blocks::prepare_acf_block( $block, $context );
				$form       = self::get_instance( $form_block, $content, $context );

				if ( ! $form ) {
					continue;
				}

				$form->get_form_object()->set_name();

				self::$all_forms[] = $form;
			}
		}

		wp_reset_postdata();

		return self::$all_forms;
	}

	/**
	 * Find a Form by ID
	 *
	 * @param ?string $form_id
	 *
	 * @return ?Form
	 */
	public static function find_form( ?string $form_id = null ): ?Form {
		if ( is_null( $form_id ) ) {
			return self::get_instance( $form_id );
		}

		foreach ( self::get_all_forms() as $form ) {
			if ( $form_id === $form->get_form_object()->get_id() ) {
				return $form;
			}
		}

		return null;
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

	/**
	 * Init the form integrations.
	 *
	 * @return void
	 */
	public function init_integrations(): void {
		Integration::get_integrations( $this->get_form_object()->get_form_id() );
	}

	/**
	 * Get the form meta.
	 *
	 * @return Meta[]
	 */
	public function get_meta(): array {
		if ( empty( $this->registered_meta ) ) {
			$this->register_meta();
		}

		return $this->registered_meta;
	}

	/**
	 * Load meta from Post meta.
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function load_meta( int $post_id ): void {
		foreach ( $this->get_meta() as $meta_field ) {
			// Set value no matter what when loading so we don't end up with default values.
			$meta_value = get_post_meta( $post_id, $meta_field->get_key(), true );
			$meta_field->set_value( $meta_value );
		}
	}

	/**
	 * Register the Form Meta
	 *
	 * @return void
	 */
	public function register_meta(): void {
		$meta_classes = [
			URL::class,
			IP::class,
			UserAgent::class,
			RequestMethod::class,
			PostID::class,
			FormMeta::class,
			ConfirmationMeta::class,
			Notifications::class,
		];

		$meta_classes = apply_filters( 'acffb_meta_classes', $meta_classes );

		foreach ( $meta_classes as $meta_class ) {
			if ( ! class_exists( $meta_class ) ) {
				return;
			}

			$meta = new $meta_class( $this->form->get_id() );
			$this->registered_meta[ $meta->get_key() ] = $meta;
		}
	}

	/**
	 * Get a meta field
	 *
	 * @param string $key
	 *
	 * @return ?Meta
	 */
	public function get_meta_field( string $key ): ?Meta {
		$meta = $this->get_meta();
		if ( empty( $meta[ $key ] ) ) {
			return null;
		}

		return $meta[ $key ];
	}
}
