<?php
/**
 * Importazione dei contenuti iniziali (testi, immagini, soci, team, investimenti).
 *
 * Gira a piccoli passi via AJAX (una immagine per richiesta) per non superare
 * i limiti di tempo dell'hosting condiviso. Si può rilanciare senza rischi:
 * non duplica nulla e non sovrascrive i contenuti già modificati.
 *
 * Pagina: Aspetto → Importa contenuti.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Pagina in bacheca.
 * ------------------------------------------------------------------------ */

add_action(
	'admin_menu',
	static function () {
		add_theme_page( 'Importa contenuti', 'Importa contenuti', 'manage_options', 'ti-import', 'ti_import_page' );
	}
);

/* Dopo l'attivazione del tema porta direttamente alla pagina di importazione. */
add_action(
	'after_switch_theme',
	static function () {
		if ( ! get_option( 'ti_import_done' ) ) {
			set_transient( 'ti_import_redirect', 1, 60 );
		}
	}
);
add_action(
	'admin_init',
	static function () {
		if ( get_transient( 'ti_import_redirect' ) && current_user_can( 'manage_options' ) && ! wp_doing_ajax() ) {
			delete_transient( 'ti_import_redirect' );
			wp_safe_redirect( admin_url( 'themes.php?page=ti-import' ) );
			exit;
		}
	}
);

/**
 * HTML della pagina di importazione.
 */
function ti_import_page() {
	$done  = get_option( 'ti_import_done' );
	$nonce = wp_create_nonce( 'ti_import' );
	$scf   = function_exists( 'acf_add_local_field_group' );
	?>
	<div class="wrap">
		<h1>Importa i contenuti del sito</h1>
		<p style="max-width:720px">Crea in WordPress tutti i contenuti del sito attuale: testi della home, immagini, soci, team, investimenti, menu e pagine legali (in bozza). Le immagini vengono caricate una alla volta: lascia aperta questa pagina finché la barra non arriva in fondo (di solito 1–3 minuti).</p>
		<p style="max-width:720px">Puoi rilanciarla quando vuoi: non crea doppioni e non sovrascrive i testi già modificati.</p>
		<?php if ( ! $scf ) : ?>
			<div class="notice notice-warning inline"><p>Consiglio: installa e attiva prima <strong>Secure Custom Fields</strong>. L’importazione funziona lo stesso, ma senza il plugin non potrai modificare i contenuti.</p></div>
		<?php endif; ?>
		<?php if ( $done ) : ?>
			<div class="notice notice-success inline"><p>Importazione completata il <?php echo esc_html( wp_date( 'j F Y, H:i', (int) $done ) ); ?>. <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">Vedi il sito</a></p></div>
		<?php endif; ?>

		<p><button type="button" class="button button-primary button-hero" id="ti-import-start"><?php echo $done ? 'Rilancia importazione' : 'Avvia importazione'; ?></button></p>

		<div id="ti-import-progress" style="display:none;max-width:720px">
			<div style="background:#dcdcde;border-radius:4px;height:14px;overflow:hidden"><div id="ti-import-bar" style="background:#2271b1;height:100%;width:0;transition:width .3s"></div></div>
			<p id="ti-import-status" style="font-weight:600"></p>
			<ol id="ti-import-log" style="max-height:260px;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:8px 8px 8px 32px;font-size:12px"></ol>
		</div>
	</div>
	<script>
	(function () {
		const btn = document.getElementById('ti-import-start');
		const box = document.getElementById('ti-import-progress');
		const bar = document.getElementById('ti-import-bar');
		const status = document.getElementById('ti-import-status');
		const log = document.getElementById('ti-import-log');
		let errors = 0;

		function add(msg, err) {
			const li = document.createElement('li');
			li.textContent = msg;
			if (err) li.style.color = '#b32d2e';
			log.appendChild(li);
			log.scrollTop = log.scrollHeight;
		}

		async function step(reset) {
			const body = new URLSearchParams({ action: 'ti_import_step', _ajax_nonce: '<?php echo esc_js( $nonce ); ?>', reset: reset ? '1' : '0' });
			let res;
			try {
				res = await fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', body });
				res = await res.json();
			} catch (e) {
				errors++;
				add('Errore di rete o timeout, riprovo… (' + errors + ')', true);
				if (errors > 5) { status.textContent = 'Interrotto: troppi errori. Ricarica la pagina e rilancia, ripartirà da dove si è fermato.'; btn.disabled = false; return; }
				return step(false);
			}
			if (!res.success) { status.textContent = 'Errore: ' + (res.data || 'sconosciuto'); btn.disabled = false; return; }
			const d = res.data;
			(d.log || []).forEach(m => add(m.msg, m.error));
			bar.style.width = Math.round(d.pos / d.total * 100) + '%';
			status.textContent = d.done ? 'Fatto! Tutti i contenuti sono stati importati.' : 'Importazione in corso… ' + d.pos + ' / ' + d.total;
			if (d.done) { btn.disabled = false; btn.textContent = 'Rilancia importazione'; return; }
			return step(false);
		}

		btn.addEventListener('click', function () {
			btn.disabled = true;
			box.style.display = 'block';
			log.innerHTML = '';
			errors = 0;
			step(true);
		});
	})();
	</script>
	<?php
}

