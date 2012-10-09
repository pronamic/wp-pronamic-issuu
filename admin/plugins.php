<h2>
	<?php _e( 'Plugins', 'pronamic_issuu' ); ?>
</h2>

<?php 

$plugin_tips = array(
	'issuu-pdf-sync/issuu-pdf-sync.php' => array(
		'slug' => 'issuu-pdf-sync' , 
		'name' => 'Issuu PDF Sync'
	),
	'issuu-pdf-sync-pronamic/issuu-pdf-sync.php' => array(
		'slug' => 'issuu-pdf-sync-pronamic' , 
		'name' => 'Issuu PDF Sync Pronamic'
	),
	'members/members.php' => array(
		'slug' => 'members',
		'name' => 'Members'
	)
);

?>

<table class="form-table">
	<?php foreach ( $plugin_tips as $file => $data ): ?>
		<tr>
			<td>
				<?php echo $data['name']; ?>
			</td>
			<td>
				<?php

				if ( is_plugin_active( $file ) ) {
					echo '&#9745;';
				} else {
					echo '&#9744;';
				}

				?>
			</td>
			<td>
				<?php 

				$search_url = add_query_arg(
					array(
						'tab'  => 'search',
						'type' => 'term',
						's'    => $data['slug']
					), 
					'plugin-install.php'
				);

				?>
				<a href="<?php echo esc_attr( $search_url ); ?>">
					<?php _e( 'Search Plugin', 'pronamic_issuu' ); ?>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>