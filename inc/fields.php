<?php
/**
 * Campi personalizzati (Secure Custom Fields) registrati via codice.
 *
 * Sono definiti qui e non nel database, così restano versionati con il tema
 * e il cliente non può romperli per errore da "Campi personalizzati".
 *
 * Convenzione chiavi: field_ti_{nome} — per i sotto-campi field_ti_{repeater}_{nome}.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Costruisce un campo.
 *
 * @param string $name   Nome.
 * @param string $label  Etichetta.
 * @param string $type   Tipo SCF.
 * @param array  $extra  Impostazioni extra.
 * @param string $prefix Prefisso chiave (per i sotto-campi).
 * @return array
 */
function ti_f( $name, $label, $type = 'text', array $extra = array(), $prefix = '' ) {
	return array_merge(
		array(
			'key'   => 'field_ti_' . ( $prefix ? $prefix . '_' : '' ) . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/**
 * Tab.
 *
 * @param string $id    ID.
 * @param string $label Etichetta.
 * @return array
 */
function ti_tab( $id, $label ) {
	return array(
		'key'       => 'field_ti_tab_' . $id,
		'label'     => $label,
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'top',
	);
}

/**
 * Messaggio informativo.
 *
 * @param string $id   ID.
 * @param string $html Testo (HTML).
 * @return array
 */
function ti_msg( $id, $html ) {
	return array(
		'key'       => 'field_ti_msg_' . $id,
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => $html,
		'new_lines' => '',
		'esc_html'  => 0,
	);
}

/**
 * Campo immagine (restituisce l'ID).
 *
 * @param string $name  Nome.
 * @param string $label Etichetta.
 * @param array  $extra Extra.
 * @return array
 */
function ti_image( $name, $label, array $extra = array() ) {
	return ti_f(
		$name,
		$label,
		'image',
		array_merge(
			array(
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			$extra
		)
	);
}

/**
 * Repeater titolo + testo (Approccio, Criteri).
 *
 * @param string $name   Nome.
 * @param string $label  Etichetta.
 * @param string $button Testo del bottone "aggiungi".
 * @return array
 */
function ti_repeater_items( $name, $label, $button ) {
	return ti_f(
		$name,
		$label,
		'repeater',
		array(
			'layout'       => 'block',
			'button_label' => $button,
			'min'          => 1,
			'sub_fields'   => array(
				ti_f( 'titolo', 'Titolo', 'text', array( 'required' => 1 ), $name ),
				ti_f( 'testo', 'Testo', 'textarea', array( 'rows' => 3, 'new_lines' => '' ), $name ),
			),
		)
	);
}

add_action(
	'acf/init',
	static function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$admin = admin_url();
		$link  = static function ( $path, $text ) use ( $admin ) {
			return '<a href="' . esc_url( $admin . $path ) . '">' . esc_html( $text ) . '</a>';
		};
		$br_help = 'Vai a capo per spezzare la riga come nel design.';

		/* ------------------------------------------------------------------
		 * Pagina opzioni.
		 * ------------------------------------------------------------------ */
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Impostazioni sito',
					'menu_title' => 'Impostazioni sito',
					'menu_slug'  => 'ti-impostazioni',
					'capability' => 'edit_pages',
					'icon_url'   => 'dashicons-admin-generic',
					'position'   => 20,
					'redirect'   => false,
				)
			);
		}

		/* ------------------------------------------------------------------
		 * Home.
		 * ------------------------------------------------------------------ */
		acf_add_local_field_group(
			array(
				'key'                   => 'group_ti_home',
				'title'                 => 'Contenuti della home',
				'location'              => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
				'position'              => 'acf_after_title',
				'style'                 => 'seamless',
				'hide_on_screen'        => array( 'the_content', 'excerpt', 'discussion', 'comments', 'featured_image', 'format', 'categories', 'tags', 'send-trackbacks' ),
				'fields'                => array(
					ti_tab( 'hero', 'Apertura' ),
					ti_f( 'hero_titolo', 'Titolo grande', 'text', array( 'instructions' => 'Il testo si adatta da solo alla larghezza dello schermo.' ) ),
					ti_image( 'hero_immagine', 'Immagine di sfondo', array( 'instructions' => 'Immagine a tutto schermo. Consigliata almeno 2400 px di larghezza.' ) ),
					ti_f(
						'hero_loader',
						'Immagini dell’animazione iniziale',
						'gallery',
						array(
							'instructions'  => 'Esattamente 4 immagini: le prime 2 scorrono a sinistra dell’immagine di sfondo, le altre 2 a destra. L’animazione si vede alla prima visita (poi ogni 7 giorni).',
							'return_format' => 'id',
							'preview_size'  => 'medium',
							'min'           => 4,
							'max'           => 4,
							'library'       => 'all',
						)
					),

					ti_tab( 'intro', 'Intro' ),
					ti_f( 'intro_titolo', 'Titolo', 'textarea', array( 'rows' => 2, 'new_lines' => '', 'instructions' => $br_help ) ),
					ti_f( 'intro_testo', 'Testo', 'textarea', array( 'rows' => 3, 'new_lines' => '' ) ),
					ti_f( 'intro_cta_testo', 'Testo del bottone', 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
					ti_f( 'intro_cta_link', 'Link del bottone', 'url', array( 'wrapper' => array( 'width' => 60 ) ) ),
					ti_image( 'intro_immagine_1', 'Immagine sinistra', array( 'wrapper' => array( 'width' => 50 ) ) ),
					ti_image( 'intro_immagine_2', 'Immagine destra', array( 'wrapper' => array( 'width' => 50 ) ) ),

					ti_tab( 'chisiamo', 'Chi siamo' ),
					ti_msg( 'chisiamo', 'Loghi, link e barre delle quote si gestiscono da ' . $link( 'edit.php?post_type=ti_socio', 'Soci' ) . '.' ),
					ti_f( 'chisiamo_etichetta', 'Etichetta', 'text' ),
					ti_f( 'chisiamo_titolo', 'Titolo', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => $br_help ) ),
					ti_f( 'chisiamo_link_testo', 'Testo del link sotto ogni logo', 'text' ),

					ti_tab( 'approccio', 'Il nostro approccio' ),
					ti_f( 'approccio_etichetta', 'Etichetta', 'text' ),
					ti_f( 'approccio_titolo', 'Titolo', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => $br_help ) ),
					ti_repeater_items( 'approccio_items', 'Punti', 'Aggiungi punto' ),

					ti_tab( 'criteri', 'I nostri criteri' ),
					ti_f( 'criteri_etichetta', 'Etichetta', 'text' ),
					ti_f( 'criteri_titolo', 'Titolo', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => $br_help ) ),
					ti_repeater_items( 'criteri_items', 'Criteri (fisarmonica)', 'Aggiungi criterio' ),

					ti_tab( 'team', 'Team' ),
					ti_msg( 'team', 'Le persone si gestiscono da ' . $link( 'edit.php?post_type=ti_membro', 'Team' ) . ', i gruppi (Consiglio di Amministrazione, ecc.) da ' . $link( 'edit-tags.php?taxonomy=ti_gruppo&post_type=ti_membro', 'Team → Gruppi' ) . '.' ),
					ti_f( 'team_etichetta', 'Etichetta', 'text' ),
					ti_f( 'team_titolo', 'Titolo', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => $br_help ) ),

					ti_tab( 'portfolio', 'Portfolio' ),
					ti_msg( 'portfolio', 'Le aziende si gestiscono da ' . $link( 'edit.php?post_type=ti_investimento', 'Portfolio' ) . '.' ),
					ti_f( 'portfolio_etichetta', 'Etichetta', 'text' ),
					ti_f( 'portfolio_titolo', 'Titolo a sinistra dello slider', 'textarea', array( 'rows' => 2, 'new_lines' => '', 'instructions' => $br_help ) ),

					ti_tab( 'contatti', 'Contatti e footer' ),
					ti_msg( 'contatti', 'Contatti, footer e SEO sono in ' . $link( 'admin.php?page=ti-impostazioni', 'Impostazioni sito' ) . ', perché compaiono anche nelle altre pagine.' ),
				),
			)
		);

		/* ------------------------------------------------------------------
		 * Impostazioni sito.
		 * ------------------------------------------------------------------ */
		$page_field = static function ( $name, $label ) {
			return ti_f(
				$name,
				$label,
				'post_object',
				array(
					'post_type'     => array( 'page' ),
					'return_format' => 'id',
					'allow_null'    => 1,
					'ui'            => 1,
					'wrapper'       => array( 'width' => 33 ),
				)
			);
		};

		acf_add_local_field_group(
			array(
				'key'      => 'group_ti_options',
				'title'    => 'Impostazioni sito',
				'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'ti-impostazioni' ) ) ),
				'style'    => 'seamless',
				'fields'   => array(
					ti_tab( 'opt_contatti', 'Contatti' ),
					ti_f( 'contatti_etichetta', 'Etichetta', 'text' ),
					ti_f( 'contatti_titolo', 'Titolo', 'text' ),
					ti_f( 'contatti_cta_testo', 'Testo del bottone email', 'text' ),
					ti_f( 'contatti_email', 'Email', 'email' ),
					ti_f( 'contatti_indirizzo', 'Indirizzo', 'text' ),

					ti_tab( 'opt_footer', 'Footer' ),
					ti_f( 'footer_copyright', 'Copyright', 'text', array( 'instructions' => 'Scrivi {anno} per inserire l’anno corrente in automatico.' ) ),
					ti_msg( 'legali', 'I link alle pagine legali compaiono nel footer solo quando la pagina scelta è <strong>pubblicata</strong>.' ),
					$page_field( 'footer_pagina_cookie', 'Pagina Cookie Policy' ),
					$page_field( 'footer_pagina_privacy', 'Pagina Privacy Policy' ),
					$page_field( 'footer_pagina_etico', 'Pagina Ethic Code' ),
					ti_f( 'footer_credits_testo', 'Testo Credits', 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
					ti_f( 'footer_credits_link', 'Link Credits', 'url', array( 'wrapper' => array( 'width' => 60 ) ) ),

					ti_tab( 'opt_seo', 'SEO e condivisione' ),
					ti_f( 'seo_descrizione', 'Descrizione del sito', 'textarea', array( 'rows' => 2, 'new_lines' => '', 'instructions' => 'Usata nei risultati di Google e quando il link viene condiviso. Circa 150 caratteri.' ) ),
					ti_image( 'seo_immagine', 'Immagine di condivisione', array( 'instructions' => 'Immagine mostrata quando il sito viene condiviso (1200×630 px).' ) ),
				),
			)
		);

		/* ------------------------------------------------------------------
		 * Socio.
		 * ------------------------------------------------------------------ */
		$colori = array();
		foreach ( ti_colori_soci() as $k => $c ) {
			$colori[ $k ] = $c[0];
		}
		acf_add_local_field_group(
			array(
				'key'      => 'group_ti_socio',
				'title'    => 'Dati del socio',
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'ti_socio' ) ) ),
				'position' => 'acf_after_title',
				'fields'   => array(
					ti_msg( 'socio', 'Il titolo serve solo in bacheca e come testo alternativo del logo. L’ordine si imposta nel box <strong>Attributi → Ordine</strong> (0 = primo).' ),
					ti_image( 'logo', 'Logo', array( 'required' => 1, 'wrapper' => array( 'width' => 50 ) ) ),
					ti_f( 'sito', 'Sito web', 'url', array( 'wrapper' => array( 'width' => 50 ) ) ),
					ti_f(
						'percentuale',
						'Quota (%)',
						'number',
						array(
							'instructions' => 'Larghezza della barra. Le barre consecutive con la stessa quota vengono raggruppate su una riga in mobile.',
							'min'          => 0,
							'max'          => 100,
							'step'         => 0.01,
							'append'       => '%',
							'wrapper'      => array( 'width' => 50 ),
						)
					),
					ti_f(
						'colore',
						'Colore',
						'select',
						array(
							'choices'       => $colori,
							'default_value' => 'orange',
							'wrapper'       => array( 'width' => 50 ),
						)
					),
				),
			)
		);

		/* ------------------------------------------------------------------
		 * Persona del team.
		 * ------------------------------------------------------------------ */
		acf_add_local_field_group(
			array(
				'key'      => 'group_ti_membro',
				'title'    => 'Dati della persona',
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'ti_membro' ) ) ),
				'position' => 'acf_after_title',
				'fields'   => array(
					ti_msg( 'membro', 'Scrivi nome e cognome nel titolo e scegli il <strong>Gruppo</strong> nel box a destra. Una persona presente in due gruppi va inserita due volte (ruolo e foto possono essere diversi). L’ordine si imposta in <strong>Attributi → Ordine</strong>.' ),
					ti_image( 'foto', 'Foto', array( 'required' => 1, 'instructions' => 'Ritratto verticale.', 'wrapper' => array( 'width' => 40 ) ) ),
					ti_f( 'ruolo', 'Ruolo', 'text', array( 'instructions' => 'Es. Presidente. Lascia vuoto se non serve.', 'wrapper' => array( 'width' => 60 ) ) ),
					ti_f( 'linkedin', 'Profilo LinkedIn', 'url', array( 'wrapper' => array( 'width' => 60 ) ) ),
					ti_f( 'foto_in_alto', 'Allinea la foto in alto', 'true_false', array( 'ui' => 1, 'instructions' => 'Utile se il ritaglio taglia la testa.', 'wrapper' => array( 'width' => 50 ) ) ),
					ti_f( 'icona_chiara', 'Icona LinkedIn bianca', 'true_false', array( 'ui' => 1, 'instructions' => 'Per foto con lo sfondo scuro in alto a destra.', 'wrapper' => array( 'width' => 50 ) ) ),
				),
			)
		);

		/* ------------------------------------------------------------------
		 * Investimento.
		 * ------------------------------------------------------------------ */
		acf_add_local_field_group(
			array(
				'key'      => 'group_ti_investimento',
				'title'    => 'Dati dell’investimento',
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'ti_investimento' ) ) ),
				'position' => 'acf_after_title',
				'fields'   => array(
					ti_msg( 'investimento', 'Scrivi la ragione sociale nel titolo. L’ordine nello slider si imposta in <strong>Attributi → Ordine</strong>. Per togliere un’azienda dal sito spostala in bozza.' ),
					ti_image( 'logo', 'Logo', array( 'required' => 1, 'instructions' => 'Meglio SVG o PNG con sfondo trasparente.', 'wrapper' => array( 'width' => 40 ) ) ),
					ti_f( 'sito', 'Sito web', 'url', array( 'wrapper' => array( 'width' => 60 ) ) ),
					ti_f( 'descrizione', 'Descrizione', 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => 'Compare cliccando sul “+” della card.' ) ),
				),
			)
		);

		/* ------------------------------------------------------------------
		 * Gruppo del team.
		 * ------------------------------------------------------------------ */
		acf_add_local_field_group(
			array(
				'key'      => 'group_ti_gruppo',
				'title'    => 'Gruppo',
				'location' => array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'ti_gruppo' ) ) ),
				'fields'   => array(
					ti_f( 'titolo', 'Titolo mostrato sul sito', 'textarea', array( 'rows' => 2, 'new_lines' => '', 'instructions' => 'Se vuoto si usa il nome. ' . $br_help ) ),
					ti_f( 'ordine', 'Ordine', 'number', array( 'default_value' => 0, 'instructions' => 'I gruppi vengono mostrati dal numero più basso al più alto.' ) ),
				),
			)
		);
	}
);