/* ---------------------------------------------------------------------------
 * Motore.
 * ------------------------------------------------------------------------ */

add_action(
	'wp_ajax_ti_import_step',
	static function () {
		check_ajax_referer( 'ti_import' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permessi insufficienti.' );
		}

		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

		$state = get_option( 'ti_import_state' );
		if ( ! empty( $_POST['reset'] ) || ! is_array( $state ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$state = array(
				'tasks' => ti_import_tasks(),
				'pos'   => 0,
			);
		}

		$log   = array();
		$start = microtime( true );
		$total = count( $state['tasks'] );

		while ( $state['pos'] < $total ) {
			$task = $state['tasks'][ $state['pos'] ];
			try {
				$msg   = ti_import_run( $task );
				$log[] = array( 'msg' => $msg, 'error' => false );
			} catch ( Throwable $e ) {
				$log[] = array( 'msg' => $task . ': ' . $e->getMessage(), 'error' => true );
			}
			$state['pos']++;
			update_option( 'ti_import_state', $state, false );

			// Un'immagine per richiesta, oppure al massimo ~8 secondi di lavoro.
			if ( str_starts_with( $task, 'img:' ) || microtime( true ) - $start > 8 ) {
				break;
			}
		}

		$done = $state['pos'] >= $total;
		if ( $done ) {
			delete_option( 'ti_import_state' );
		}

		wp_send_json_success(
			array(
				'pos'   => $state['pos'],
				'total' => $total,
				'done'  => $done,
				'log'   => $log,
			)
		);
	}
);

/**
 * Elenco dei passi.
 *
 * @return string[]
 */
function ti_import_tasks() {
	$tasks = array( 'base' );
	foreach ( ti_import_image_files() as $file ) {
		$tasks[] = 'img:' . $file;
	}
	$tasks[] = 'home';
	$tasks[] = 'soci';
	$tasks[] = 'team';
	$tasks[] = 'investimenti';
	$tasks[] = 'finish';
	return $tasks;
}

/**
 * Tutte le immagini usate dai contenuti iniziali.
 *
 * @return string[] Nomi file in seed/images.
 */
function ti_import_image_files() {
	$d     = ti_defaults();
	$files = array();
	foreach ( $d['home_images'] as $v ) {
		$files = array_merge( $files, (array) $v );
	}
	foreach ( $d['soci'] as $s ) {
		$files[] = $s['logo'];
	}
	foreach ( $d['team'] as $m ) {
		$files[] = $m['foto'];
	}
	foreach ( $d['investimenti'] as $i ) {
		$files[] = $i['logo'];
	}
	return array_values( array_unique( $files ) );
}

/**
 * Esegue un passo.
 *
 * @param string $task Passo.
 * @return string Messaggio per il log.
 * @throws RuntimeException Se qualcosa va storto.
 */
function ti_import_run( $task ) {
	if ( str_starts_with( $task, 'img:' ) ) {
		return ti_import_image( substr( $task, 4 ) );
	}
	switch ( $task ) {
		case 'base':
			return ti_import_base();
		case 'home':
			return ti_import_home();
		case 'soci':
			return ti_import_soci();
		case 'team':
			return ti_import_team();
		case 'investimenti':
			return ti_import_investimenti();
		case 'finish':
			update_option( 'ti_import_done', time() );
			flush_rewrite_rules();
			return 'Importazione completata.';
	}
	throw new RuntimeException( 'Passo sconosciuto.' );
}

/* ---------------------------------------------------------------------------
 * Scrittura dei campi nel formato di Secure Custom Fields.
 * ------------------------------------------------------------------------ */

/**
 * Salva un campo (valore + riferimento alla chiave del campo).
 *
 * @param int|string $target    ID post, 'option' oppure 'term_{id}'.
 * @param string     $name      Nome campo.
 * @param mixed      $value     Valore.
 * @param string     $key       Chiave campo (default: field_ti_{name}).
 * @param bool       $overwrite Sovrascrivi se esiste già.
 */
function ti_set_field( $target, $name, $value, $key = '', $overwrite = false ) {
	$key = $key ? $key : 'field_ti_' . $name;

	if ( 'option' === $target ) {
		$sentinel = '__ti_missing__';
		if ( ! $overwrite && get_option( 'options_' . $name, $sentinel ) !== $sentinel ) {
			return;
		}
		update_option( 'options_' . $name, $value, false );
		update_option( '_options_' . $name, $key, false );
		return;
	}

	if ( is_string( $target ) && str_starts_with( $target, 'term_' ) ) {
		$term_id = (int) substr( $target, 5 );
		if ( ! $overwrite && metadata_exists( 'term', $term_id, $name ) ) {
			return;
		}
		update_term_meta( $term_id, $name, $value );
		update_term_meta( $term_id, '_' . $name, $key );
		return;
	}

	$post_id = (int) $target;
	if ( ! $overwrite && metadata_exists( 'post', $post_id, $name ) ) {
		return;
	}
	update_post_meta( $post_id, $name, $value );
	update_post_meta( $post_id, '_' . $name, $key );
}

/**
 * Salva un repeater.
 *
 * @param int    $post_id Post.
 * @param string $name    Nome repeater.
 * @param array  $rows    Righe (array associativi).
 */
function ti_set_rows( $post_id, $name, array $rows ) {
	if ( metadata_exists( 'post', $post_id, $name ) ) {
		return;
	}
	foreach ( array_values( $rows ) as $i => $row ) {
		foreach ( $row as $sub => $value ) {
			ti_set_field( $post_id, "{$name}_{$i}_{$sub}", $value, "field_ti_{$name}_{$sub}", true );
		}
	}
	ti_set_field( $post_id, $name, count( $rows ), 'field_ti_' . $name, true );
}

/**
 * Trova un post creato dall'importatore.
 *
 * @param string $type Post type.
 * @param string $seed Identificativo.
 * @return int
 */
function ti_seed_post( $type, $seed ) {
	$ids = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'any',
			'meta_key'       => '_ti_seed', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $seed, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

/**
 * Crea un post se non esiste già.
 *
 * @param string $type Post type.
 * @param string $seed Identificativo.
 * @param array  $data Dati per wp_insert_post.
 * @return array [ID, creato?]
 * @throws RuntimeException Se l'inserimento fallisce.
 */
function ti_seed_insert( $type, $seed, array $data ) {
	$id = ti_seed_post( $type, $seed );
	if ( $id ) {
		return array( $id, false );
	}
	$id = wp_insert_post(
		array_merge(
			array(
				'post_type'   => $type,
				'post_status' => 'publish',
			),
			$data
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	update_post_meta( $id, '_ti_seed', $seed );
	return array( (int) $id, true );
}

/**
 * ID dell'allegato importato per un file.
 *
 * @param string $file Nome file.
 * @return int
 */
function ti_seed_media( $file ) {
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_ti_seed_file', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

/* ---------------------------------------------------------------------------
 * Passi.
 * ------------------------------------------------------------------------ */

/**
 * Impostazioni generali, pagine, menu, gruppi, testi di base.
 *
 * @return string
 */
function ti_import_base() {
	$d = ti_defaults();

	update_option( 'blogname', 'Trentino Invest' );
	update_option( 'blogdescription', '' );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	// Home.
	$home = ti_seed_post( 'page', 'home' );
	if ( ! $home && ti_front_id() && 'page' === get_option( 'show_on_front' ) ) {
		$home = ti_front_id();
		update_post_meta( $home, '_ti_seed', 'home' );
	}
	if ( ! $home ) {
		list( $home ) = ti_seed_insert(
			'page',
			'home',
			array(
				'post_title' => 'Home',
				'post_name'  => 'home',
			)
		);
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );

	// Pagine legali (bozza). Per la privacy riusa la pagina che WordPress crea all'installazione.
	$wp_privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $wp_privacy && get_post( $wp_privacy ) && ! ti_seed_post( 'page', 'legal:privacy-policy' ) ) {
		update_post_meta( $wp_privacy, '_ti_seed', 'legal:privacy-policy' );
	}
	foreach ( $d['legal'] as $field => $page ) {
		list( $id ) = ti_seed_insert(
			'page',
			'legal:' . $page['slug'],
			array(
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_status'  => 'draft',
				'post_content' => "<!-- wp:paragraph -->\n<p>Testo da inserire.</p>\n<!-- /wp:paragraph -->",
			)
		);
		ti_set_field( 'option', $field, $id );
	}

	// Testi delle impostazioni sito.
	foreach ( $d['options'] as $name => $value ) {
		ti_set_field( 'option', $name, $value );
	}

	// Testi e liste della home.
	foreach ( $d['home'] as $name => $value ) {
		ti_set_field( $home, $name, $value );
	}
	foreach ( $d['home_rows'] as $name => $rows ) {
		ti_set_rows( $home, $name, $rows );
	}

	// Gruppi del team.
	foreach ( $d['gruppi'] as $slug => $g ) {
		$term = get_term_by( 'slug', $slug, 'ti_gruppo' );
		if ( ! $term ) {
			$res = wp_insert_term( $g['name'], 'ti_gruppo', array( 'slug' => $slug ) );
			if ( is_wp_error( $res ) ) {
				throw new RuntimeException( $res->get_error_message() );
			}
			$term_id = (int) $res['term_id'];
		} else {
			$term_id = (int) $term->term_id;
		}
		ti_set_field( 'term_' . $term_id, 'titolo', $g['titolo'] );
		ti_set_field( 'term_' . $term_id, 'ordine', $g['ordine'] );
	}

	// Menu principale.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['principale'] ) || ! wp_get_nav_menu_object( $locations['principale'] ) ) {
		$menu = wp_get_nav_menu_object( 'Menu principale' );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( 'Menu principale' );
			if ( is_wp_error( $menu_id ) ) {
				throw new RuntimeException( $menu_id->get_error_message() );
			}
			foreach ( $d['menu'] as $title => $url ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $title,
						'menu-item-url'    => $url,
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					)
				);
			}
		} else {
			$menu_id = $menu->term_id;
		}
		$locations['principale'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	return 'Impostazioni, home, pagine legali (in bozza), gruppi del team e menu creati.';
}

/**
 * Importa un'immagine nella libreria media.
 *
 * @param string $file Nome file in seed/images.
 * @return string
 * @throws RuntimeException Se il caricamento fallisce.
 */
function ti_import_image( $file ) {
	if ( ti_seed_media( $file ) ) {
		return "Immagine già presente: {$file}";
	}

	$src = TI_DIR . '/seed/images/' . $file;
	if ( ! is_readable( $src ) ) {
		throw new RuntimeException( 'file mancante nel tema.' );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		throw new RuntimeException( 'impossibile creare il file temporaneo.' );
	}

	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		pathinfo( $file, PATHINFO_FILENAME )
	);

	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.unlink_unlink
		throw new RuntimeException( $id->get_error_message() );
	}

	update_post_meta( $id, '_ti_seed_file', $file );
	return "Immagine caricata: {$file}";
}

