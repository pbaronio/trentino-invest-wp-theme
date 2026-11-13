<?php
/**
 * Team: uno slider per ogni gruppo (tassonomia ti_gruppo), persone dal CPT ti_membro.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_groups = array();
foreach ( ti_gruppi() as $ti_term ) {
	$ti_people = ti_posts(
		'ti_membro',
		array(
			'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'ti_gruppo',
					'field'    => 'term_id',
					'terms'    => $ti_term->term_id,
				),
			),
		)
	);
	if ( $ti_people ) {
		$ti_title    = (string) get_term_meta( $ti_term->term_id, 'titolo', true );
		$ti_groups[] = array(
			'title'  => '' !== trim( $ti_title ) ? $ti_title : $ti_term->name,
			'people' => $ti_people,
		);
	}
}
?>
<section id="team" class="team_section">
	<div class="container">
		<div class="page-padding is-team">
			<div class="team_layout">
				<div class="section-header is-team">
					<?php ti_label( 'label-team', ti_get( 'team_etichetta' ) ); ?>
					<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'team_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				</div>
				<?php if ( $ti_groups ) : ?>
					<div class="team-slider_container">
						<?php foreach ( $ti_groups as $ti_g => $ti_group ) : ?>
							<div class="slide1">
								<div class="<?php echo 0 === $ti_g ? 'line_top' : 'line-divider'; ?>"></div>
								<div class="slide_layout">
									<div class="slide-titolo">
										<div class="capital-m"><?php echo ti_br( $ti_group['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									</div>
									<div class="slide">
										<div data-swiper-group="" class="swiper-group">
											<div data-swiper-wrap="" class="swiper">
												<div class="swiper-wrapper"></div>
											</div>
											<div class="swiper-navigation"></div>
										</div>
										<div aria-label="Slider" data-gsap-slider-init="" role="region" aria-roledescription="carousel" class="gsap-slider">
											<div data-gsap-slider-collection="" class="gsap-slider__collection">
												<div data-gsap-slider-list="" class="gsap-slider__list">
													<?php
													foreach ( $ti_group['people'] as $ti_p ) :
														$ti_foto     = (int) get_post_meta( $ti_p->ID, 'foto', true );
														$ti_ruolo    = (string) get_post_meta( $ti_p->ID, 'ruolo', true );
														$ti_linkedin = (string) get_post_meta( $ti_p->ID, 'linkedin', true );
														$ti_top      = (bool) get_post_meta( $ti_p->ID, 'foto_in_alto', true );
														$ti_white    = (bool) get_post_meta( $ti_p->ID, 'icona_chiara', true );
														?>
														<div data-gsap-slider-item-status="active" data-gsap-slider-item="" class="gsap-slider__item">
															<div class="demo-card">
																<?php echo ti_img( $ti_foto, array( 'class' => 'img_card' . ( $ti_top ? ' is-nome' : '' ), 'alt' => get_the_title( $ti_p ), 'sizes' => '(max-width: 767px) 80vw, 25vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
																<div class="demo-card__visual"></div>
																<div class="demo-card__text"><span class="demo-card__title text-m"><?php echo esc_html( get_the_title( $ti_p ) ); ?></span>
																	<?php if ( '' !== trim( $ti_ruolo ) ) : ?>
																		<div class="text-s"><?php echo esc_html( $ti_ruolo ); ?></div>
																	<?php endif; ?>
																</div>
																<div class="linear_bg"></div>
																<?php if ( $ti_linkedin ) : ?>
																	<a href="<?php echo esc_url( $ti_linkedin ); ?>" target="_blank" rel="noopener" class="linkedin_cta<?php echo $ti_white ? ' is-white' : ''; ?> w-inline-block" aria-label="<?php echo esc_attr( 'LinkedIn di ' . get_the_title( $ti_p ) ); ?>">
																		<div class="w-embed"><?php echo ti_svg( $ti_white ? 'linkedin-white' : 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
																	</a>
																<?php endif; ?>
															</div>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
											<div data-gsap-slider-controls="" class="gsap-slider__controls"><button data-gsap-slider-control="prev" class="gsap-slider__control">Prev</button><button data-gsap-slider-control="next" class="gsap-slider__control">Next</button></div>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
