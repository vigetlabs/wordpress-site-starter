<?php
/**
 * Base Integration
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Integrations;

use ACFFormBlocks\Elements\Form;
use ACFFormBlocks\Submission;

/**
 * Base Integration Class
 */
class Integration {

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
	 * @param array $config
	 */
	public function __construct( array $config = [] ) { }

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init(): void {
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
		$this->form       = $submission->form->get_form_object();
	}
}
