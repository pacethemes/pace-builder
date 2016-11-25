<?php

/**
 * Base Class for PaceBuilder Modules
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

/**
 * Base Class for all PaceBuilder Modules
 * This class does all the heavy work like displaying Module Backbone Templates
 * DO NOT override or extend this class
 */
class PTPB_Module extends PTPB_Singleton {

	public $icon;
	public $label;
	public $description;
	public $has_items = false;
	public $tab_panes = false;
	protected $yes_no_option;
	private $slug;

	/**
	 * PTPB_Module Constructor
	 */
	public function __construct() {
		// create a slug using the Module Class Name
		$this->slug          = strtolower( str_replace( 'PTPB_Module_', '', get_class( $this ) ) );
		$this->yes_no_option = array(
			'yes' => __( 'Yes', 'pace-builder' ),
			'no'  => __( 'No', 'pace-builder' )
		);
	}

	/**
	 * Returns the Module Slug
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * Prints Preview and Edit BackboneJS templates for this module
	 * @return void
	 */
	public function js_templates() {
		?>

		<!-- <?php echo $this->slug; ?> module preview template -->
		<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>">
			<div class="pt-pb-<?php echo $this->slug(); ?>-preview">
				{{{ ptPbApp.partial('module-header', { label: data.label, module: data.type }) }}}
				<div class="content-preview" style="display: {{{ ptPbOptions.formFields.items[data.type] ? 'block' : 'none'  }}};">
					<?php $this->preview(); ?>
					<?php
						if ( $this->has_items ) {
							$this->item_preview();
						}
					?>
				</div>
			</div>
		</script>

		<!-- <?php echo $this->slug; ?> module settings template -->
		<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-edit">
			<div class="bbm-modal__topbar">
				<h2><?php echo $this->label . ' ' . __( 'Settings ', 'pace-builder' ) ; ?></h2>
				<div class="pt-pb-top-bar">
					<ul class="pt-topbar-tabs clearfix">
						<li class="tab-active">
							<a href="#pt-form-module-settings"><?php _e( 'General', 'pace-builder' ); ?></a>
						</li>
						<li>
							<a href="#pt-form-design-settings"><?php _e( 'Design Options', 'pace-builder' ); ?></a>
						</li>
						<li>
							<a href="#pt-form-typo-settings"><?php _e( 'Typography Options', 'pace-builder' ); ?></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="bbm-modal__section has-tabs">
				<div class="edit-content-wrap">
					<?php $this->form(); ?>
				</div>
			</div>
			<div class="bbm-modal__bottombar">
				<input type="button" class="button button-primary save-<?php echo $this->slug(); ?> save-module"
				       value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
				<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
			</div>
		</script>

		<?php

		if ( $this->has_items ) {
			$this->js_item_templates();
		}

		/**
		 * Print any extra backbone js templates
		 */
		echo $this->after_js_templates();

	}

	/**
	 * Prints Preview and Edit BackboneJS templates for items in this module
	 * @return void
	 */
	public function js_item_templates() {
		?>
		<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-item">
			<div class="pt-pb-item-content-inner">
				{{{ptPbApp.partial('module-item-header', { label: data.label })}}}
			</div>
		</script>

		<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-item-edit">
			<div class="bbm-modal__topbar">
				<h2><?php echo $this->item_label . ' ' . __( 'Settings ', 'pace-builder' ) ; ?></h2>
			</div>
			<div class="bbm-modal__section">
				<div class="pt-tab-pane-item">
					<?php $this->item_form(); ?>
				</div>
			</div>
			<div class="bbm-modal__bottombar">
				<input type="button" class="button button-primary save-module-item"
				       value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
				<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
			</div>
		</script>
	<?php
	}

	/**
	 * Print any extra backbone js templates
	 * Hook into ptpb_module_after_js_templates and add custom js templates
	 * @return string
	 */
	public function after_js_templates() {
		ob_start();
		do_action( "ptpb_module_{$this->slug}_after_js_templates" );
		return ob_get_clean();
	}

	/**
	 * Prints HTML for Module Preview in the PaceBuilder Stage area
	 * @return void
	 */
	public function preview() {
	}

	/**
	 * Prints HTML for Module Item Preview in the PaceBuilder Stage area
	 * @return void
	 */
	public function item_preview() {
		?>
		<div class="item-content-wrap clearfix"></div>
		<div class="pt-pb-column-foot">
			<a href="#" class="pt-pb-insert-item button"><span> <i
						class="fa fa-plus-circle"></i> <?php echo __( 'Add ', 'pace-builder' ) . $this->item_label; ?></span></a>
		</div>
	<?php
	}

	/**
	 * Prints HTML form which allows users to edit Module Settings
	 * @return void
	 */
	public function form() {
		?>
		<div id="pt-form-module-settings" class="pt-tab-pane">
			<?php if ( method_exists( $this, 'fields' ) ) : ?>
				<?php foreach ( $this->fields() as $name => $args ) : ?>
					<?php ptpb_form_field( $name, $args ); ?>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php ptpb_form_field( 'module_label' ); ?>
		</div>

		<div id="pt-form-design-settings" class="pt-tab-pane">
			<?php ptpb_form_field( 'pt' ); ?>
			<?php ptpb_form_field( 'pb' ); ?>
			<?php ptpb_form_field( 'pl' ); ?>
			<?php ptpb_form_field( 'pr' ); ?>
			<?php ptpb_form_field( 'mb' ); ?>
			<?php ptpb_form_field( 'animation' ); ?>
		</div>

		<div id="pt-form-typo-settings" class="pt-tab-pane">
			<?php ptpb_form_fonts(); ?>
		</div>
	<?php
	}

	/**
	 * Prints HTML form which allows users to edit Module Item Settings
	 * @return void
	 */
	public function item_form() {
		if ( method_exists( $this, 'item_fields' ) ) {
			foreach ( $this->item_fields() as $name => $args ) {
				ptpb_form_field( $name, $args );
			}
		}
		ptpb_form_field( 'module_label' );
	}

}
