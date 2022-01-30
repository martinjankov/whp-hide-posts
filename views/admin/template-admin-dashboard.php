<?php
/**
 * Admin Settings Page Template
 *
 * @package    WordPressHidePosts
 */

?>

<div class="wrap">
	<h1><?php esc_html_e( 'WordPress Hide Posts Settings', 'whp-hide-posts' ); ?></h1>
	<hr>
	<form method="post" action="options.php">
		<?php settings_fields( 'whp-settings-group' ); ?>
		<?php do_settings_sections( 'whp-settings-group' ); ?>

		<div class="whp-post-types">
			<p><?php esc_html_e( 'Additionally enable "Hide Posts" functionality on the following post types:', 'whp-hide-posts' ); ?></p>
			<?php
			foreach ( $post_types as $wp_post_type ) :
				if ( 'post' === $wp_post_type->name ) {
					continue;
				}
				?>
				<span class="whp-post-type">
					<label for="<?php echo esc_attr( $wp_post_type->name ); ?>">
						<input
							type="checkbox"
							name="whp_enabled_post_types[]"
							value="<?php echo esc_attr( $wp_post_type->name ); ?>"
							id="<?php echo esc_attr( $wp_post_type->name ); ?>"
							<?php echo in_array( $wp_post_type->name, $enabled_post_types, true ) ? 'checked' : ''; ?>>
						<?php echo esc_html( ucfirst( $wp_post_type->name ) ); ?>
					</label>
				</span>
			<?php endforeach; ?>
		</div>

		<div>
			<p>
				<label for='whp_disable_hidden_on_column'>
					<input type='checkbox' name="whp_disable_hidden_on_column" value='1' <?php checked( $whp_disable_hidden_on_column, 1 ); ?> id='whp_disable_hidden_on_column'>
					<?php esc_html_e( 'Disable "Hidden On" column on post types lists table', 'whp-hide-posts' ); ?>
				</label>
			</p>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
