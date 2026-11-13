<?php
/**
 * Bacheca: avvisi, colonne, piccoli aggiustamenti per il cliente.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Avvisi: plugin mancante e contenuti iniziali da importare.
 */
add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			$url = admin_url( 'plugin-install.php?s=secure-custom-fields&tab=search&type=term' );
			printf(
				'<div class="notice notice-error"><p><strong>Tema Trentino Invest:</strong> per modificare i contenuti serve il plugin gratuito <strong>Secure Custom Fields</strong>. <a href="%s">Installalo e attivalo</a>.</p></div>',
				esc_url( $url )
			);
		}

		$screen = get_current_screen();
		if ( ! get_option( 'ti_import_done' ) && $screen && 'appearance_page_ti-import' !== $screen->id ) {
			printf(
				'<div class="notice notice-warning"><p><strong>Tema Trentino Invest:</strong> i contenuti del sito non sono ancora stati importati. <a class="button button-primary" href="%s">Importa i contenuti</a></p></div>',
				esc_url( admin_url( 'themes.php?page=ti-import' ) )
			);
		}
	}
);

/* La home si modifica solo con i campi: niente editor a blocchi. */
add_filter(
	'use_block_editor_for_post',
	static function ( $use, $post ) {
		if ( $post && (int) $post->ID === ti_front_id() ) {
			return false;
		}
		return $use;
	},
	10,
	2
);
add_action(
	'admin_init',
	static function () {
		global $pagenow;
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) && (int) $_GET['post'] === ti_front_id() ) { // phpcs:ignore WordPress.Security.NonceVerification
			remove_post_type_support( 'page', 'editor' );
		}
	}
);

/* Articoli e commenti non servono a questo sito: li nascondiamo dal menu. */
add_action(
	'admin_menu',
	static function () {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
	},
	999
);

/* Liste dei CPT ordinate come sul sito. */
add_action(
	'pre_get_posts',
	static function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( in_array( $q->get( 'post_type' ), array( 'ti_socio', 'ti_membro', 'ti_investimento' ), true ) && ! isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$q->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				)
			);
		}
	}
);

/**
 * Colonne in bacheca: anteprima immagine, dati principali, ordine.
 */
$ti_columns = array(
	'ti_socio'        => array( 'logo', array( 'percentuale' => 'Quota' ) ),
	'ti_membro'       => array( 'foto', array( 'ruolo' => 'Ruolo' ) ),
	'ti_investimento' => array( 'logo', array( 'sito' => 'Sito' ) ),
);

foreach ( $ti_columns as $ti_type => $ti_conf ) {
	add_filter(
		"manage_{$ti_type}_posts_columns",
		static function ( $cols ) use ( $ti_conf ) {
			$new = array();
			foreach ( $cols as $k => $v ) {
				if ( 'title' === $k ) {
					$new['ti_thumb'] = '';
				}
				$new[ $k ] = $v;
				if ( 'title' === $k ) {
					foreach ( $ti_conf[1] as $field => $label ) {
						$new[ 'ti_' . $field ] = $label;
					}
				}
			}
			unset( $new['date'] );
			$new['ti_order'] = 'Ordine';
			return $new;
		}
	);

	add_action(
		"manage_{$ti_type}_posts_custom_column",
		static function ( $col, $post_id ) use ( $ti_conf ) {
			if ( 'ti_thumb' === $col ) {
				$id = (int) get_post_meta( $post_id, $ti_conf[0], true );
				if ( $id ) {
					echo ti_img( $id, array( 'srcset' => false, 'attrs' => array( 'style' => 'width:60px;height:60px;object-fit:contain;background:#f6f6f6;border-radius:4px' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				return;
			}
			if ( 'ti_order' === $col ) {
				echo (int) get_post_field( 'menu_order', $post_id );
				return;
			}
			$field = substr( $col, 3 );
			if ( isset( $ti_conf[1][ $field ] ) ) {
				$val = get_post_meta( $post_id, $field, true );
				if ( 'percentuale' === $field && '' !== $val ) {
					$val = ti_percent( $val );
				}
				if ( 'sito' === $field && $val ) {
					printf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', esc_url( $val ), esc_html( wp_parse_url( $val, PHP_URL_HOST ) ) );
					return;
				}
				echo esc_html( (string) $val );
			}
		},
		10,
		2
	);
}

add_action(
	'admin_head',
	static function () {
		echo '<style>.column-ti_thumb{width:72px}.column-ti_order{width:70px}</style>';
	}
);