/**
 * Immagini della home.
 *
 * @return string
 */
function ti_import_home() {
	$d    = ti_defaults();
	$home = ti_seed_post( 'page', 'home' );
	if ( ! $home ) {
		throw new RuntimeException( 'pagina Home non trovata.' );
	}
	foreach ( $d['home_images'] as $name => $files ) {
		if ( is_array( $files ) ) {
			$ids = array_values( array_filter( array_map( static fn( $f ) => (string) ti_seed_media( $f ), $files ) ) );
			ti_set_field( $home, $name, $ids );
		} else {
			ti_set_field( $home, $name, ti_seed_media( $files ) );
		}
	}
	return 'Immagini della home collegate.';
}

/**
 * Soci.
 *
 * @return string
 */
function ti_import_soci() {
	$n = 0;
	foreach ( ti_defaults()['soci'] as $i => $s ) {
		list( $id, $new ) = ti_seed_insert(
			'ti_socio',
			'socio:' . sanitize_title( $s['title'] ),
			array(
				'post_title' => $s['title'],
				'menu_order' => $i,
			)
		);
		$logo = ti_seed_media( $s['logo'] );
		ti_set_field( $id, 'logo', $logo );
		ti_set_field( $id, 'sito', $s['sito'] );
		ti_set_field( $id, 'percentuale', $s['percentuale'] );
		ti_set_field( $id, 'colore', $s['colore'] );
		ti_alt( $logo, $s['title'] );
		$n += $new ? 1 : 0;
	}
	return "Soci: {$n} creati.";
}

