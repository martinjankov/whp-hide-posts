<?php
/**
 * Template for showing the hide options on create/edit post.
 *
 * @package wordpress-hide-posts
 */

?>
<div class='whp_hide_posts'>
	<p>
		<label for='whp_hide_on_frontpage'>
			<input type='checkbox' name="whp_hide_on_frontpage" value='1' <?php checked($whp_hide_on_frontpage, 1); ?> id='whp_hide_on_frontpage'>
		 	Hide on frontpage
		</label>
	</p>
	<p>
		<label for='whp_hide_on_categories'>
			<input type='checkbox' name="whp_hide_on_categories" value='1' <?php checked($whp_hide_on_categories, 1); ?> id='whp_hide_on_categories'>
		 	Hide on categories
		</label>
	</p>
	<p>
		<label for='whp_hide_on_search'>
			<input type='checkbox' name="whp_hide_on_search" value='1' <?php checked($whp_hide_on_search, 1); ?> id='whp_hide_on_search'>
		 	Hide on search
		</label>
	</p>
	<p>
		<label for='whp_hide_on_tags'>
			<input type='checkbox' name="whp_hide_on_tags" value='1' <?php checked($whp_hide_on_tags, 1); ?> id='whp_hide_on_tags'>
		 	Hide on tags page
		</label>
	</p>
	<p>
		<label for='whp_hide_on_authors'>
			<input type='checkbox' name="whp_hide_on_authors" value='1' <?php checked($whp_hide_on_authors, 1); ?> id='whp_hide_on_authors'>
		 	Hide on authors page
		</label>
	</p>
</div>
