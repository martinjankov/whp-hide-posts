<?php
/**
 * Template for showing the hide options on create/edit post.
 *
 * @package wordpress-hide-posts
 */
global $post;
$taxonomies = get_object_taxonomies( $post );

?>
<div class='whp_hide_posts'>
	<p>
		<label for='whp_select_all'>
			<input type='checkbox' <?php checked( $whp_select_all, 1 ); ?> id='whp_select_all'>
		 	<strong><?php _e( 'SELECT ALL', 'whp' ); ?></strong>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_frontpage'>
			<input type='checkbox' name="whp_hide_on_frontpage" value='1' <?php checked( $whp_hide_on_frontpage, 1 ); ?> id='whp_hide_on_frontpage'>
		 	<?php _e( 'Hide on frontpage', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_categories'>
			<input type='checkbox' name="whp_hide_on_categories" value='1' <?php checked( $whp_hide_on_categories, 1 ); ?> id='whp_hide_on_categories'>
			<?php _e( 'Hide on categories', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_search'>
			<input type='checkbox' name="whp_hide_on_search" value='1' <?php checked( $whp_hide_on_search, 1 ); ?> id='whp_hide_on_search'>
			<?php _e( 'Hide on search', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_tags'>
			<input type='checkbox' name="whp_hide_on_tags" value='1' <?php checked( $whp_hide_on_tags, 1 ); ?> id='whp_hide_on_tags'>
			<?php _e( 'Hide on tags page', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_authors'>
			<input type='checkbox' name="whp_hide_on_authors" value='1' <?php checked( $whp_hide_on_authors, 1 ); ?> id='whp_hide_on_authors'>
			<?php _e( 'Hide on authors page', 'whp' ); ?>
		</label>
	</p>
    <?php if ( whp_is_custom_post_type( $post ) ): ?>
        <p>
            <label for='whp_hide_on_cpt_archive'>
                <input type='checkbox' name="whp_hide_on_cpt_archive" value='1' <?php checked( $whp_hide_on_cpt_archive, 1 ); ?> id='whp_hide_on_cpt_archive'>
                <?php _e( 'Hide on CPT archive page', 'whp' ); ?>
            </label>
        </p>
    <?php endif; ?>
	<p>
		<label for='whp_hide_on_date'>
			<input type='checkbox' name="whp_hide_on_date" value='1' <?php checked( $whp_hide_on_date, 1 ); ?> id='whp_hide_on_date'>
			<?php _e( 'Hide on date archive', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_in_rss_feed'>
			<input type='checkbox' name="whp_hide_in_rss_feed" value='1' <?php checked( $whp_hide_in_rss_feed, 1 ); ?> id='whp_hide_in_rss_feed'>
			<?php _e( 'Hide in RSS Feed', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_blog_page'>
			<input type='checkbox' name="whp_hide_on_blog_page" value='1' <?php checked( $whp_hide_on_blog_page, 1 ); ?> id='whp_hide_on_blog_page'>
			<?php _e( 'Hide on blog page', 'whp' ); ?>
			<em><?php _e( '(The POSTS PAGE that is selected in Settings -> Reading)', 'whp' ); ?></em>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_post_navigation'>
			<input type='checkbox' name="whp_hide_on_post_navigation" value='1' <?php checked( $whp_hide_on_post_navigation, 1 ); ?> id='whp_hide_on_post_navigation'>
			<?php _e( 'Hide from post navigation', 'whp' ); ?>
		</label>
	</p>
	<p>
		<label for='whp_hide_on_recent_posts'>
			<input type='checkbox' name="whp_hide_on_recent_posts" value='1' <?php checked( $whp_hide_on_recent_posts, 1 ); ?> id='whp_hide_on_recent_posts'>
			<?php _e( 'Hide from recent posts widget', 'whp' ); ?>
		</label>
	</p>
	<?php if ( whp_wc_exists() && whp_admin_wc_product() ) : ?>
		<h4><?php _e( 'Woocommerce options', 'whp' ); ?></h4>
		<p>
			<label for='whp_hide_on_store'>
				<input type='checkbox' name="whp_hide_on_store" value='1' <?php checked( $whp_hide_on_store, 1 ); ?> id='whp_hide_on_store'>
				<?php _e( 'Hide on shop page', 'whp' ); ?>
			</label>
		</p>
		<p>
			<label for='whp_hide_on_product_category'>
				<input type='checkbox' name="whp_hide_on_product_category" value='1' <?php checked( $whp_hide_on_product_category, 1 ); ?> id='whp_hide_on_product_category'>
				<?php _e( 'Hide on product category page', 'whp' ); ?>
			</label>
		</p>
	<?php endif; ?>
    <?php if ( ! empty( $taxonomies ) ): ?>
        <p>
            <label for='whp_hide_on_cpt_tax'>
                <!-- <input type='checkbox' name="whp_hide_on_cpt_tax" value='1' <?php //checked( $whp_hide_on_cpt_tax, 1 ); ?> id='whp_hide_on_cpt_tax'> -->
                <?php _e( 'Hide on tax:', 'whp' ); ?>
                <select name="whp_hide_on_cpt_tax[]" id="whp_hide_on_cpt_tax" multiple>
                    <?php foreach ( $taxonomies as $tax ): ?>
                        <option value="<?php echo $tax; ?>" <?php is_array( $whp_hide_on_cpt_tax ) && selected( in_array( $tax, $whp_hide_on_cpt_tax ), 1 ) ?>><?php echo ucfirst( $tax ); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
    <?php endif; ?>
</div>
