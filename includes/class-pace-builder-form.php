<?php

/**
 * Handles all HTML for Pace Builder Form elements.
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

class PTPB_Form extends PTPB_Singleton {

	private $form_fields;

	/**
	 * PTPB_Form Constructor
	 */
	public function __construct() {

		$yes_no_option = array(
			'yes' => __( 'Yes', 'pace-builder' ),
			'no'  => __( 'No', 'pace-builder' )
		);

		/**
		 * Holds all common Form Fields
		 * Maring/Padding Fields
		 * Typography Fields
		 */
		$this->form_fields = array(

			/****************************
			 * Page Options
			 ****************************/

			'po_layout'            => array(
				'name'    => 'layout',
				'type'    => 'select',
				'label'   => __( 'Page Layout', 'pace-builder' ),
				'desc'    => __( 'Default - Page Builder content will be displayed inside the Active Theme\'s layout. None - Blank Layout will be used, you have to create the headers, menu and footers as Page Builder Sections', 'pace-builder' ),
				'options' => array(
					'default' => __( 'Default', 'pace-builder' ),
					'none'    => __( 'None', 'pace-builder' ),
				)
			),
			'po_fullwidth'         => array(
				'name'    => 'fullwidth',
				'type'    => 'select',
				'label'   => __( 'Force Full Width', 'pace-builder' ),
				'desc'    => __( 'Do you want to force the Page Builder content to be Full Width of the browser view port ?', 'pace-builder' ),
				'options' => $yes_no_option
			),
			
			/****************************
			 * Generic Fields
			 ****************************/

			'animation'            => array(
				'name'  => 'animation',
				'type'  => 'animation',
				'label' => __( 'CSS3 Animation', 'pace-builder' ),
				'desc'  => __( 'CSS Animation for the Module', 'pace-builder' )
			),
			'module_label'         => array(
				'name'  => 'label',
				'type'  => 'text',
				'label' => __( 'Admin Label', 'pace-builder' ),
				'desc'  => __( 'Admin label for the module, this is the label/title you will see in the Pace Builder stage area, it lets you name your module and helps keep track of them', 'pace-builder' )
			),
			'css_class'            => array(
				'type'  => 'text',
				'label' => __( 'CSS Class(es)', 'pace-builder' ),
				'desc'  => __( 'Additional CSS classes, this will help you set custom styling. You can enter multiple classes by seperating them with spaces', 'pace-builder' ),
			),
			'mb'                   => array(
				'type'  => 'slider',
				'label' => __( 'Margin Bottom', 'pace-builder' ),
				'desc'  => __( 'Margin (Spacing) at the Bottom for this Module (Padding is space inside the module, Margin is space outside the module)', 'pace-builder' ),
				'max'   => 300,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'pt'                   => array(
				'type'  => 'slider',
				'label' => __( 'Padding Top', 'pace-builder' ),
				'desc'  => __( 'Padding (Spacing) at the top', 'pace-builder' ),
				'max'   => 300,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'pb'                   => array(
				'type'  => 'slider',
				'label' => __( 'Padding Bottom', 'pace-builder' ),
				'desc'  => __( 'Padding (Spacing) at the bottom', 'pace-builder' ),
				'max'   => 300,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'pl'                   => array(
				'type'  => 'slider',
				'label' => __( 'Padding Left', 'pace-builder' ),
				'desc'  => __( 'Padding (Spacing) at the left side', 'pace-builder' ),
				'max'   => 300,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'pr'                   => array(
				'type'  => 'slider',
				'label' => __( 'Padding Right', 'pace-builder' ),
				'desc'  => __( 'Padding (Spacing) at the right side', 'pace-builder' ),
				'max'   => 300,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'btw'                  => array(
				'type'  => 'slider',
				'label' => __( 'Border Top Width', 'pace-builder' ),
				'max'   => 10,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'bbw'                  => array(
				'type'  => 'slider',
				'label' => __( 'Border Bottom Width', 'pace-builder' ),
				'max'   => 10,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'blw'                  => array(
				'type'  => 'slider',
				'label' => __( 'Border Left Width', 'pace-builder' ),
				'max'   => 10,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'brw'                  => array(
				'type'  => 'slider',
				'label' => __( 'Border Right Width', 'pace-builder' ),
				'max'   => 10,
				'min'   => 0,
				'step'  => 1,
				'unit'  => 'px'
			),
			'btc'                  => array(
				'type'  => 'color',
				'label' => __( 'Border Top Color', 'pace-builder' )
			),
			'bbc'                  => array(
				'type'  => 'color',
				'label' => __( 'Border Bottom Color', 'pace-builder' )
			),
			'blc'                  => array(
				'type'  => 'color',
				'label' => __( 'Border Left Color', 'pace-builder' ),
				'desc'  => ''
			),
			'brc'                  => array(
				'type'  => 'color',
				'label' => __( 'Border Right Color', 'pace-builder' ),
				'desc'  => ''
			),
			/****************************
			 * Typography
			 ****************************/
			'fh_c'                 => array(
				'type'  => 'color',
				'label' => __( 'Text Color', 'pace-builder' )
			),
			'fh_f'                 => array(
				'type'  => 'font',
				'label' => __( 'Font Family', 'pace-builder' ),
				'desc'  => __( 'If Set to Inherit all Font Options except Text Color will be ignored.', 'pace-builder' )
			),
			'fh_v'                 => array(
				'type'    => 'select',
				'label'   => __( 'Font Variant', 'pace-builder' ),
				'options' => array( 'normal' => 'normal' )
			),
			'fh_s'                 => array(
				'type'  => 'slider',
				'label' => __( 'Font Size (px)', 'pace-builder' ),
				'desc'  => __( 'This is the size for H1, font sizes for H2 to H6 are calculated based on this size.', 'pace-builder' ),
				'max'   => 120,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
			'fh_lh'                => array(
				'type'  => 'slider',
				'label' => __( 'Line Height (em)', 'pace-builder' ),
				'max'   => 5,
				'min'   => 0.8,
				'step'  => 0.05,
				'unit'  => 'em'
			),
			'fh_ls'                => array(
				'type'  => 'slider',
				'label' => __( 'Letter Spacing  (px)', 'pace-builder' ),
				'max'   => 30,
				'min'   => 0,
				'step'  => 0.1,
				'unit'  => 'px'
			),
			'fh_ws'                => array(
				'type'  => 'slider',
				'label' => __( 'Word Spacing (px)', 'pace-builder' ),
				'max'   => 30,
				'min'   => 0,
				'step'  => 0.1,
				'unit'  => 'px'
			),
			'ft_c'                 => array(
				'type'  => 'color',
				'label' => __( 'Text Color', 'pace-builder' )
			),
			'ft_f'                 => array(
				'type'  => 'font',
				'label' => __( 'Font Family', 'pace-builder' ),
				'desc'  => __( 'If Set to Inherit all Font Options except Text Color will be ignored.', 'pace-builder' )
			),
			'ft_v'                 => array(
				'type'    => 'select',
				'label'   => __( 'Font Variant', 'pace-builder' ),
				'options' => array( 'normal' => 'normal' )
			),
			'ft_s'                 => array(
				'type'  => 'slider',
				'label' => __( 'Font Size (px)', 'pace-builder' ),
				'max'   => 60,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
			'ft_lh'                => array(
				'type'  => 'slider',
				'label' => __( 'Line Height (em)', 'pace-builder' ),
				'max'   => 5,
				'min'   => 0.8,
				'step'  => 0.05,
				'unit'  => 'em'
			),
			'ft_ls'                => array(
				'type'  => 'slider',
				'label' => __( 'Letter Spacing  (px)', 'pace-builder' ),
				'max'   => 30,
				'min'   => 0,
				'step'  => 0.1,
				'unit'  => 'px'
			),
			'ft_ws'                => array(
				'type'  => 'slider',
				'label' => __( 'Word Spacing (px)', 'pace-builder' ),
				'max'   => 30,
				'min'   => 0,
				'step'  => 0.1,
				'unit'  => 'px'
			),
			'f_tss'                 => array(
				'type'  => 'range-slider',
				'label' => __( 'Min and Max Screen Size', 'pace-builder' ),
				'default' => '768;991',
				// 'desc'  => __( 'This is the size for H1, font sizes for H2 to H6 are calculated based on this size.', 'pace-builder' ),
				'max'   => 1170,
				'min'   => 620,
				'step'  => 1,
				'unit'  => 'px'
			),
			'fh_st'                 => array(
				'type'  => 'slider',
				'label' => __( 'Title / Heading Font Size (px)', 'pace-builder' ),
				'desc'  => __( 'This is the size for H1, font sizes for H2 to H6 are calculated based on this size.', 'pace-builder' ),
				'max'   => 120,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
			'ft_st'                 => array(
				'type'  => 'slider',
				'label' => __( 'Text / Content Font Size (px)', 'pace-builder' ),
				'max'   => 60,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
			'f_mss'                 => array(
				'type'  => 'slider',
				'label' => __( 'Max Screen Size', 'pace-builder' ),
				'default' => 768,
				'max'   => 768,
				'min'   => 420,
				'step'  => 1,
				'unit'  => 'px'
			),
			'fh_sm'                 => array(
				'type'  => 'slider',
				'label' => __( 'Title / Heading Font Size (px)', 'pace-builder' ),
				'desc'  => __( 'This is the size for H1, font sizes for H2 to H6 are calculated based on this size.', 'pace-builder' ),
				'max'   => 120,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
			'ft_sm'                 => array(
				'type'  => 'slider',
				'label' => __( 'Text / Content Font Size (px)', 'pace-builder' ),
				'max'   => 60,
				'min'   => 8,
				'step'  => 1,
				'unit'  => 'px'
			),
		);

	}

	/**
	 * Print HTML for a PaceBuilder form field
	 * @param $name
	 * @param array $args
	 *
	 * @return string
	 */
	public function field( $name, $args = array() ) {
		$field = ( count( $args ) > 0 ) ? $args : $this->get_form_field( $name );
		if ( ! $field ) {
			return;
		}

		$name  	 = isset( $field['name'] ) ? $field['name'] : $name;
		$type  	 = isset( $field['type'] ) ? $field['type'] : 'text';
		$label 	 = isset( $field['label'] ) ? $field['label'] : '';
		$desc  	 = isset( $field['desc'] ) ? $field['desc'] : '';
		$append  = isset( $field['append'] ) ? $field['append'] : '';
		$after 	 = '';

		$image_lbl = __( 'Select Image', 'pace-builder' );
		$video_lbl = __( 'Select Video', 'pace-builder' );

		$dependency = !empty( $field['dependency'] ) && !empty( $field['condition'] ) ? $field['dependency'] : false;

		if( $dependency ) {
			$condition  = str_replace( '"', "'", $field['condition'] );
			$str_cond   = ' data-dependency="' . $dependency . '" data-condition="' . $condition . '"';
		}


		$field_html      = '';
		$field_container = '<div class="pt-pb-option" %6$s><label for="%1$s">%2$s:</label><div class="pt-pb-option-container">%3$s <p class="description">%4$s</p>%5$s</div></div>';

		switch ( $type ) {
			case 'text':
				$field_html .= '<input name="%1$s" class="regular-text" type="text" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				break;

			case 'hidden':
				$field_html .= '<input name="%1$s" class="regular-text" type="hidden" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				$field_container = '<div>%3$s</div>';
				break;

			case 'textarea':
				$field_html .= '<textarea name="%1$s">{{{ ptPbApp.htmlEncode(data.%1$s) }}}</textarea>';
				break;

			case 'tinymce':
				$field_html .= '<input name="content" class="hidden" value="{{{ ptPbApp.htmlEncode(data.content) }}}" type="hidden" />';
				break;

			case 'autocomplete':
				$action = empty( $field['action'] ) ? false : $field['action'];
				$field_html .= '<input name="%1$s" class="regular-text pt-autocomplete" type="text" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}" data-action="'. $action .'" />';
				$field_container = '<div>%3$s</div>';
				break;

			case 'color':
				$field_html .= '<input name="%1$s" class="pt-pb-color color-picker" type="text" data-alpha="true" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				break;

			case 'icon':
				$field_html .= '<div class="icon-preview"><i class="icon fa-5x {{{data.%1$s}}}"></i></div> <input name="%1$s" type="hidden" class="pt-pb-icon" value="{{{data.%1$s}}}"> <input type="button" class="button pt-pb-icon-select" value="' . __( 'Select Icon', 'pace-builder' ) . '"> <input type="button" class="button pt-pb-icon-delete" value="' . __( 'Remove', 'pace-builder' ) . '">';
				break;

			case 'slider':
				$field_html .= '<input name="%1$s" class="input-slider" type="text" value="{{{data.%1$s}}}" max="' . $field['max'] . '" min="' . $field['min'] . '" step="' . $field['step'] . '" data-postfix="' . $field['unit'] . '" data-hide-min-max="true">';
				break;

			case 'range-slider':
				$field_html .= '<input name="%1$s" class="input-range-slider" type="text" value="{{{data.%1$s}}}" max="' . $field['max'] . '" min="' . $field['min'] . '" step="' . $field['step'] . '" data-postfix="' . $field['unit'] . '" data-hide-min-max="true">';
				break;

			case 'date':
				$field_html .= '<input name="%1$s" class="date-picker" type="text" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				break;

			case 'time':
				$field_html .= '<input name="%1$s" class="time-picker" type="text" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				break;

			case 'datetime':
				$field_html .= '<input name="%1$s" class="datetime-picker" type="text" value="{{{ ptPbApp.htmlEncode(data.%1$s) }}}"/>';
				break;

			case 'image':
				$field_html .= '<input name="%1$s" type="text" class="regular-text pt-pb-upload-field" value="{{{data.%1$s}}}"> <input type="button" class="button pt-pb-upload-button" value="' . $image_lbl . '" data-type="image" data-choose="' . $image_lbl . '" data-update="' . $image_lbl . '"> <input type="button" class="button pt-pb-remove-upload-button" value="' . __( 'Remove', 'pace-builder' ) . '" data-type="image">
				<# if(typeof post_id !== "undefined") { #><input name="post_id" type="hidden" class="regular-text pt-pb-upload-field-id" value={{{data.post_id}}} /> <# } #> ';
				$after = '<div class="screenshot"></div>';
				break;

			case 'video':
				$field_html .= '<input name="%1$s" type="text" class="regular-text pt-pb-upload-field" value="{{{data.%1$s}}}"> <input type="button" class="button pt-pb-upload-button" value="' . $video_lbl . '" data-type="image" data-choose="' . $video_lbl . '" data-update="' . $video_lbl . '"> <input type="button" class="button pt-pb-remove-upload-button" value="' . __( 'Remove', 'pace-builder' ) . '" data-type="image">';
				break;

			case 'chosen':
				$multiple = ! empty( $field['multiple'] );
				$field_html .=	'<select name="%1$s' . ( $multiple ? '[]' : '' ) . '" class="chosen-select" ' . ( $multiple ? 'multiple' : '' ) . '>' . $this->form_select( $name, isset( $field['options'] ) ? $field['options'] : array() ) . '</select>';
				break;

			case 'font':
				$n         = 'data.' . str_replace( '_f', '', $name );
				$bold      = $n . '_b';
				$italic    = $n . '_i';
				$underline = $n . '_u';

				$field_html .= '<select name="%1$s" class="chosen-select font-select"></select>
								<a class="pt-pb-toggle-btn {{{ ' . $bold . ' == 1 ? "active" : "" }}}" href="#">
									<i class="fa fa-bold"></i>
									<input type="hidden" name="' . $bold . '" value="{{{' . $bold . '}}}" />
								</a>
								<a class="pt-pb-toggle-btn {{{ ' . $italic . ' == 1 ? "active" : "" }}}" href="#">
									<i class="fa fa-italic"></i>
									<input type="hidden" name="' . $italic . '" value="{{{' . $italic . '}}}" />
								</a>
								<a class="pt-pb-toggle-btn {{{ ' . $underline . ' == 1 ? "active" : "" }}}" href="#">
									<i class="fa fa-underline"></i>
									<input type="hidden" name="' . $underline . '" value="{{{' . $underline . '}}}" />
								</a>';
				$after = '<div class="icon-grid"></div>';
				break;

			case 'select':
				$field_html .= '<select name="%1$s">' . $this->form_select( $name, isset( $field['options'] ) ? $field['options'] : array() ) . '</select>';
				break;

			case 'menu':
				$field_html .= '<select name="%1$s">' . $this->form_menu_options( $name ) . '</select>';
				break;

			case 'animation':
				$field_html .= "<select class='js-animations' name='animation'>
										{{{ptPbApp.generateOption(data.animation, '', 'none')}}}
									<optgroup label='Attention Seekers'>
										{{{ptPbApp.generateOption(data.animation, 'bounce')}}}
										{{{ptPbApp.generateOption(data.animation, 'flash')}}}
										{{{ptPbApp.generateOption(data.animation, 'pulse')}}}
										{{{ptPbApp.generateOption(data.animation, 'rubberBand')}}}
										{{{ptPbApp.generateOption(data.animation, 'shake')}}}
										{{{ptPbApp.generateOption(data.animation, 'swing')}}}
										{{{ptPbApp.generateOption(data.animation, 'tada')}}}
										{{{ptPbApp.generateOption(data.animation, 'wobble')}}}
									</optgroup>

									<optgroup label='Bouncing Entrances'>
										{{{ptPbApp.generateOption(data.animation, 'bounceIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceInDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceInLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceInRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceInUp')}}}
									</optgroup>

									<optgroup label='Bouncing Exits'>
										{{{ptPbApp.generateOption(data.animation, 'bounceOut')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceOutDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceOutLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceOutRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'bounceOutUp')}}}
									</optgroup>

									<optgroup label='Fading Entrances'>
										{{{ptPbApp.generateOption(data.animation, 'fadeIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInDownBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInLeftBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInRightBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInUp')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeInUpBig')}}}
									</optgroup>

									<optgroup label='Fading Exits'>
										{{{ptPbApp.generateOption(data.animation, 'fadeOut')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutDownBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutLeftBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutRightBig')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutUp')}}}
										{{{ptPbApp.generateOption(data.animation, 'fadeOutUpBig')}}}
									</optgroup>

									<optgroup label='Flippers'>
										{{{ptPbApp.generateOption(data.animation, 'flip')}}}
										{{{ptPbApp.generateOption(data.animation, 'flipInX')}}}
										{{{ptPbApp.generateOption(data.animation, 'flipInY')}}}
										{{{ptPbApp.generateOption(data.animation, 'flipOutX')}}}
										{{{ptPbApp.generateOption(data.animation, 'flipOutY')}}}
									</optgroup>

									<optgroup label='Lightspeed'>
										{{{ptPbApp.generateOption(data.animation, 'lightSpeedIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'lightSpeedOut')}}}
									</optgroup>

									<optgroup label='Rotating Entrances'>
										{{{ptPbApp.generateOption(data.animation, 'rotateIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateInDownLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateInDownRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateInUpLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateInUpRight')}}}
									</optgroup>

									<optgroup label='Rotating Exits'>
										{{{ptPbApp.generateOption(data.animation, 'rotateOut')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateOutDownLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateOutDownRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateOutUpLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'rotateOutUpRight')}}}
									</optgroup>

									<optgroup label='Specials'>
										{{{ptPbApp.generateOption(data.animation, 'hinge')}}}
										{{{ptPbApp.generateOption(data.animation, 'rollIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'rollOut')}}}
									</optgroup>

									<optgroup label='Zoom Entrances'>
										{{{ptPbApp.generateOption(data.animation, 'zoomIn')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomInDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomInLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomInRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomInUp')}}}
									</optgroup>

									<optgroup label='Zoom Exits'>
										{{{ptPbApp.generateOption(data.animation, 'zoomOut')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomOutDown')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomOutLeft')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomOutRight')}}}
										{{{ptPbApp.generateOption(data.animation, 'zoomOutUp')}}}
									</optgroup>
								</select>";
					$after = '<h3 class="animation-preview">' . __( 'Animation Preview', 'pace-builder' ) . '</h3>';
				break;

			default:
				# nothing to do
				break;
		}

		$field_html = sprintf( $field_html, $name );
		$field_html = sprintf( $field_container . $append, $name, $label, $field_html, $desc, $after, $dependency ? $str_cond : '' );

		if ( isset( $args['return'] ) && $args['return'] ) {
			return $field_html;
		}

		echo $field_html;

	}

	/**
	 * Return Form Field array
	 * @param $name
	 *
	 * @return bool|array
	 */
	private function get_form_field( $name ) {
		return isset( $this->form_fields[ $name ] ) ? $this->form_fields[ $name ] : false;
	}

	/**
	 * Return BackboneJS HTML for a select field
	 * @param $select_name
	 * @param $options
	 *
	 * @return string
	 */
	private function form_select( $select_name, $options ) {
		$html = '';
		foreach ( $options as $value => $label ) {
			if( is_array( $label ) && ! empty( $label['label'] ) && ! empty( $label['options'] ) && is_array( $label['options'] ) ) {
				$html   .= "<optgroup label='{$label['label']}'>";
				foreach ( $label['options'] as $val => $lbl ) {
					$html   .= $this->form_select_option( $select_name, $lbl, $val );
				}
				$html   .= "</optgroup>";
			} else {
				$html   .= $this->form_select_option( $select_name, $label, $value );
			}
		}

		return $html;
	}

	/**
	 * Return BackboneJS HTML for a select option item
	 * @param $select_name
	 * @param $label
	 * @param $value
	 *
	 * @return string
	 */
	private function form_select_option( $select_name, $label, $value ){
		return sprintf( '<option value="%1$s" {{{ (%2$s == "%1$s" || (jQuery.isArray(%2$s) && %2$s.indexOf("%1$s") > -1 )) ? "selected" : "" }}}>%3$s</option>', $value, "data.$select_name", $label );
	}

	/**
	 * Return BackboneJS HTML for a menu
	 * @param $select_name
	 *
	 * @return string
	 */
	private function form_menu_options( $select_name ) {
		$menus = wp_get_nav_menus();
		$html  = '';
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				$html .= sprintf( "{{{ ptPbApp.generateOption( %s, '%s', '%s' ) }}}\n", "data.$select_name", esc_attr( $menu->term_id ), $menu->name );
			}
		}

		return $html;
	}

	/**
	 * Return BackboneJS HTML for font options
	 * @param $select_name
	 * @param $fonts
	 *
	 * @return string
	 */
	private function form_select_font( $select_name, $fonts ) {
		$html = '';
		foreach ( $fonts as $value => $name ) {
			$variants = implode( ',', $name['variants'] );
			$html .= sprintf( "<option value='%s' data-variants='%s'>%s</option>\n", esc_attr( $value ), esc_attr( $variants ), $value );
		}

		return $html;
	}

}

// Instantiate PTPB_Form class
PTPB_Form::instance();

// Just a wrapper function for the PTPB_Form->field method
/**
 * @param $name
 * @param array $args
 */
function ptpb_form_field( $name, $args = array() ) {
	PTPB_Form::instance()->field( $name, $args );
}

function ptpb_form_fonts() {
	include( PTPB()->plugin_path() . '/includes/templates/fonts.php' );
}
