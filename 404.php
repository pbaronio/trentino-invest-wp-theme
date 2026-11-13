<?php
/**
 * Pagina non trovata.
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
				<h1 class="heading-l">Pagina non trovata</h1>
				<div class="ti-page_content text-m">
					<p>La pagina che cerchi non esiste o è stata spostata.</p>
				</div>
				<div class="button"><?php ti_button( home_url( '/' ), 'Torna alla home' ); ?></div>
			</div>
		</div>
	</div>
</section>
<?php
get_template_part( 'template-parts/contatti' );
get_footer();
