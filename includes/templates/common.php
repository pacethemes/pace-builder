<script type="text/template" id="pt-pb-tmpl-module-header">
	<# var module = typeof module != 'undefined' ? module : 'module'; #>
		<div class="module-controls {{{ ptPbOptions.formFields.items[data.module] ? '' : 'close'  }}}">
			<div class="edit-module edit-module-{{{ data.module }}}">
				<a href="#" title="<?php _e( 'Edit Module', 'pace-builder' ) ?>" class="edit"><i class="fa fa-bars"></i></a>
				<a href="#" title="<?php _e( 'Clone Module', 'pace-builder' ); ?>" class="clone"><i
						class="fa fa-clone"></i></a>
				<a href="#" title="<?php _e( 'Delete Module', 'pace-builder' ) ?>" class="remove"><i class="fa fa-trash-o"></i></a>
			</div>
			<div class="admin-label">{{{data.label}}}</div>
			<# if( typeof hideToggle === 'undefined' || !hideToggle ) { #>
				<a href="#" class="pt-pb-module-toggle" title="<?php _e( 'Click to toggle', 'pace-builder' ); ?>">
					<div class="handlediv"><i class="fa fa-caret-up"></i><i class="fa fa-caret-down"></i></div>
				</a>
				<# } #>
		</div>
</script>

