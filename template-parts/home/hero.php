<?php
/**
 * Apertura: animazione di caricamento + immagine a tutto schermo + titolo.
 *
 * L'animazione (assets/js/custom/loader.js) fa scorrere 5 immagini: 2 a sinistra,
 * l'immagine principale al centro (che poi si allarga a tutto schermo), 2 a destra.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_hero   = (int) ti_get( 'hero_immagine', null, 0 );
$ti_loader = array_values( array_filter( array_map( 'intval', (array) ti_get( 'hero_loader', null, array() ) ) ) );

// Sequenza: [sx1, sx2, HERO, dx1, dx2]. Se mancano immagini si ripete quella principale.
$ti_seq = array(
	$ti_loader[0] ?? $ti_hero,
	$ti_loader[1] ?? $ti_hero,
	$ti_hero,
	$ti_loader[2] ?? $ti_hero,
	$ti_loader[3] ?? $ti_hero,
);
$ti_title = (string) ti_get( 'hero_titolo' );
?>
<section data-slideshow="wrap" class="crisp-header is--loading is--hidden">
	<div class="crisp-header__slider">
		<div class="crisp-header__slider-list">
			<div data-slideshow="slide" class="crisp-header__slider-slide is--current">
				<?php
				echo ti_img( // phpcs:ignore WordPress.Security.EscapeOutput
					$ti_hero,
					array(
						'class'   => 'crisp-header__slider-slide-inner',
						'loading' => 'eager',
						'alt'     => '',
						'attrs'   => array(
							'data-slideshow' => 'parallax',
							'draggable'      => 'false',
							'fetchpriority'  => 'high',
						),
					)
				);
				?>
			</div>
		</div>
	</div>
	<div class="crisp-loader">
		<div class="crisp-loader__wrap">
			<div class="crisp-loader__groups">
				<div class="crisp-loader__group is--duplicate">
					<?php foreach ( $ti_seq as $ti_id ) : ?>
						<div class="crisp-loader__single">
							<div class="crisp-loader__media"><?php echo ti_img( $ti_id, array( 'class' => 'crisp-loader__cover-img', 'loading' => 'eager', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="crisp-loader__group is--relative">
					<?php foreach ( $ti_seq as $ti_i => $ti_id ) : ?>
						<div class="crisp-loader__single">
							<?php if ( 2 === $ti_i ) : ?>
								<div class="crisp-loader__media is--scaling is--radius"><?php echo ti_img( $ti_id, array( 'class' => 'crisp-loader__cover-img', 'loading' => 'eager', 'alt' => '', 'srcset' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							<?php else : ?>
								<div class="crisp-loader__media"><?php echo ti_img( $ti_id, array( 'class' => 'crisp-loader__cover-img is--scale-down', 'loading' => 'eager', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="crisp-loader__fade"></div>
			<div class="crisp-loader__fade is--duplicate"></div>
		</div>
	</div>
	<div class="crisp-header__content">
		<div class="crisp-header__center">
			<div class="container">
				<h1 data-fit-width="" class="crisp-header__h1"><?php echo esc_html( $ti_title ); ?></h1>
			</div>
		</div>
	</div>
</section>
