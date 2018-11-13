<div class="core-grant-list-display-item">
<ul <?php if ( ! empty( $project_id ) ) : ?>class="has-project-id"<?php endif; ?>>
<?php if ( ! empty( $project_id ) ) : ?><li class="project-id"><?php echo esc_html( $project_id ); ?></li><?php endif; ?>
		<li class="grant-title"><<?php echo esc_html( $title_tag ); ?>><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>></li>
		<li class="project-pi"><?php echo esc_html( $pi ); ?></li>
	</ul>
	<div class="grant-link"><a href="<?php echo esc_url( $link ); ?>">Visit <?php echo esc_html( $title ); ?></a></div>
</div>
