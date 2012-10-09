<?php 

flush_rewrite_rules();

?>
<div class="wrap">
	<?php screen_icon(); ?>

	<h2>
		<?php _e( 'Issuu Settings', 'pronamic_issuu' ); ?>
	</h2>

	<form name="form" action="options.php" method="post"> 
		<?php settings_fields( 'pronamic_issuu' ); ?>

		<?php do_settings_sections( 'pronamic_issuu' ); ?>

		<?php submit_button(); ?>
	</form>
	
	<?php Pronamic_Issuu_Plugin::admin_include( 'plugins.php' ); ?>
	<?php Pronamic_Issuu_Plugin::admin_include( 'pronamic.php' ); ?>
</div>