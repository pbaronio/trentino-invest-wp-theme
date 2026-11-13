<?php
/**
 * Chi siamo: titolo, barre delle quote e loghi dei soci (CPT ti_socio).
 *
 * Barre: le larghezze sono proporzionali alle quote. Le barre consecutive con la stessa
 * quota vengono raggruppate in una riga (finché la riga non supera la quota più grande),
 * come nel design originale: 40% | 12% 12% 12% | 12% 12%. In mobile ogni riga va a capo.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

$ti_soci   = ti_posts( 'ti_socio' );
$ti_colori = ti_colori_soci();
$ti_items  = array();
foreach ( $ti_soci as $ti_s ) {
	$ti_col     = (string) get_post_meta( $ti_s->ID, 'colore', true );
	$ti_items[] = array(
		'post'   => $ti_s,
		'pct'    => (float) get_post_meta( $ti_s->ID, 'percentuale', true ),
		'logo'   => (int) get_post_meta( $ti_s->ID, 'logo', true ),
		'sito'   => (string) get_post_meta( $ti_s->ID, 'sito', true ),
		'colore' => $ti_colori[ $ti_col ] ?? $ti_colori['orange'],
	);
}

// Righe delle barre.
$ti_bars  = array_values( array_filter( $ti_items, static fn( $i ) => $i['pct'] > 0 ) );
$ti_sum   = array_sum( array_column( $ti_bars, 'pct' ) );
$ti_max   = $ti_bars ? max( array_column( $ti_bars, 'pct' ) ) : 0;
$ti_rows  = array();
$ti_row   = array();
$ti_rsum  = 0;
foreach ( $ti_bars as $ti_b ) {
	$ti_same = ! $ti_row || abs( $ti_row[0]['pct'] - $ti_b['pct'] ) < 0.0001;
	if ( $ti_row && ( ! $ti_same || $ti_rsum + $ti_b['pct'] > $ti_max + 0.0001 ) ) {
		$ti_rows[] = $ti_row;
		$ti_row    = array();
		$ti_rsum   = 0;
	}
	$ti_row[] = $ti_b;
	$ti_rsum += $ti_b['pct'];
}
if ( $ti_row ) {
	$ti_rows[] = $ti_row;
}

// Classi Webflow dei bordi delle card in base alla posizione (griglia da 3 / 2 colonne).
$ti_pos_class = array( 1 => '', 2 => 'finanziaria', 3 => 'isa', 4 => 'fincoop', 5 => '', 0 => 'agriduemila' );
$ti_link_text = (string) ti_get( 'chisiamo_link_testo' );
?>
<section id="chi-siamo" class="about_section">
	<div class="container">
		<div class="page-padding is-about">
			<div class="about_layout">
				<div class="section-header">
					<?php ti_label( 'label-chi-siamo', ti_get( 'chisiamo_etichetta' ) ); ?>
					<h2 data-split="heading" class="heading-l"><?php echo ti_br( ti_get( 'chisiamo_titolo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				</div>

				<?php if ( $ti_rows && $ti_sum > 0 ) : ?>
					<div class="bar-container">
						<?php foreach ( $ti_rows as $ti_r => $ti_row ) : ?>
							<?php $ti_w = array_sum( array_column( $ti_row, 'pct' ) ) / $ti_sum * 100; ?>
							<div class="row-<?php echo (int) $ti_r + 1; ?> ti-bar-row" style="--ti-row-w:<?php echo esc_attr( round( $ti_w, 4 ) ); ?>%">
								<?php foreach ( $ti_row as $ti_b ) : ?>
									<div class="progress-bar <?php echo esc_attr( $ti_b['colore'][1] ); ?>">
										<div class="bar-text"><?php echo esc_html( ti_percent( $ti_b['pct'] ) ); ?></div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $ti_items ) : ?>
					<div data-reveal-group="" class="grid-loghi">
						<?php foreach ( $ti_items as $ti_n => $ti_it ) : ?>
							<?php
							$ti_cls  = $ti_pos_class[ ( $ti_n + 1 ) % 6 ];
							$ti_btn  = $ti_it['colore'][2];
							$ti_url  = $ti_it['sito'];
							?>
							<div class="card-logo<?php echo $ti_cls ? ' ' . esc_attr( $ti_cls ) : ''; ?>">
								<div class="card-logo_testata">
									<?php echo ti_img( $ti_it['logo'], array( 'class' => 'logo-card-loghi', 'alt' => get_the_title( $ti_it['post'] ), 'sizes' => '(max-width: 767px) 60vw, 20vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<a href="<?php echo esc_url( $ti_url ? $ti_url : '#' ); ?>"<?php echo $ti_url ? ' target="_blank" rel="noopener"' : ''; ?> class="button-card<?php echo $ti_btn ? ' ' . esc_attr( $ti_btn ) : ''; ?> w-inline-block" aria-label="<?php echo esc_attr( get_the_title( $ti_it['post'] ) ); ?>">
										<div class="w-embed"><?php echo ti_svg( 'arrow-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									</a>
								</div>
								<div>
									<?php
									if ( $ti_url ) {
										ti_button( $ti_url, $ti_link_text, 'is-small', true );
									}
									?>
								</div>
							</div>
						<?php endforeach; ?>
						<div class="absolute-line-elements">
							<div class="line-1"></div>
							<div class="line-2"></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
