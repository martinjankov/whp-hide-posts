<?php
/**
 * Template for showing the hide options on create/edit post.
 *
 * @package WordPressHidePosts
 */

?>
<div class='whp_hide_posts'>
	<p>
		<label for='whp_select_all'>
			<input type='checkbox' id='whp_select_all'>
			<strong><?php esc_html_e( 'SELECT ALL', 'whp-hide-posts' ); ?></strong>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_frontpage'>
			<input type='checkbox' name="whp_hide_on_frontpage" value='1' <?php checked( $whp_hide_on_frontpage, 1 ); ?> id='whp_hide_on_frontpage'>
			<?php esc_html_e( 'Hide on frontpage', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_categories'>
			<input type='checkbox' name="whp_hide_on_categories" value='1' <?php checked( $whp_hide_on_categories, 1 ); ?> id='whp_hide_on_categories'>
			<?php esc_html_e( 'Hide on categories', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_search'>
			<input type='checkbox' name="whp_hide_on_search" value='1' <?php checked( $whp_hide_on_search, 1 ); ?> id='whp_hide_on_search'>
			<?php esc_html_e( 'Hide on search', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_tags'>
			<input type='checkbox' name="whp_hide_on_tags" value='1' <?php checked( $whp_hide_on_tags, 1 ); ?> id='whp_hide_on_tags'>
			<?php esc_html_e( 'Hide on tags page', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_authors'>
			<input type='checkbox' name="whp_hide_on_authors" value='1' <?php checked( $whp_hide_on_authors, 1 ); ?> id='whp_hide_on_authors'>
			<?php esc_html_e( 'Hide on authors page', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<?php if ( whp_plugin()->is_custom_post_type( $post ) ) : ?>
		<p>
			<label for='whp_hide_on_cpt_archive'>
				<input type='checkbox' name="whp_hide_on_cpt_archive" value='1' <?php checked( $whp_hide_on_cpt_archive, 1 ); ?> id='whp_hide_on_cpt_archive'>
				<?php esc_html_e( 'Hide on CPT archive page', 'whp-hide-posts' ); ?>
			</label>
		</p>
	<?php endif; ?>
	<p>
		<label for='whp_hide_on_date'>
			<input type='checkbox' name="whp_hide_on_date" value='1' <?php checked( $whp_hide_on_date, 1 ); ?> id='whp_hide_on_date'>
			<?php esc_html_e( 'Hide on date archive', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_archive'>
			<input type='checkbox' name="whp_hide_on_archive" value='1' <?php checked( $whp_hide_on_archive, 1 ); ?> id='whp_hide_on_archive'>
			<?php esc_html_e( 'Hide on archive page', 'whp-hide-posts' ); ?>
			<em><?php esc_html_e( '(This includes any custom taxonomy archive pages)', 'whp-hide-posts' ); ?></em>
		</label>
	</p>
	<p>
		<label for='whp_hide_in_rss_feed'>
			<input type='checkbox' name="whp_hide_in_rss_feed" value='1' <?php checked( $whp_hide_in_rss_feed, 1 ); ?> id='whp_hide_in_rss_feed'>
			<?php esc_html_e( 'Hide in RSS Feed', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_blog_page'>
			<input type='checkbox' name="whp_hide_on_blog_page" value='1' <?php checked( $whp_hide_on_blog_page, 1 ); ?> id='whp_hide_on_blog_page'>
			<?php esc_html_e( 'Hide on blog page', 'whp-hide-posts' ); ?>
			<em><?php esc_html_e( '(The POSTS PAGE that is selected in Settings -> Reading)', 'whp-hide-posts' ); ?></em>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_post_navigation'>
			<input type='checkbox' name="whp_hide_on_post_navigation" value='1' <?php checked( $whp_hide_on_post_navigation, 1 ); ?> id='whp_hide_on_post_navigation'>
			<?php esc_html_e( 'Hide from post navigation', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_recent_posts'>
			<input type='checkbox' name="whp_hide_on_recent_posts" value='1' <?php checked( $whp_hide_on_recent_posts, 1 ); ?> id='whp_hide_on_recent_posts'>
			<?php esc_html_e( 'Hide from recent posts widget', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_rest_api'>
			<input type='checkbox' name="whp_hide_on_rest_api" value='1' <?php checked( $whp_hide_on_rest_api, 1 ); ?> id='whp_hide_on_rest_api'>
			<?php esc_html_e( 'Hide from REST API', 'whp-hide-posts' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_single_post_page'>
			<input type='checkbox' name="whp_hide_on_single_post_page" value='1' <?php checked( $whp_hide_on_single_post_page, 1 ); ?> id='whp_hide_on_single_post_page'>
			<?php esc_html_e( 'Hide on single post page', 'whp-hide-posts' ); ?>
			<em><?php esc_html_e( '(This will hide post from recent posts, related posts and any other widget shown on the single post page)', 'whp-hide-posts' ); ?></em>
		</label>
	</p>
	<?php if ( whp_plugin()->is_woocommerce_active() && whp_plugin()->is_woocommerce_product() ) : ?>
		<h4><?php esc_html_e( 'Woocommerce options', 'whp-hide-posts' ); ?></h4>
		<p>
			<label for='whp_hide_on_store'>
				<input type='checkbox' name="whp_hide_on_store" value='1' <?php checked( $whp_hide_on_store, 1 ); ?> id='whp_hide_on_store'>
				<?php esc_html_e( 'Hide on shop page', 'whp-hide-posts' ); ?>
			</label>
		</p>
		<p>
			<label for='whp_hide_on_product_category'>
				<input type='checkbox' name="whp_hide_on_product_category" value='1' <?php checked( $whp_hide_on_product_category, 1 ); ?> id='whp_hide_on_product_category'>
				<?php esc_html_e( 'Hide on product category page', 'whp-hide-posts' ); ?>
			</label>
		</p>
	<?php endif; ?>
</div>
