<?php
/**
 * Intro: titolo, testo, bottone e due immagini.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_cta_text = (string) ti_get( 'intro_cta_testo' );
$ti_cta_link = (string) ti_get( 'intro_cta_link' );
$ti_blank    = ti_is_external( $ti_cta_link );
?>
<section class="intro_section">
	<div class="container">
		<div class="page-padding is-intro">
			<div data-reveal-group="" class="intro_layout">
				<div class="intro_header">
					<div class="intro_header-1">
						<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'intro_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					</div>
					<div class="intro_header-2">
						<div data-split="heading" class="text-m"><?php echo esc_html( ti_get( 'intro_testo' ) ); ?></div>
						<?php if ( '' !== trim( $ti_cta_text ) ) : ?>
							<div class="button">
								<?php ti_button( $ti_cta_link, $ti_cta_text, 'is-first', $ti_blank ); ?>
								<a href="<?php echo esc_url( $ti_cta_link ? $ti_cta_link : '#' ); ?>"<?php echo $ti_blank ? ' target="_blank" rel="noopener"' : ''; ?> class="button-mobile w-inline-block">
									<div class="text-s"><?php echo esc_html( $ti_cta_text ); ?></div>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div data-reveal-group="" class="intro_img">
					<div class="intro-img-2"><?php echo ti_img( (int) ti_get( 'intro_immagine_1', null, 0 ), array( 'class' => 'img-100 rounded', 'sizes' => '(max-width: 767px) 100vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="intro-img-1"><?php echo ti_img( (int) ti_get( 'intro_immagine_2', null, 0 ), array( 'class' => 'img-100 rounded', 'sizes' => '(max-width: 767px) 100vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				</div>
			</div>
		</div>
	</div>
</section>
