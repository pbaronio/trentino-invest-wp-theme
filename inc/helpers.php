<?php
/**
 * Funzioni di supporto per i template.
 *
 * I valori vengono letti direttamente dai meta nel formato di Secure Custom Fields,
 * così il sito resta visibile anche se il plugin viene disattivato per errore
 * (serve solo per modificare i contenuti).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID della pagina impostata come home.
 *
 * @return int
 */
function ti_front_id() {
	return (int) get_option( 'page_on_front' );
}

/**
 * Legge un campo. Se il campo non è mai stato salvato restituisce il contenuto iniziale.
 *
 * @param string     $name    Nome del campo.
 * @param int|string $post_id ID del post oppure 'option'. Default: home.
 * @param mixed      $default Valore di riserva. Se null usa ti_defaults().
 * @return mixed
 */
function ti_get( $name, $post_id = null, $default = null ) {
	if ( null === $post_id ) {
		$post_id = ti_front_id();
	}

	if ( 'option' === $post_id ) {
		$sentinel = '__ti_missing__';
		$value    = get_option( 'options_' . $name, $sentinel );
		if ( $sentinel === $value ) {
			return null !== $default ? $default : ti_default( 'options', $name );
		}
		return $value;
	}

	$post_id = (int) $post_id;
	if ( ! $post_id || ! metadata_exists( 'post', $post_id, $name ) ) {
		return null !== $default ? $default : ti_default( 'home', $name );
	}
	return get_post_meta( $post_id, $name, true );
}

/**
 * Legge un campo ripetitore (repeater) come array di righe.
 *
 * @param string   $name    Nome del repeater.
 * @param string[] $subs    Nomi dei sotto-campi.
 * @param int|null $post_id ID del post. Default: home.
 * @return array
 */
function ti_rows( $name, array $subs, $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = ti_front_id();
	}
	if ( ! $post_id || ! metadata_exists( 'post', $post_id, $name ) ) {
		$d = ti_defaults();
		return $d['home_rows'][ $name ] ?? array();
	}
	$count = (int) get_post_meta( $post_id, $name, true );
	$rows  = array();
	for ( $i = 0; $i < $count; $i++ ) {
		$row = array();
		foreach ( $subs as $sub ) {
			$row[ $sub ] = get_post_meta( $post_id, "{$name}_{$i}_{$sub}", true );
		}
		$rows[] = $row;
	}
	return $rows;
}

/**
 * Testo semplice con gli a capo trasformati in <br>.
 *
 * @param string $text Testo.
 * @return string HTML sicuro.
 */
function ti_br( $text ) {
	$lines = preg_split( '/\R/u', trim( (string) $text ) );
	$lines = array_map(
		static function ( $l ) {
			return esc_html( trim( $l ) );
		},
		$lines
	);
	return implode( '<br>', $lines );
}

/**
 * Tag <img> per un allegato, senza width/height (come in Webflow) e con srcset.
 *
 * @param int   $id   ID allegato.
 * @param array $args class, sizes, loading, alt, attrs (array extra), srcset (bool).
 * @return string
 */
function ti_img( $id, array $args = array() ) {
	$id = (int) $id;
	if ( ! $id ) {
		return '';
	}
	$url = wp_get_attachment_url( $id );
	if ( ! $url ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class'   => '',
			'sizes'   => '100vw',
			'loading' => 'lazy',
			'alt'     => null,
			'attrs'   => array(),
			'srcset'  => true,
		)
	);

	$alt = null !== $args['alt'] ? $args['alt'] : get_post_meta( $id, '_wp_attachment_image_alt', true );

	$attrs = array(
		'src'     => $url,
		'loading' => $args['loading'],
		'alt'     => (string) $alt,
	);

	if ( $args['srcset'] && wp_attachment_is_image( $id ) && 'image/svg+xml' !== get_post_mime_type( $id ) ) {
		$full = wp_get_attachment_image_src( $id, 'full' );
		if ( $full ) {
			$attrs['src'] = $full[0];
			$srcset       = wp_get_attachment_image_srcset( $id, 'full' );
			if ( $srcset ) {
				$attrs['sizes']  = $args['sizes'];
				$attrs['srcset'] = $srcset;
			}
		}
	}

	if ( $args['class'] ) {
		$attrs['class'] = $args['class'];
	}
	$attrs = array_merge( $attrs, $args['attrs'] );

	$html = '<img';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . $k . '="' . ( 'src' === $k ? esc_url( $v ) : esc_attr( $v ) ) . '"';
	}
	return $html . '>';
}

