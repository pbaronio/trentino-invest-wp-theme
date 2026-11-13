<?php
/**
 * Template di riserva. Il sito usa front-page.php (home) e page.php (pagine).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ti-page_section">
	<div class="container">
		<div class="page-padding is-page">
			<div class="ti-page_layout">
				<?php if ( have_posts() ) : ?>
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="ti-page_article">
							<h2 class="heading-m"><a class="link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="ti-page_content text-m"><?php the_excerpt(); ?></div>
						</article>
					<?php endwhile; ?>
				<?php else : ?>
					<h1 class="heading-l">Nessun contenuto</h1>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php
get_template_part( 'template-parts/contatti' );
get_footer();
