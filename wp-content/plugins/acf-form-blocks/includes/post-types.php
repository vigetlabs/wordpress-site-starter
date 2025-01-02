<?php
/**
 * Register Admin Post Types
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Admin\EmailTemplate;
use ACFFormBlocks\Admin\Integration;
use ACFFormBlocks\Admin\Submission;

( new Submission() );
( new EmailTemplate() );
( new Integration() );
