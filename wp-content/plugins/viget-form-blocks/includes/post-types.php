<?php
/**
 * Register Admin Post Types
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Admin\EmailTemplate;
use VigetFormBlocks\Admin\Integration;
use VigetFormBlocks\Admin\Submission;

( new Submission() );
( new EmailTemplate() );
( new Integration() );
