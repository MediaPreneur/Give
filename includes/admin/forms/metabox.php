<?php
/**
 * Metabox Functions
 *
 * @package     Give
 * @subpackage  Admin/Downloads
 * @copyright   Copyright (c) 2014, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'cmb2_meta_boxes', 'give_single_forms_cmb2_metaboxes' );

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 *
 * @return array
 */
function give_single_forms_cmb2_metaboxes( array $meta_boxes ) {


	$post_id = give_get_admin_post_id();

	$price            = give_get_form_price( $post_id );
	$variable_pricing = give_has_variable_prices( $post_id );
	$prices           = give_get_variable_prices( $post_id );


	// Start with an underscore to hide fields from custom fields list
	$prefix = '_give_';

	/**
	 * Repeatable Field Groups
	 */
	$meta_boxes['form_field_options'] = apply_filters( 'give_form_field_options', array(
			'id'           => 'form_field_options',
			'title'        => __( 'Create a New Donation Form', 'give' ),
			'object_types' => array( 'give_forms', ),
			'context'      => 'normal',
			'priority'     => 'high', //Show above Content WYSIWYG
			'fields'       => array(
				//Donation Option
				array(
					'name'        => __( 'Donation Option', 'give' ),
					'description' => __( 'Do you want this form to have one set donation price or multiple levels?', 'give' ),
					'id'          => $prefix . 'price_option',
					'type'        => 'radio_inline',
					'default'     => 'set',
					'options'     => array(
						'set'   => __( 'Set Donation', 'give' ),
						'multi' => __( 'Multi-level Donation', 'give' ),
					),
				),
				array(
					'name'        => __( 'Set Amount', 'give' ),
					'description' => __( 'This is the set donation amount for this form.', 'give' ),
					'id'          => $prefix . 'set_price',
					'type'        => 'text_money',
					'attributes'  => array(
						'value' => isset( $price ) ? esc_attr( give_format_amount( $price ) ) : '',
					),
				),
				//Donation levels: Header
				array(
					'id'   => $prefix . 'levels_header',
					'type' => 'levels_repeater_header',
				),
				//Donation Levels: Repeatable CMB2 Group
				array(
					'id'      => $prefix . 'donation_levels',
					'type'    => 'group',
					'options' => array(
						'add_button'    => __( 'Add Level', 'give' ),
						'remove_button' => __( 'Remove Level', 'give' ),
						'sortable'      => true, // beta
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'  => array(
						array(
							'name' => __( 'Amount', 'give' ),
							'id'   => $prefix . 'amount',
							'type' => 'text_money',
						),
						array(
							'name'       => __( 'Text', 'give' ),
							'id'         => $prefix . 'text',
							'type'       => 'text',
							'attributes' => array(
								'placeholder' => 'Donation Level',
								'rows'        => 3,
							),
						),
						array(
							'name'       => __( 'Default', 'give' ),
							'id'         => $prefix . 'default',
							'type'       => 'radio_inline',
							'options'    => array(
								'default' => __( 'Default', 'give' ),
							),
							'attributes' => array(
								'class' => 'donation-level-radio',
							),
						),
					),
				),
				//Display Style
				array(
					'name'        => __( 'Display Style', 'give' ),
					'description' => __( 'Set how the donations levels will display on the form.', 'give' ),
					'id'          => $prefix . 'display_style',
					'type'        => 'radio_inline',
					'default'     => 'buttons',
					'options'     => array(
						'buttons'  => __( 'Buttons', 'give' ),
						'radios'   => __( 'Radios', 'give' ),
						'dropdown' => __( 'Dropdown', 'give' ),
					),
				),
				//Custom Amount
				array(
					'name'        => __( 'Custom Amount', 'give' ),
					'description' => __( 'Do you want the user to be able to input their own donation amount?', 'give' ),
					'id'          => $prefix . 'custom_amount',
					'type'        => 'radio_inline',
					'default'     => 'no',
					'options'     => array(
						'yes' => __( 'Yes', 'give' ),
						'no'  => __( 'No', 'give' ),
					),
				),
			),
		)
	);

	return $meta_boxes;

}

/**
 * Repeatable Levels Custom Field
 */
add_action( 'cmb2_render_levels_repeater_header', 'give_cmb_render_levels_repeater_header', 10 );
function give_cmb_render_levels_repeater_header() {
	?>

	<div class="table-container">
		<div class="table-row">
			<div class="table-cell col-amount"><?php _e( 'Amount', 'give' ); ?></div>
			<div class="table-cell col-text"><?php _e( 'Text', 'give' ); ?></div>
			<div class="table-cell col-default"><?php _e( 'Default', 'give' ); ?></div>
			<div class="table-cell col-sort"><?php _e( 'Sort', 'give' ); ?></div>
		</div>
	</div>

<?php }