<div class="pt-pb-option onoffswitch-wrap">
	<label><?php _e( 'Enabled', 'pace-builder' ); ?></label>

	<div class="pt-pb-option-container">
		<input type="checkbox" class="onoffswitch-checkbox" id="f_e" name="f_e" {{{(data.f_e == true || (data.f_e && data.f_e.length)) ? 'checked' : ''}}} value="true" />
		<label class="onoffswitch-label" for="f_e">
			<span class="onoffswitch-inner"></span>
			<span class="onoffswitch-switch"></span>
		</label>

		<p class="description"><?php _e( "Enable or disable typography settings for this element and it's children. If disabled typography settings will be applied from parent", 'pace-builder' ); ?></p>
	</div>
</div>

<h3><?php _e( 'Desktop', 'pace-builder' ); ?></h3>
<h4><?php _e( 'Title / Heading', 'pace-builder' ); ?></h4>
<?php ptpb_form_field( 'fh_c' ); ?>
<?php ptpb_form_field( 'fh_f' ); ?>
<?php ptpb_form_field( 'fh_v' ); ?>
<?php ptpb_form_field( 'fh_s' ); ?>
<?php ptpb_form_field( 'fh_lh' ); ?>
<?php ptpb_form_field( 'fh_ls' ); ?>
<?php ptpb_form_field( 'fh_ws' ); ?>

<h4><?php _e( 'Text / Content', 'pace-builder' ); ?></h4>
<?php ptpb_form_field( 'ft_c' ); ?>
<?php ptpb_form_field( 'ft_f' ); ?>
<?php ptpb_form_field( 'ft_v' ); ?>
<?php ptpb_form_field( 'ft_s' ); ?>
<?php ptpb_form_field( 'ft_lh' ); ?>
<?php ptpb_form_field( 'ft_ls' ); ?>
<?php ptpb_form_field( 'ft_ws' ); ?>

<h3><?php _e( 'Tablets', 'pace-builder' ); ?></h3>

<div class="pt-pb-option onoffswitch-wrap">
	<label><?php _e( 'Enabled', 'pace-builder' ); ?></label>

	<div class="pt-pb-option-container">
		<input type="checkbox" class="onoffswitch-checkbox" id="f_et" name="f_et" {{{(data.f_et == true || (data.f_et && data.f_et.length)) ? 'checked' : ''}}} value="true" />
		<label class="onoffswitch-label" for="f_et">
			<span class="onoffswitch-inner"></span>
			<span class="onoffswitch-switch"></span>
		</label>
	</div>
</div>

<?php ptpb_form_field( 'f_tss' ); ?>
<?php ptpb_form_field( 'fh_st' ); ?>
<?php ptpb_form_field( 'ft_st' ); ?>

<h3><?php _e( 'Mobiles', 'pace-builder' ); ?></h3>
<div class="pt-pb-option onoffswitch-wrap">
	<label><?php _e( 'Enabled', 'pace-builder' ); ?></label>

	<div class="pt-pb-option-container">
		<input type="checkbox" class="onoffswitch-checkbox" id="f_em" name="f_em" {{{(data.f_em == true || (data.f_em && data.f_em.length)) ? 'checked' : ''}}} value="true" />
		<label class="onoffswitch-label" for="f_em">
			<span class="onoffswitch-inner"></span>
			<span class="onoffswitch-switch"></span>
		</label>
	</div>
</div>
<?php ptpb_form_field( 'f_mss' ); ?>
<?php ptpb_form_field( 'fh_sm' ); ?>
<?php ptpb_form_field( 'ft_sm' ); ?>