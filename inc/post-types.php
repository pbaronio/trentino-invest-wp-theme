<?php
/**
 * Custom post type: Soci, Team, Investimenti. Tassonomia: Gruppi del team.
 *
 * Non hanno pagine singole: servono solo ad alimentare le sezioni della home.
 * L'ordine di visualizzazione si imposta con il campo "Ordine" (Attributi).
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Etichette italiane per un post type.
 *
 * @param string $plural   Plurale.
 * @param string $singular Singolare.
 * @param bool   $female   Sostantivo femminile.
 * @return array
 */
function ti_cpt_labels( $plural, $singular, $female = false ) {
	$new  = $female ? 'Nuova' : 'Nuovo';
	$all  = $female ? 'Tutte' : 'Tutti';
	$none = $female ? 'Nessuna' : 'Nessun';
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'add_new'            => 'Aggiungi',
		'add_new_item'       => "Aggiungi {$singular}",
		'edit_item'          => "Modifica {$singular}",
		'new_item'           => "{$new} {$singular}",
		'view_item'          => "Vedi {$singular}",
		'search_items'       => "Cerca {$plural}",
		'not_found'          => "{$none} {$singular} trovato",
		'not_found_in_trash' => "{$none} {$singular} nel cestino",
		'all_items'          => "{$all} i {$plural}",
		'items_list'         => "Elenco {$plural}",
	);
}

add_action(
	'init',
	static function () {
		$common = array(
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'page-attributes' ),
		);

		register_post_type(
			'ti_socio',
			array_merge(
				$common,
				array(
					'labels'        => ti_cpt_labels( 'Soci', 'Socio' ),
					'description'   => 'Soci mostrati nella sezione "Chi siamo" con le barre delle quote.',
					'menu_icon'     => 'dashicons-groups',
					'menu_position' => 21,
				)
			)
		);

		register_post_type(
			'ti_membro',
			array_merge(
				$common,
				array(
					'labels'        => array_merge(
						ti_cpt_labels( 'Team', 'Persona', true ),
						array(
							'all_items' => 'Tutte le persone',
							'not_found' => 'Nessuna persona trovata',
						)
					),
					'description'   => 'Persone della sezione "Team", divise per gruppo.',
					'menu_icon'     => 'dashicons-businessperson',
					'menu_position' => 22,
				)
			)
		);

		register_post_type(
			'ti_investimento',
			array_merge(
				$common,
				array(
					'labels'        => array_merge(
						ti_cpt_labels( 'Investimenti', 'Investimento' ),
						array( 'menu_name' => 'Portfolio' )
					),
					'description'   => 'Aziende della sezione "Portfolio".',
					'menu_icon'     => 'dashicons-portfolio',
					'menu_position' => 23,
				)
			)
		);

		register_taxonomy(
			'ti_gruppo',
			'ti_membro',
			array(
				'labels'            => array(
					'name'          => 'Gruppi',
					'singular_name' => 'Gruppo',
					'menu_name'     => 'Gruppi',
					'all_items'     => 'Tutti i gruppi',
					'edit_item'     => 'Modifica gruppo',
					'add_new_item'  => 'Aggiungi gruppo',
					'search_items'  => 'Cerca gruppi',
					'not_found'     => 'Nessun gruppo trovato',
				),
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'hierarchical'      => true, // Checkbox nel box laterale: più comodo per il cliente.
				'rewrite'           => false,
				'query_var'         => false,
			)
		);
	}
);

/**
 * Gruppi del team in ordine (campo "Ordine" del gruppo).
 *
 * @return WP_Term[]
 */
function ti_gruppi() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'ti_gruppo',
			'hide_empty' => true,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		static function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, 'ordine', true );
			$ob = (int) get_term_meta( $b->term_id, 'ordine', true );
			return $oa === $ob ? strcmp( $a->name, $b->name ) : $oa <=> $ob;
		}
	);
	return $terms;
}