<script type="text/template" id="pt-pb-tmpl-module-item-header">
	<div class="module-controls close">
		<div class="edit-module-item">
			<a href="#" title="<?php _e( 'Edit', 'pace-builder' ) ?>" class="edit"><i class="fa fa-bars"></i></a>
			<a href="#" title="<?php _e( 'Clone', 'pace-builder' ); ?>" class="clone"><i class="fa fa-clone"></i></a>
			<a href="#" title="<?php _e( 'Delete', 'pace-builder' ) ?>" class="remove"><i class="fa fa-trash-o"></i></a>
		</div>
		<div class="admin-label">{{{data.label}}}</div>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-actions">
	<a href="#" class="button button-large" id="ptpb_manage_layout"><?php _e( 'Layout Manager', 'pace-builder' ); ?></a>
	<a href="#" class="button button-large" id="ptpb_clear_layout"><?php _e( 'Clear Layout', 'pace-builder' ); ?></a>
	<a href="#" class="button button-large" id="ptpb_page_options"><?php _e( 'Page Options', 'pace-builder' ); ?></a>
	<a href="#" class="button button-large" id="ptpb_fullscreen"><?php _e( 'Full Screen Mode', 'pace-builder' ); ?></a>

	<div class="ptpb-page-actions">
		<a href="#" class="button button-large" id="ptpb_preview_page"><?php _e( 'Preview', 'pace-builder' ); ?></a>
		<a href="#" class="button button-large button-primary" id="ptpb_save_page"><?php _e( 'Update', 'pace-builder' ); ?></a>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-insert-layout-dropdown">
	<div class="button-dropdown-wrap">
		<a href="#"
		   class="button button-primary button-dropdown layout-insert"><?php _e( 'Insert', 'pace-builder' ); ?></a>
		<ul class="pt-pb-dropdown hidden">
			<li><a class="pt-pb-dropdown-button" data-value="append"><?php _e( 'Append', 'pace-builder' ); ?></a></li>
			<li><a class="pt-pb-dropdown-button" data-value="prepend"><?php _e( 'Prepend', 'pace-builder' ); ?></a></li>
			<li><a class="pt-pb-dropdown-button" data-value="replace"><?php _e( 'Replace', 'pace-builder' ); ?></a></li>
		</ul>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-layout-manager">
	<div class="bbm-modal__topbar">
		<h2><?php _e( 'Manage Layouts', 'pace-builder' ); ?></h2>
		<div class="pt-pb-top-bar">
			<ul class="pt-topbar-tabs clearfix">
				<li class="tab-active"><a href="#pt-layout-prebuilt"><?php _e( 'Prebuilt', 'pace-builder' ); ?></a> </li>
				<# _.each(ptPbApp.layoutPanes, function(layouts, name) { #>
				<li>
					<a href="#pt-layout-pane-{{{ ptPbApp.paneName(name) }}}">{{{ name }}}</a>
				</li>
				<# }) #>
				<li><a href="#pt-layout-load"><?php _e( 'Load from DB', 'pace-builder' ); ?></a></li>
				<li><a href="#pt-layout-save"><?php _e( 'Save to DB', 'pace-builder' ); ?></a></li>
				<li><a href="#pt-layout-import"><?php _e( 'Import', 'pace-builder' ); ?></a></li>
				<li><a href="#pt-layout-export"><?php _e( 'Export', 'pace-builder' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="bbm-modal__section has-tabs">
		<div class="edit-content-wrap">
			<div class="pt-pb-edit-content">
				<div id="pt-layout-prebuilt" class="pt-tab-pane pt-layout-prebuilt">
					<?php _e( 'Prebuilt', 'pace-builder' ); ?>
				</div>
				<# _.each(ptPbApp.layoutPanes, function(layouts, paneName) { #>
				<div id="pt-layout-pane-{{{ ptPbApp.paneName(paneName) }}}" class="pt-tab-pane pt-layout-prebuilt">
					{{{ptPbApp.partial('layout-items', {layouts: layouts, type: 'theme-prebuilt'})}}}
				</div>
				<# }) #>
				<div id="pt-layout-load" class="pt-tab-pane">
				</div>
				<div id="pt-layout-save" class="pt-tab-pane">
					<div class="pt-save-message"></div>
					<input type="text" name="pt-layout-name" id="pt-layout-name"
					       placeholder="<?php _e( 'Layout Name', 'pace-builder' ) ?>"/>
					<br/>
					<input type="button" class="button button-primary" value="<?php _e( 'Save', 'pace-builder' ); ?>"
					       id="pt-pb-save-layout">
				</div>
				<div id="pt-layout-import" class="pt-tab-pane">
					<div class="import-ui">
						<div class="import-upload-ui hide-if-no-js">
							<div class="drag-upload-area">

								<h3 class="drag-drop-message"><?php _e( 'Drop import file here', 'pace-builder' ); ?></h3>

								<p class="drag-drop-message"><?php _e( 'OR', 'pace-builder' ) ?></p>

								<p class="drag-drop-buttons">
									<input type="button"
									       value="<?php esc_attr_e( 'Select Import File', 'pace-builder' ); ?>"
									       class="file-browse-button button"/>
								</p>

								<div class="progress-bar">
									<div class="progress-percent"></div>
								</div>
							</div>
						</div>

						<div id="import-complete"></div>

					</div>
				</div>
				<div id="pt-layout-export" class="pt-tab-pane">
					<div class="import-export">
						<div class="export-file-ui">
							<iframe id="pt-export-iframe" style="display: none;" name="pt-export-iframe"></iframe>
							<form action="<?php echo admin_url( 'admin-ajax.php?action=ptpb_export_layout' ) ?>"
							      target="pt-export-iframe" class="pt-export" method="post">
								<input type="submit" value="<?php esc_attr_e( 'Download Layout', 'pace-builder' ) ?>"
								       class="button-primary"/>
								<input type="hidden" name="ptpb_export_data" id="ptpb_export_data" value=""/>
								<?php wp_nonce_field( 'ptpb_action', '_ptpb_nonce' ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="bbm-modal__bottombar">
		<input type="button" class="button close-model" value="Close">
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-layout-items">
	<div class="pt-pb-layout-items clearfix">
		<# if( data.type === 'import' ) { #>
			<div class="pt-pb-layout-item" data-layout-type="{{{ data.type }}}">
				{{{ptPbApp.partial('insert-layout-dropdown')}}}
			</div>
			<# } else if( data.type === 'prebuilt' || data.type === 'theme-prebuilt' ) { #>
				<#  _.each( data.layouts, function( layout, name ){ #>
					<div class="pt-pb-layout-item" data-layout="{{{ name }}}" data-layout-type="{{{ data.type }}}">
						<div class="item-wrap">
							<div class="item-thumb">
								<# if(layout.thumb) { #>
								<img src="{{{ layout.thumb }}}"/>
								<# } #>
							</div>
							<h3 class="layout-name">{{{ name }}}</h3>
							<div class="layout-actions">
								{{{ptPbApp.partial('insert-layout-dropdown')}}}
								<# if(layout.preview) { #>
								<a href="{{{ layout.preview }}}" target="_blank"
								   class="button layout-preview"><?php _e( 'Preview', 'pace-builder' ); ?></a>
								<# } #>
							</div>
						</div>
					</div>
					<# }); #>
						<# } else { #>
							<#  _.each( data.layouts, function( layout, name ){ #>
								<div class="pt-pb-layout-item" data-layout="{{{ name }}}"
								     data-layout-type="{{{ data.type }}}">
									<div class="item-wrap">
										<h3 class="layout-name">{{{ name }}}</h3>

										<div class="layout-actions">
											{{{ptPbApp.partial('insert-layout-dropdown')}}}
											<a href="#"
											   class="button layout-delete"><?php _e( 'Delete', 'pace-builder' ); ?></a>
										</div>
									</div>
								</div>
								<# }); #>
						<# } #>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-page-options-edit">
	<div class="bbm-modal__topbar">
		<h2><?php _e( 'Page Options', 'pace-builder' ); ?></h2>
		<div class="pt-pb-top-bar">
			<ul class="pt-topbar-tabs clearfix">
				<li class="tab-active">
					<a href="#pt-form-design-settings"><?php _e( 'Layout Settings', 'pace-builder' ); ?></a>
				</li>
				<li>
					<a href="#pt-form-typo-settings"><?php _e( 'Typography Settings', 'pace-builder' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
	<div class="bbm-modal__section has-tabs">
		<div class="edit-content-wrap">
			<div id="pt-form-design-settings" class="pt-tab-pane">
				<?php ptpb_form_field( 'po_layout' ); ?>
				<?php ptpb_form_field( 'po_fullwidth' ); ?>
			</div>

			<div id="pt-form-typo-settings" class="pt-tab-pane">
				<?php ptpb_form_fonts(); ?>
			</div>
		</div>
	</div>
	<div class="bbm-modal__bottombar">
		<input type="button" class="button button-primary save-row" value="Save"/>
		<input type="button" class="button close-model" value="Close"/>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-insert-row">
	<div id="pt-pb-insert-columns" class="{{{ (typeof update === 'undefined' ) ? '': 'update-columns' }}}">
		<div class="bbm-modal__topbar">
			<h2><?php _e( 'Select Column Layout', 'pace-builder' ); ?></h2>
		</div>
		<div class="bbm-modal__section">
			<ul class="column-layouts">
				<li data-layout="1-1">
					<div class="column-layout full-width"></div>
				</li>
				<li data-layout="1-2,1-2">
					<div class="column-layout column-layout-1_2"></div>
					<div class="column-layout column-layout-1_2"></div>
				</li>
				<li data-layout="1-3,1-3,1-3">
					<div class="column-layout column-layout-1_3"></div>
					<div class="column-layout column-layout-1_3"></div>
					<div class="column-layout column-layout-1_3"></div>
				</li>
				<li data-layout="1-4,1-4,1-4,1-4">
					<div class="column-layout column-layout-1_4"></div>
					<div class="column-layout column-layout-1_4"></div>
					<div class="column-layout column-layout-1_4"></div>
					<div class="column-layout column-layout-1_4"></div>
				</li>
				<?php
				/*
				* Action hook to add custom layouts
				*/
				do_action( 'ptpb_column_layouts' );
				?>
			</ul>
		</div>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-insert-module">
	<div id="pt-pb-insert-modules">
		<div class="bbm-modal__topbar">
			<h2><?php _e( 'Select Module', 'pace-builder' ); ?></h2>
			<div class="pt-pb-top-bar">
				<ul class="pt-topbar-tabs clearfix">
					<li class="tab-active">
						<a href="#pt-pb-all-modules"><?php _e( 'All', 'pace-builder' ); ?></a>
					</li>
					<li>
						<a href="#pt-pb-pb-modules"><?php _e( 'Modules', 'pace-builder' ); ?></a>
					</li>
					<li>
						<a href="#pt-pb-wp-widgets"><?php _e( 'WordPress Widgets', 'pace-builder' ); ?></a>
					</li>
					<# _.each(ptPbApp.modulePanes, function(modules, name) { #>
					<li>
						<a href="#pt-pb-pane-{{{ ptPbApp.paneName(name) }}}">{{{ name }}}</a>
					</li>
					<# }) #>
				</ul>
			</div>
		</div>
		<div class="bbm-modal__section has-tabs">
			<div id="pt-pb-all-modules" class="pt-tab-pane"></div>
			<div id="pt-pb-pb-modules" class="pt-tab-pane">
				<# var modules = new Backbone.Collection( _.map(ptPbOptions.formFields.modules, function(val, key){val.slug = key; return val; }) ); #>
				{{{ ptPbApp.partial('module-items', { models: modules.toJSON() }) }}}
			</div>
			<div id="pt-pb-wp-widgets" class="pt-tab-pane">
				<# var modules = new Backbone.Collection( _.map(ptPbOptions.widgets, function(val, key){val.slug ='widget'; return val; }) ); #>
				{{{ ptPbApp.partial('module-items', { models: modules.toJSON() }) }}}
			</div>
			<# _.each(ptPbApp.modulePanes, function(modules, name) { #>
			<div id="pt-pb-pane-{{{ ptPbApp.paneName(name) }}}" class="pt-tab-pane">
				<# modules = new Backbone.Collection( _.map(modules, function(val, key){val.slug = key; return val; }) ); #>
				{{{ ptPbApp.partial('module-items', { models: modules.toJSON() }) }}}
			</div>
			<# }) #>
		</div>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-module-items">
	<ul class="column-modules">
	<# _(data.models).each(function(module) { #>
		<# var icon = module.slug === 'widget' ? ( module.icon || ( module.class.indexOf('WP_Widget') > -1 ) ? 'dashicons dashicons-wordpress' : 'dashicons dashicons-admin-generic' )
					 : ( ( module.ic.indexOf('dashicons') > -1 ) ? 'dashicons ' + module.ic : module.ic ) #>
		<li class="column-module">
			<div class="module-type" data-module="{{{module.slug.toLowerCase()}}}" {{{ module.slug === 'widget' ?  'data-widget="'+module.class+'"' : '' }}}>
			<# if( (/\.(gif|jpg|jpeg|tiff|png|svg)$/i).test(icon) ) { #>
				<img src="{{{icon}}}" title="{{{module.label}}}" />
			<# } else { #>
				<i class="{{{icon}}}"></i>
			<# } #>
				<h3>{{{module.label}}}</h3>
				<small>{{{module.description}}}</small>
			</div>
		</li>
	<# }) #>
	</ul>
</script>

<script type="text/template" id="pt-pb-tmpl-no-module">
	<div class="module-controls close no-module">
		<div class="edit-module">
			<a href="#" title="Delete" class="remove"><i class="fa fa-trash-o"></i></a>
		</div>
		<div class="admin-label"><?php _e( 'Missing Module', 'pace-builder' ); ?> - {{{data.type}}}</div>
	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-icon-picker">
	<div class="bbm-modal__topbar">
		<h2><?php _e( 'Select an Icon', 'pace-builder' ); ?></h2>
		<div class="pt-pb-top-bar">
			<ul class="pt-topbar-tabs clearfix">
				<# _.each(ptPbOptions.icons, function(icons, name) { 
					var slug = ptPbApp.slug(name);
				#>
				<li class="tab-active">
					<a href="#pt-icons-{{{slug}}}">{{{name}}}</a>
				</li>
				<# }) #>
			</ul>
		</div>
	</div>
	<div class="bbm-modal__section has-tabs">

		<div class="edit-content-wrap">			
			<# _.each(ptPbOptions.icons, function(icons, name) { 
						var slug = ptPbApp.slug(name);
			#>
			<div id="pt-icons-{{{slug}}}" class="pt-tab-pane"></div>
			<# }) #>
		</div>

	</div>
</script>

<script type="text/template" id="pt-pb-tmpl-icon-item">
	<div>
	<# _(data.models).each(function(icon) { #>
		<div class="icon-hover"><a href="#" data-class="{{{icon.cls}}}"><i class="{{{icon.cls}}}"></i> {{{icon.name}}}</a></div>
	<# }) #>
	</div>
</script>