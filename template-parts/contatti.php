<?php
/**
 * Contatti + footer (home e pagine interne). Dati da Impostazioni sito.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_email     = (string) ti_get( 'contatti_email', 'option' );
$ti_mailto    = $ti_email ? 'mailto:' . antispambot( $ti_email ) : '#';
$ti_copyright = str_replace( '{anno}', wp_date( 'Y' ), (string) ti_get( 'footer_copyright', 'option' ) );

$ti_legal = array();
foreach ( array( 'footer_pagina_cookie', 'footer_pagina_privacy', 'footer_pagina_etico' ) as $ti_field ) {
	$ti_page = (int) ti_get( $ti_field, 'option', 0 );
	if ( $ti_page && 'publish' === get_post_status( $ti_page ) ) {
		$ti_legal[] = $ti_page;
	}
}
$ti_credits_text = (string) ti_get( 'footer_credits_testo', 'option' );
$ti_credits_link = (string) ti_get( 'footer_credits_link', 'option' );
?>
<section id="contatti" class="contatti_section">
	<div class="container">
		<div class="page-padding is-contatti">
			<div class="contatti_layout">
				<div class="section-header is-contatti">
					<?php ti_label( 'label-contatti', ti_get( 'contatti_etichetta', 'option' ) ); ?>
					<div class="footer_cta">
						<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'contatti_titolo', 'option' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
						<div class="button">
							<?php ti_button( $ti_mailto, ti_get( 'contatti_cta_testo', 'option' ) ); ?>
						</div>
					</div>
				</div>
				<div class="contatti_body">
					<?php if ( $ti_email ) : ?>
						<a href="<?php echo esc_attr( $ti_mailto ); ?>" class="link w-inline-block">
							<div class="text-s"><?php echo esc_html( antispambot( $ti_email ) ); ?></div>
						</a>
					<?php endif; ?>
					<div class="text-s"><?php echo esc_html( ti_get( 'contatti_indirizzo', 'option' ) ); ?></div>
				</div>
				<div class="footer">
					<div class="text-s"><?php echo esc_html( $ti_copyright ); ?></div>
					<div class="footer_link">
						<?php foreach ( $ti_legal as $ti_page ) : ?>
							<a href="<?php echo esc_url( get_permalink( $ti_page ) ); ?>" class="text-s link_footer"><?php echo esc_html( get_the_title( $ti_page ) ); ?></a>
						<?php endforeach; ?>
						<?php if ( $ti_credits_text && $ti_credits_link ) : ?>
							<a href="<?php echo esc_url( $ti_credits_link ); ?>" target="_blank" rel="noopener" class="text-s link_footer"><?php echo esc_html( $ti_credits_text ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
