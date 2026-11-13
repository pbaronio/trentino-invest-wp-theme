<?php
/**
 * Pagine interne (Cookie Policy, Privacy Policy, Ethic Code…).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="ti-page_section">
		<div class="container">
			<div class="page-padding is-page">
				<div class="ti-page_layout">
					<h1 class="heading-l"><?php the_title(); ?></h1>
					<div class="ti-page_content text-m">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_template_part( 'template-parts/contatti' );

get_footer();
