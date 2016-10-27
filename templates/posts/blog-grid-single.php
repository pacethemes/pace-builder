<div class="post-grid-wrap masonry-item">
	<article <?php post_class( 'post-grid' ); ?> id="post-<?php the_ID(); ?>">

		<?php if ( ( ! isset( $show_image ) || $show_image ) ) : ?>
			<div class="post-image blog-normal effect slide-top">
				<a href="<?php the_permalink() ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( $columns == 'one' ? 'full' : ( $columns == 'two' ? 'ptpb-post-big' : 'ptpb-gallery' ) ); ?>
					<?php else : ?>
						<img src="<?php echo PTPB()->plugin_url(); ?>/assets/css/images/post-dummy.jpg"
						     alt="<?php the_title() ?>">
					<?php endif; ?>
				</a>

				<div class="overlay">
					<div class="caption">
						<a href="<?php the_permalink() ?>"><?php _e( 'View more', 'pace-builder' ); ?></a>
					</div>
					<a href="<?php the_permalink() ?>" class="expand">+</a>
					<a href="#" class="close-overlay hidden">x</a>
				</div>
			</div>

		<?php endif; ?>

		<?php if ( ! isset( $show_title ) || $show_title ): ?>
			<h1 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
		<?php endif; ?>

		<?php if ( ( ! isset( $show_meta ) || $show_meta ) && 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<?php ptpb_post_meta(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>

	</article>
</div>
