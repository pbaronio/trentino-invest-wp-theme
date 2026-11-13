<?php
/**
 * CSS e JS del sito: tutto locale, nessuna dipendenza da Netlify o CDN Webflow.
 * L'unica risorsa esterna è il kit Adobe Fonts (Typekit), legato alla licenza dei font.
 *
 * L'ordine di caricamento riproduce quello dell'export Webflow.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

const TI_TYPEKIT_ID = 'hua0oug';

/**
 * Versione di un file del tema = data di modifica: il browser scarica sempre l'ultima versione.
 *
 * @param string $rel Percorso relativo alla cartella del tema.
 * @return string
 */
function ti_ver( $rel ) {
	$file = TI_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $file ) ? TI_VERSION . '.' . filemtime( $file ) : TI_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		$css = TI_URI . '/assets/css/';
		$js  = TI_URI . '/assets/js/';
		$ven = TI_URI . '/assets/vendor/';

		/* ---------- CSS ---------- */
		wp_enqueue_style( 'ti-normalize', $css . 'normalize.css', array(), ti_ver( 'assets/css/normalize.css' ) );
		wp_enqueue_style( 'ti-webflow', $css . 'webflow.css', array( 'ti-normalize' ), ti_ver( 'assets/css/webflow.css' ) );
		wp_enqueue_style( 'ti-site', $css . 'trentino-invest.webflow.css', array( 'ti-webflow' ), ti_ver( 'assets/css/trentino-invest.webflow.css' ) );
		wp_enqueue_style( 'ti-lenis', $ven . 'lenis.css', array( 'ti-site' ), ti_ver( 'assets/vendor/lenis.css' ) );
		wp_enqueue_style( 'ti-swiper', $ven . 'swiper-bundle.min.css', array( 'ti-site' ), ti_ver( 'assets/vendor/swiper-bundle.min.css' ) );
		wp_enqueue_style( 'ti-typekit', 'https://use.typekit.net/' . TI_TYPEKIT_ID . '.css', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

		$custom = array( 'loader', 'fit-text', 'accordion', 'slider-team', 'gsap-slider', 'back-to-top', 'card-investimenti' );
		foreach ( $custom as $name ) {
			wp_enqueue_style( 'ti-' . $name, $css . 'custom/' . $name . '.css', array( 'ti-site' ), ti_ver( 'assets/css/custom/' . $name . '.css' ) );
		}
		wp_enqueue_style( 'ti-embeds', $css . 'embeds.css', array( 'ti-site' ), ti_ver( 'assets/css/embeds.css' ) );
		wp_enqueue_style( 'ti-theme', $css . 'theme.css', array( 'ti-embeds' ), ti_ver( 'assets/css/theme.css' ) );

		/* ---------- JS ---------- */
		$footer = array( 'in_footer' => true );

		// jQuery 3.5.1, la stessa versione usata da Webflow (sostituisce quella di WordPress solo sul sito).
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', $ven . 'jquery-3.5.1.min.js', array(), ti_ver( 'assets/vendor/jquery-3.5.1.min.js' ), $footer );

		wp_enqueue_script( 'ti-webflow', $js . 'webflow.js', array( 'jquery' ), ti_ver( 'assets/js/webflow.js' ), $footer );

		$gsap = array( 'gsap', 'Flip', 'ScrollTrigger', 'SplitText', 'CustomEase', 'Draggable', 'InertiaPlugin' );
		$prev = 'ti-webflow';
		foreach ( $gsap as $lib ) {
			$handle = 'ti-gsap-' . strtolower( $lib );
			wp_enqueue_script( $handle, $ven . $lib . '.min.js', array( $prev ), ti_ver( 'assets/vendor/' . $lib . '.min.js' ), $footer );
			$prev = $handle;
		}
		wp_add_inline_script( $prev, 'gsap.registerPlugin(Flip,ScrollTrigger,SplitText);' );

		wp_enqueue_script( 'ti-lenis', $ven . 'lenis.min.js', array( $prev ), ti_ver( 'assets/vendor/lenis.min.js' ), $footer );
		wp_enqueue_script( 'ti-swiper', $ven . 'swiper-bundle.min.js', array( 'ti-lenis' ), ti_ver( 'assets/vendor/swiper-bundle.min.js' ), $footer );

		$scripts = array( 'loader', 'fit-text', 'lenis', 'reveal-group', 'text-animation-reveal', 'accordion', 'slider-team', 'gsap-slider', 'back-to-top', 'card-investimenti', 'menu-button' );
		if ( ! is_front_page() ) {
			// L'animazione di apertura esiste solo in home; altrove loader.js andrebbe in errore con Lenis.
			$scripts = array_diff( $scripts, array( 'loader' ) );
		}
		$prev = 'ti-swiper';
		foreach ( $scripts as $name ) {
			$handle = 'ti-' . $name . '-js';
			wp_enqueue_script( $handle, $js . 'custom/' . $name . '.js', array( $prev ), ti_ver( 'assets/js/custom/' . $name . '.js' ), $footer );
			$prev = $handle;
		}

		/* La home non usa i blocchi di Gutenberg: niente CSS dei blocchi. */
		if ( is_front_page() ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'classic-theme-styles' );
			wp_dequeue_style( 'global-styles' );
		}
	},
	20
);

/**
 * Script di Typekit e classe w-mod-js nel <head>, come nell'export Webflow.
 */
add_action(
	'wp_head',
	static function () {
		?>
<script src="https://use.typekit.net/<?php echo esc_attr( TI_TYPEKIT_ID ); ?>.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
<script>!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
		<?php
	},
	1
);
