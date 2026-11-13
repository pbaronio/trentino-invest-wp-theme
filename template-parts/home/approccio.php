<?php
/**
 * Il nostro approccio: titolo + punti (repeater).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_items = ti_rows( 'approccio_items', array( 'titolo', 'testo' ) );
$ti_last  = count( $ti_items ) - 1;
?>
<section id="approccio" class="approccio_section">
	<div class="container">
		<div class="page-padding is-approccio">
			<div data-reveal-group="" class="approccio_layout">
				<div class="section-header">
					<?php ti_label( 'label-approccio', ti_get( 'approccio_etichetta' ) ); ?>
					<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'approccio_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				</div>
				<?php if ( $ti_items ) : ?>
					<div data-reveal-group="" class="approccio_grid">
						<?php foreach ( $ti_items as $ti_i => $ti_item ) : ?>
							<div class="approccio_item<?php echo $ti_i === $ti_last ? ' is-last' : ''; ?>">
								<div class="heading-m"><?php echo esc_html( $ti_item['titolo'] ); ?></div>
								<div class="text-m"><?php echo ti_br( $ti_item['testo'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