/**
 * Team.
 *
 * @return string
 */
function ti_import_team() {
	$n = 0;
	foreach ( ti_defaults()['team'] as $i => $m ) {
		list( $id, $new ) = ti_seed_insert(
			'ti_membro',
			'membro:' . $m['gruppo'] . ':' . sanitize_title( $m['title'] ),
			array(
				'post_title' => $m['title'],
				'menu_order' => $i,
			)
		);
		if ( $new ) {
			wp_set_object_terms( $id, $m['gruppo'], 'ti_gruppo' );
		}
		$foto = ti_seed_media( $m['foto'] );
		ti_set_field( $id, 'foto', $foto );
		ti_set_field( $id, 'ruolo', $m['ruolo'] );
		ti_set_field( $id, 'linkedin', $m['linkedin'] );
		ti_set_field( $id, 'foto_in_alto', empty( $m['foto_in_alto'] ) ? 0 : 1 );
		ti_set_field( $id, 'icona_chiara', empty( $m['icona_chiara'] ) ? 0 : 1 );
		ti_alt( $foto, $m['title'] );
		$n += $new ? 1 : 0;
	}
	return "Team: {$n} persone create.";
}

/**
 * Investimenti.
 *
 * @return string
 */
function ti_import_investimenti() {
	$n = 0;
	foreach ( ti_defaults()['investimenti'] as $i => $inv ) {
		list( $id, $new ) = ti_seed_insert(
			'ti_investimento',
			'investimento:' . sanitize_title( $inv['title'] ),
			array(
				'post_title' => $inv['title'],
				'menu_order' => $i,
			)
		);
		$logo = ti_seed_media( $inv['logo'] );
		ti_set_field( $id, 'logo', $logo );
		ti_set_field( $id, 'sito', $inv['sito'] );
		ti_set_field( $id, 'descrizione', $inv['descrizione'] );
		ti_alt( $logo, $inv['title'] );
		$n += $new ? 1 : 0;
	}
	return "Investimenti: {$n} creati.";
}

/**
 * Imposta il testo alternativo di un'immagine se manca.
 *
 * @param int    $id  Allegato.
 * @param string $alt Testo.
 */
function ti_alt( $id, $alt ) {
	if ( $id && ! get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
}
