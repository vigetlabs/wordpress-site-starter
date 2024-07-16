<?php
/**
 * Register Post Types
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Admin\Submission;

const ACFFB_SUBMISSION_POST_TYPE = 'acffb-submission';

add_action( 'init', function() {
	register_post_type( ACFFB_SUBMISSION_POST_TYPE, array(
		'labels' => array(
			'name' => 'Form Submissions',
			'singular_name' => 'Form Submission',
			'menu_name' => 'Form Submissions',
			'all_items' => 'Form Submissions',
			'edit_item' => 'View Form Submission',
			'view_item' => 'View Form Submission',
			'view_items' => 'View Form Submissions',
			'add_new_item' => '',
			'add_new' => '',
			'new_item' => 'New Form Submission',
			'parent_item_colon' => 'Parent Form Submission:',
			'search_items' => 'Search Form Submissions',
			'not_found' => 'No form submissions found',
			'not_found_in_trash' => 'No form submissions found in Trash',
			'archives' => 'Form Submission Archives',
			'attributes' => 'Form Submission Attributes',
			'insert_into_item' => 'Insert into form submission',
			'uploaded_to_this_item' => 'Uploaded to this form submission',
			'filter_items_list' => 'Filter form submissions list',
			'filter_by_date' => 'Filter form submissions by date',
			'items_list_navigation' => 'Form Submissions list navigation',
			'items_list' => 'Form Submissions list',
			'item_published' => 'Form Submission published.',
			'item_published_privately' => 'Form Submission published privately.',
			'item_reverted_to_draft' => 'Form Submission reverted to draft.',
			'item_scheduled' => 'Form Submission scheduled.',
			'item_updated' => 'Form Submission updated.',
			'item_link' => 'Form Submission Link',
			'item_link_description' => 'A link to a form submission.',
		),
		'description' => 'ACF Form Block Submissions',
		'public' => true,
		'exclude_from_search' => true,
		'show_in_menu' => 'edit.php?post_type=acf-field-group',
		'show_in_nav_menus' => false,
		'show_in_admin_bar' => false,
		'show_in_rest' => true,
		'menu_position' => 50,
		'menu_icon' => 'dashicons-feedback',
		'supports' => array(
			'title',
		),
		'rewrite' => false,
		'delete_with_user' => false,
	) );
} );

$admin_submission = new Submission();
