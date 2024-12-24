<?php
/**
 * All Fields Template
 *
 * @global AllFieldsBlock $block
 * @global ?Form          $form
 * @global array          $content
 */

use ACFFormBlocks\Admin\Submission;
use ACFFormBlocks\Blocks\AllFieldsBlock;
use ACFFormBlocks\Form;

if ( ! isset( $form ) && isset( $block ) ) {
	$form = $block->get_form();
}
?>
<table class="form-table acffb-submission">
	<tbody>
		<?php foreach ( $content as $key => $data ) :
			$field = $form->get_form_object()->get_field_by_id( $key );
			$label = $field->get_field_label() ?: $data['label'];

			if ( Submission::has_rendered( $field->get_id() ) ) {
				continue;
			}
			?>
			<tr id="<?php echo esc_attr( $key ); ?>">
				<th scope="row" title="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
				<td title="<?php echo esc_attr( $field->get_label() ); ?>"><?php $field->render_value( $data['value'], $form ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
