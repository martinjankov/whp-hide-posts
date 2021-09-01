<?php
/**
 * Quick edit posts settings.
 *
 * @package    WordPressHidePosts
 */

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<span class="title"><?php esc_html_e( 'Hide Posts', 'whp-hide-posts' ); ?></span>
		<div class='whp_qe_hide_posts'>
			<?php include_once WHP_PLUGIN_DIR . 'views/admin/template-admin-post-metabox.php'; ?>
		</div>
	</div>
</fieldset>
