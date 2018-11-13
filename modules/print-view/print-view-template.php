<!DOCTYPE html>
<html>
<head>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=EDGE">
	<meta charset="UTF-8" />
	<title>Extension Publications | CAHNRS Core | Washington State University</title>
	<meta name="viewport" content="width=device-width, user-scalable=yes">
	<?php wp_head(); ?>
	<style type="text/css" media="print">
		@page {
    		size: auto;   /* auto is the initial value */
    		margin: 0;  /* this affects the margin in the printer settings */
		}
	</style>
</head>
<body id="core-print-body" <?php body_class(); ?>>
<div id="core-print-view">
	<?php if ( is_active_sidebar( 'print_header' ) ) : ?>
	<div id="core-print-view-header">
		<?php dynamic_sidebar( 'print_header' ); ?>
	</div>
	<?php endif; ?>
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<h1 class="core-print-post-title">
			<?php the_title(); ?>
		</h1>
		<div class="core-print-post-content">
			<?php the_content(); ?>
		</div>
		<?php endwhile; ?>
	<?php endif; ?>
	<?php if ( is_active_sidebar( 'print_footer' ) ) : ?>
	<div id="core-print-view-footer">
		<?php dynamic_sidebar( 'print_footer' ); ?>
	</div>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
