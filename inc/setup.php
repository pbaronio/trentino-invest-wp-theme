<?php
/**
 * Impostazioni generali del tema.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'trentino-invest', TI_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'style', 'script', 'gallery', 'caption' ) );
		register_nav_menus(
			array(
				'principale' => 'Menu principale',
			)
		);
	}
);

/* Il titolo della home è solo il nome del sito (come su Webflow). */
add_filter(
	'document_title_parts',
	static function ( $parts ) {
		if ( is_front_page() ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
);

/* Niente emoji script di WordPress: il sito non li usa. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Favicon, meta description e Open Graph.
 */
add_action(
	'wp_head',
	static function () {
		if ( ! has_site_icon() ) {
			$img = TI_URI . '/assets/images/';
			?>
<link href="<?php echo esc_url( $img . 'favicon.png' ); ?>" rel="icon" type="image/png" sizes="32x32" media="(prefers-color-scheme: light)">
<link href="<?php echo esc_url( $img . 'favicon-dark.png' ); ?>" rel="icon" type="image/png" sizes="32x32" media="(prefers-color-scheme: dark)">
<link href="<?php echo esc_url( $img . 'favicon-48.png' ); ?>" rel="icon" type="image/png" sizes="48x48">
<link href="<?php echo esc_url( $img . 'webclip-180.png' ); ?>" rel="apple-touch-icon" sizes="180x180">
<link href="<?php echo esc_url( $img . 'webclip-192.png' ); ?>" rel="icon" type="image/png" sizes="192x192">
<link href="<?php echo esc_url( $img . 'webclip.png' ); ?>" rel="icon" type="image/png" sizes="512x512">
			<?php
		}

		// Se è attivo un plugin SEO lasciamo a lui meta description e Open Graph.
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'All_in_One_SEO_Pack' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$title = wp_get_document_title();
		$desc  = is_front_page() || ! is_singular() ? ti_get( 'seo_descrizione', 'option' ) : get_the_excerpt();
		$image = (int) ti_get( 'seo_immagine', 'option', 0 );
		?>
<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( (string) $desc ) ); ?>">
<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( (string) $desc ) ); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_url( is_singular() ? get_permalink() : home_url( '/' ) ); ?>">
<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
<meta name="twitter:card" content="summary_large_image">
		<?php
		if ( $image && wp_get_attachment_url( $image ) ) {
			?>
<meta property="og:image" content="<?php echo esc_url( wp_get_attachment_url( $image ) ); ?>">
			<?php
		}
	},
	5
);

/**
 * Classe sul body per le pagine interne (navbar sempre leggibile su fondo bianco).
 */
add_filter(
	'body_class',
	static function ( $classes ) {
		if ( ! is_front_page() ) {
			$classes[] = 'is-inner';
		}
		return $classes;
	}
);
