<?php
/**
 * Caricamento SVG (molti loghi del portfolio sono SVG).
 *
 * Consentito solo a chi può caricare file; ogni SVG viene ripulito da script,
 * gestori di eventi e link javascript prima di essere salvato.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'upload_mimes',
	static function ( $mimes ) {
		if ( current_user_can( 'upload_files' ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}
		return $mimes;
	}
);

/* WordPress non riconosce sempre il tipo degli SVG: lo forziamo in base all'estensione. */
add_filter(
	'wp_check_filetype_and_ext',
	static function ( $data, $file, $filename ) {
		if ( 'svg' === strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) ) && current_user_can( 'upload_files' ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	},
	10,
	3
);

/**
 * Ripulisce un file SVG sul posto.
 *
 * @param string $path Percorso del file.
 * @return bool False se il file non è un SVG valido.
 */
function ti_sanitize_svg_file( $path ) {
	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $svg || false === stripos( $svg, '<svg' ) ) {
		return false;
	}

	$prev = libxml_use_internal_errors( true );
	$dom  = new DOMDocument();
	$ok   = $dom->loadXML( $svg, LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );
	if ( ! $ok ) {
		return false;
	}

	$xpath = new DOMXPath( $dom );

	foreach ( iterator_to_array( $xpath->query( '//*[local-name()="script" or local-name()="foreignObject" or local-name()="iframe" or local-name()="embed" or local-name()="object"]' ) ) as $node ) {
		$node->parentNode->removeChild( $node );
	}

	foreach ( iterator_to_array( $xpath->query( '//@*' ) ) as $attr ) {
		$name  = strtolower( $attr->nodeName );
		$value = strtolower( preg_replace( '/\s+/', '', (string) $attr->nodeValue ) );
		if ( str_starts_with( $name, 'on' ) || ( ( 'href' === $name || 'xlink:href' === $name ) && ( str_starts_with( $value, 'javascript:' ) || str_starts_with( $value, 'data:text' ) ) ) ) {
			$attr->ownerElement->removeAttributeNode( $attr );
		}
	}

	file_put_contents( $path, $dom->saveXML( $dom->documentElement ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	return true;
}

add_filter(
	'wp_handle_upload_prefilter',
	static function ( $file ) {
		if ( isset( $file['name'], $file['tmp_name'] ) && 'svg' === strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			if ( ! ti_sanitize_svg_file( $file['tmp_name'] ) ) {
				$file['error'] = 'Il file SVG non è valido.';
			}
		}
		return $file;
	}
);

add_filter(
	'wp_handle_sideload_prefilter',
	static function ( $file ) {
		if ( isset( $file['name'], $file['tmp_name'] ) && 'svg' === strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
			if ( ! ti_sanitize_svg_file( $file['tmp_name'] ) ) {
				$file['error'] = 'Il file SVG non è valido.';
			}
		}
		return $file;
	}
);

/* Anteprima corretta degli SVG nella libreria media. */
add_filter(
	'wp_prepare_attachment_for_js',
	static function ( $response, $attachment ) {
		if ( 'image/svg+xml' === $response['mime'] ) {
			$url               = wp_get_attachment_url( $attachment->ID );
			$response['image'] = array( 'src' => $url );
			$response['sizes'] = array(
				'full'   => array( 'url' => $url, 'width' => 300, 'height' => 300, 'orientation' => 'landscape' ),
				'medium' => array( 'url' => $url, 'width' => 300, 'height' => 300, 'orientation' => 'landscape' ),
			);
		}
		return $response;
	},
	10,
	2
);
