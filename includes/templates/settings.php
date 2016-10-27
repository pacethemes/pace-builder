<div class="wrap" id="ptpb-settings">
	<h1> <?php _e( 'Page Builder by PaceThemes', 'pace-builder' ); ?> </h1>

	<?php if( $this->settings_saved ) : ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('Settings Saved', 'pace-builder') ?></strong></p>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo admin_url('options-general.php?page=ptpb_settings') ?>">
		<table class="form-table">
			<tbody>
			<?php foreach( $settings_fields as $field_id => $field ) : ?>
				<tr>
					<th scope="row">
						<label><?php echo esc_html( $field['label'] ) ?></label>
					</th>
					<td>
						<?php $this->form_field( $field_id, $field ); ?>
						<?php
							if( !empty( $field['description'] ) ) {
								?>
								<small class="description" data-keywords="<?php if(!empty($field['keywords'])) echo esc_attr($field['keywords']) ?>">
									<?php
									echo wp_kses( $field['description'], array(
										'a' => array(
											'href' => array(),
											'title' => array(),
											'target' => array(),
										),
										'em' => array(),
										'strong' => array(),
									) );
									?>
								</small>
								<?php
							}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php wp_nonce_field( 'ptpb-settings' ) ?>
		<?php submit_button(); ?>
	</form>
</div>