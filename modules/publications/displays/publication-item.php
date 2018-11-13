<div class="core-publication-item" style="position:relative">
    <h3><?php the_title(); ?></h3>
    <div class="core-publication-summary">
        <?php the_excerpt(); ?>
    </div>
    <div class="core-publication-link"><a style="font-size:0;display:block;position:absolute;height:100%;width:100%;top:0;left:0;" href="<?php echo esc_url( $link ); ?>">Visit <?php the_title(); ?></a></div>
</div>