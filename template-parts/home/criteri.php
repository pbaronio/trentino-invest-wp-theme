<?php
/**
 * I nostri criteri: titolo + fisarmonica (repeater).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_items = ti_rows( 'criteri_items', array( 'titolo', 'testo' ) );
$ti_last  = count( $ti_items ) - 1;
?>
<section id="criteri" class="criteri_section">
	<div class="container">
		<div class="page-padding is-criteri">
			<div class="criteri_layout">
				<div class="section-header">
					<?php ti_label( 'label-criteri', ti_get( 'criteri_etichetta' ) ); ?>
				</div>
				<div class="criteri_content">
					<div class="criteri_heading">
						<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'criteri_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					</div>
					<?php if ( $ti_items ) : ?>
						<div class="criteri_accordion">
							<div data-accordion-close-siblings="true" data-accordion-css-init="" class="accordion-css">
								<ul class="accordion-css__list">
									<?php foreach ( $ti_items as $ti_i => $ti_item ) : ?>
										<li data-accordion-status="not-active" class="accordion-css__item<?php echo $ti_i === $ti_last ? ' is-last' : ''; ?>">
											<div data-hover="" data-accordion-toggle="" class="accordion-css__item-top">
												<h3 data-split="heading" class="heading-m"><?php echo esc_html( $ti_item['titolo'] ); ?></h3>
												<div class="accordion-css__item-icon"><?php echo ti_svg( 'accordion-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
											</div>
											<div class="accordion-css__item-bottom">
												<div class="accordion-css__item-bottom-wrap">
													<div class="accordion-css__item-bottom-content text-m">
														<p class="accordion-css__item-p<?php echo $ti_i ? ' text-m' : ''; ?>"><?php echo ti_br( $ti_item['testo'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
													</div>
												</div>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
