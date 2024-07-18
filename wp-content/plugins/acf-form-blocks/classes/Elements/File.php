<?php
/**
 * File Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;

/**
 * Class for File Input Fields
 */
class File extends Input {

	/**
	 * Handle the file upload.
	 *
	 * @param mixed $input
	 * @param ?Form $form
	 *
	 * @return array
	 */
	public function sanitize_input( mixed $input = null, ?Form $form = null ): array {
		$upload = $_FILES[ $this->get_name() ] ?? null;

		if ( ! $upload ) {
			return [];
		}

		$upload_dir  = wp_upload_dir();
		$folder_path = '/form-submissions';
		$upload_path = $upload_dir['basedir'] . $folder_path;

		// Make sure root directory is protected.
		if ( ! is_dir( $upload_path ) ) {
			wp_mkdir_p( $upload_path );
			file_put_contents( $upload_path . '/index.php', "<?php // Silence is golden.\n" );
		}

		$form_dir     = '/' . $form->get_form_object()->get_id();
		$folder_path .= $form_dir;
		$upload_path .= $form_dir;

		// Make sure upload directory is protected.
		if ( ! is_dir( $upload_path ) ) {
			wp_mkdir_p( $upload_path );
			file_put_contents( $upload_path . '/index.php', "<?php // Silence is golden.\n" );
		}

		$upload_name  = wp_unique_filename( $upload_path, $upload['name'] );
		$folder_path .= '/' . $upload_name;
		$upload_path .= '/' . $upload_name;

		if ( ! move_uploaded_file( $upload['tmp_name'], $upload_path ) ) {
			$form->get_validation()->add_error( $this, 'Failed to upload file.' );
			return [];
		}

		return [
			'raw'  => $upload,
			'path' => $upload_path,
			'url'  => $upload_dir['baseurl'] . $folder_path,
		];
	}

	/**
	 * Render the input value.
	 *
	 * @param mixed $value Value to render.
	 * @param Form $form Form object.
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		if ( empty( $value ) ) {
			parent::render_value( $value, $form );
			return;
		}

		printf(
			'<div class="text-input"><a href="%s" target="_blank" rel="noopener">%s</a></div>',
			esc_attr( $value['url'] ),
			esc_html__( 'View File', 'acf-form-blocks' )
		);
	}

	/**
	 * Get the field value.
	 *
	 * @return mixed
	 */
	public function get_value_attr(): mixed {
		$value = parent::get_value_attr();
		if ( ! $value ) {
			return '';
		}

		return $value['raw']['full_path'] ?? '';
	}
}
