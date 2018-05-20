<?php

get_header();
// Using the have_post loop will give other plugins a chance hook into the 'in the loop' process, but since we are trying to just drive the point home, i opted in to use the_post()
the_post();

$file = get_field('pdf'); 
 ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

				<form id="request_download" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
					<input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
					<input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
					<input type="hidden" name="action" value="download_e_book">
					<?php wp_nonce_field( 'download_e_book_' . get_the_ID(), 'nonce_token' ); ?>
					<button id="download_button" type="submit">Download E Book</button>
				</form>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
