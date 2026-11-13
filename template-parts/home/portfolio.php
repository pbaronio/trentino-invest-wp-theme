<?php
/**
 * Portfolio: slider delle aziende (CPT ti_investimento).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_items = ti_posts( 'ti_investimento' );
?>
<section id="portfolio" class="portfolio_section">
	<div class="container">
		<div class="page-padding is-portfolio">
			<div class="portfolio_layout">
				<?php ti_label( 'label-portfolio', ti_get( 'portfolio_etichetta' ) ); ?>
				<div class="slide_layout is-investimenti">
					<div class="slide-titolo is-investimenti">
						<div class="capital-m"><?php echo ti_br( ti_get( 'portfolio_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					</div>
					<div class="slide">
						<div data-swiper-group="" class="swiper-group">
							<div data-swiper-wrap="" class="swiper">
								<div class="swiper-wrapper"></div>
							</div>
							<div class="swiper-navigation"></div>
						</div>
						<?php if ( $ti_items ) : ?>
							<div aria-label="Slider" data-gsap-slider-init="" role="region" aria-roledescription="carousel" class="gsap-slider">
								<div data-gsap-slider-collection="" class="gsap-slider__collection">
									<div data-gsap-slider-list="" class="gsap-slider__list">
										<?php
										foreach ( $ti_items as $ti_inv ) :
											$ti_logo = (int) get_post_meta( $ti_inv->ID, 'logo', true );
											$ti_sito = (string) get_post_meta( $ti_inv->ID, 'sito', true );
											$ti_desc = (string) get_post_meta( $ti_inv->ID, 'descrizione', true );
											?>
											<div data-gsap-slider-item-status="active" data-gsap-slider-item="" class="gsap-slider__item is-investimenti">
												<div class="demo-card is-investimenti">
													<div class="investimenti_card"><?php echo ti_img( $ti_logo, array( 'class' => 'card-investimenti-logo', 'alt' => get_the_title( $ti_inv ), 'sizes' => '(max-width: 767px) 50vw, 15vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
													<div class="card-detail">
														<div class="card-detail-content">
															<div class="heading-m"><?php echo esc_html( get_the_title( $ti_inv ) ); ?></div>
															<div class="text-s"><?php echo ti_br( $ti_desc ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
														</div>
													</div>
													<div class="investimenti-card-buttons">
														<a href="#" class="card-toggle w-inline-block">
															<div class="w-embed"><button class="card-toggle" type="button" aria-label="Apri card">
																<span class="plus-line plus-line-h"></span>
																<span class="plus-line plus-line-v"></span>
															</button></div>
														</a>
														<?php if ( $ti_sito ) : ?>
															<a href="<?php echo esc_url( $ti_sito ); ?>" target="_blank" rel="noopener" class="visit w-inline-block" aria-label="<?php echo esc_attr( 'Sito di ' . get_the_title( $ti_inv ) ); ?>">
																<div class="w-embed"><?php echo ti_svg( 'arrow-visit' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
															</a>
														<?php endif; ?>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
								<div data-gsap-slider-controls="" class="gsap-slider__controls"><button data-gsap-slider-control="prev" class="gsap-slider__control">Prev</button><button data-gsap-slider-control="next" class="gsap-slider__control">Next</button></div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
