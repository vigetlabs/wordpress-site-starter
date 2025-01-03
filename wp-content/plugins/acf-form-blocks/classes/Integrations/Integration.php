<?php
/**
 * Base Integration
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Integrations;

use ACFFormBlocks\Form;
use ACFFormBlocks\Submission;
use WP_Error;

/**
 * Base Integration Class
 */
class Integration {

	/**
	 * The Integration ID
	 *
	 * @var int
	 */
	protected int $id;

	/**
	 * The Form ID
	 *
	 * @var string
	 */
	protected string $form_id;

	/**
	 * The config array
	 *
	 * @var array
	 */
	protected array $config = [];

	/**
	 * The Submission object
	 *
	 * @var ?Submission
	 */
	protected ?Submission $submission = null;

	/**
	 * The Form object
	 *
	 * @var ?Form
	 */
	protected ?Form $form = null;

	/**
	 * Integration constructor.
	 *
	 * @param int $integration_id
	 */
	public function __construct( int $integration_id ) {
		$this->id = $integration_id;

		$this->init();
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init(): void {
		$this->form_id = get_field( '_acffb_form_id', $this->id );
		$this->form    = Form::find_form( $this->form_id );

		add_action(
			'acffb_handle_validation',
			[ $this, 'validate' ]
		);

		add_action(
			'acffb_process_submission',
			[ $this, 'process' ]
		);
	}

	/**
	 * Run Validation
	 *
	 * @return bool
	 */
	public function validate(): bool {
		return true;
	}

	/**
	 * Process the Integration
	 *
	 * @param Submission $submission
	 *
	 * @return void
	 */
	public function process( Submission $submission ): void {
		$this->submission = $submission;
		$this->form       = $submission->form;
	}

	/**
	 * Perform Test
	 *
	 * @return array|WP_Error
	 */
	public function test(): array|WP_Error {
		return [
			'response' => [
				'code'    => 200,
				'message' => 'OK',
				'form_id' => $this->form_id
			],
		];
	}
}
