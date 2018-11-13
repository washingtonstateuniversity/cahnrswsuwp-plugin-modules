<div class="core-post-feed">
	<?php echo wp_kses_post( $items_html ); ?>
</div>
<script>
var core_post_feed = function( $form ) {
	this.ele = $form;
}
</script>