/**
 * Contenuto di un'icona SVG del tema (assets/svg/{name}.svg).
 *
 * @param string $name Nome file senza estensione.
 * @return string
 */
function ti_svg( $name ) {
	static $cache = array();
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = TI_DIR . '/assets/svg/' . sanitize_file_name( $name ) . '.svg';
		$cache[ $name ] = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
	return $cache[ $name ];
}

/**
 * Etichetta di sezione (icona + testo maiuscolo).
 *
 * @param string $icon  Nome icona in assets/svg.
 * @param string $label Testo.
 */
function ti_label( $icon, $label ) {
	?>
	<div class="label">
		<div class="w-embed"><?php echo ti_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="capital-m"><?php echo esc_html( $label ); ?></div>
	</div>
	<?php
}

/**
 * Bottone animato "button-020" di Webflow.
 *
 * @param string $url   Link.
 * @param string $text  Testo.
 * @param string $class Classi aggiuntive.
 * @param bool   $blank Apri in nuova scheda.
 */
function ti_button( $url, $text, $class = '', $blank = false ) {
	if ( '' === trim( (string) $text ) ) {
		return;
	}
	printf(
		'<a data-button-020="" href="%1$s"%2$s class="button-020%3$s w-inline-block"><span class="button-020__inner"><span class="button-020__default"><span class="button-020__default-bg"></span><span class="button-020__default-text text-s">%4$s</span></span><span aria-hidden="true" class="button-020__hover"><span class="button-020__hover-bg"></span><span class="button-020__hover-text text-s">%4$s</span></span></span></a>',
		esc_url( $url ? $url : '#' ),
		$blank ? ' target="_blank" rel="noopener"' : '',
		$class ? ' ' . esc_attr( $class ) : '',
		esc_html( $text )
	);
}

/**
 * Link del menu: le ancore (#sezione) puntano alla home quando si è su un'altra pagina.
 *
 * @param string $url URL.
 * @return string
 */
function ti_anchor_url( $url ) {
	if ( is_string( $url ) && str_starts_with( $url, '#' ) && ! is_front_page() ) {
		return home_url( '/' ) . $url;
	}
	return $url;
}

/**
 * True se l'URL punta a un altro sito.
 *
 * @param string $url URL.
 * @return bool
 */
function ti_is_external( $url ) {
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );
	return $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host;
}

/**
 * Formatta una percentuale all'italiana (12,5%).
 *
 * @param float $n Numero.
 * @return string
 */
function ti_percent( $n ) {
	$n = (float) $n;
	$s = ( floor( $n ) == $n ) ? (string) (int) $n : rtrim( rtrim( number_format( $n, 2, ',', '' ), '0' ), ',' ); // phpcs:ignore Universal.Operators.StrictComparisons
	return $s . '%';
}

/**
 * Colori disponibili per i soci: chiave => [etichetta, classe barra, classe bottone].
 *
 * @return array
 */
function ti_colori_soci() {
	return array(
		'orange'       => array( 'Arancio', 'is-orange', '' ),
		'pistacchio'   => array( 'Pistacchio', 'is-pistacchio', 'is-pistacchio' ),
		'blu'          => array( 'Blu', 'is-blu', 'is-blu' ),
		'giallo'       => array( 'Giallo', 'is-yellow', 'is-yellow' ),
		'verde'        => array( 'Verde', 'is-green', 'is-verde' ),
		'verde-chiaro' => array( 'Verde chiaro', 'is-lightgreen', 'is-verdechiaro' ),
	);
}

/**
 * Elenco post di un CPT ordinati come in bacheca (campo "Ordine").
 *
 * @param string $type Post type.
 * @param array  $args Argomenti extra per get_posts.
 * @return WP_Post[]
 */
function ti_posts( $type, array $args = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'        => $type,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'orderby'          => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'suppress_filters' => false,
			),
			$args
		)
	);
}
