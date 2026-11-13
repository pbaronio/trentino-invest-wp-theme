<?php
/**
 * Contenuti iniziali del sito (presi dal sito Webflow).
 *
 * Servono a due cose:
 *  - l'importatore li usa per popolare WordPress al primo avvio;
 *  - i template li usano come testo di riserva finché un campo non è mai stato salvato.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tutti i contenuti iniziali.
 *
 * @return array
 */
function ti_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}

	$d = array(

		/* ------------------------------------------------------------------
		 * Home: campi di testo semplici.
		 * ------------------------------------------------------------------ */
		'home'         => array(
			'hero_titolo'         => 'Trentino Invest',

			'intro_titolo'        => "Dal Trentino,\ninvestiamo in imprese ad alto potenziale",
			'intro_testo'         => 'Sosteniamo la crescita di startup e aziende attraverso investimenti nel capitale, contribuendo allo sviluppo del territorio e all’attrazione di nuove iniziative imprenditoriali.',
			'intro_cta_testo'     => 'Scopri dove ci troviamo',
			'intro_cta_link'      => 'https://www.trentinosviluppo.it/it/spazi-location/spazi-e-location/progetto-manifattura',

			'chisiamo_etichetta'  => 'Chi siamo',
			'chisiamo_titolo'     => "Favoriamo lo sviluppo sostenibile del Trentino,\nfacilitando e supportando la crescita dell’imprenditorialità e la capacità di fare innovazione",
			'chisiamo_link_testo' => 'View Website',

			'approccio_etichetta' => 'Il nostro approccio',
			'approccio_titolo'    => 'Crediamo in progetti capaci di generare valore e contribuire allo sviluppo del territorio, accompagnandone la crescita nel tempo.',

			'criteri_etichetta'   => 'I nostri criteri',
			'criteri_titolo'      => 'Guidano le nostre scelte e riflettono il modo in cui valutiamo il potenziale di un’impresa.',

			'team_etichetta'      => 'Il team',
			'team_titolo'         => 'Competenze strategiche, operative e di investimento integrate per supportare lo sviluppo delle imprese.',

			'portfolio_etichetta' => 'Portfolio',
			'portfolio_titolo'    => "Investimenti\nattivi",
		),

		/* Home: immagini (nomi dei file in /seed/images). */
		'home_images'  => array(
			'hero_immagine'    => 'bg.jpg',
			'hero_loader'      => array(
				'TrentinoSviluppo_PressKit_MichelePurin_HD_183-1.jpg',
				'Rectangle-52.jpg',
				'Rectangle-4.jpg',
				'_I8A9424-1.jpg',
			),
			'intro_immagine_1' => 'Rectangle-52.jpg',
			'intro_immagine_2' => 'Rectangle-4.jpg',
		),

		/* Home: liste ripetibili. */
		'home_rows'    => array(
			'approccio_items' => array(
				array(
					'titolo' => 'Capitale',
					'testo'  => 'Partecipiamo al capitale di startup e imprese ad alto potenziale, adattando tempi e modalità a ogni singola operazione.',
				),
				array(
					'titolo' => 'Crescita',
					'testo'  => 'Contribuiamo allo sviluppo delle aziende partecipate, sostenendone la crescita nel tempo.',
				),
				array(
					'titolo' => 'Territorio',
					'testo'  => 'Siamo radicati nel territorio del Trentino e puntiamo alla crescita del tessuto imprenditoriale e all’attrazione di nuove iniziative.',
				),
			),
			'criteri_items'   => array(
				array(
					'titolo' => 'Persone',
					'testo'  => 'Investiamo in team con visione imprenditoriale, competenze solide e capacità di esecuzione.',
				),
				array(
					'titolo' => 'Potenziale',
					'testo'  => 'Selezioniamo startup e imprese con modelli scalabili e prospettive di crescita nel medio-lungo periodo.',
				),
				array(
					'titolo' => 'Posizionamento',
					'testo'  => 'Valutiamo realtà con un chiaro vantaggio competitivo o il potenziale per diventare leader nel proprio mercato.',
				),
				array(
					'titolo' => 'Impatto',
					'testo'  => 'Privilegiamo iniziative che contribuiscono allo sviluppo dell’ecosistema imprenditoriale e all’attrazione di nuove realtà.',
				),
			),
		),

		/* ------------------------------------------------------------------
		 * Impostazioni sito (pagina opzioni): contatti, footer, SEO.
		 * ------------------------------------------------------------------ */
		'options'      => array(
			'contatti_etichetta' => 'Contatti',
			'contatti_titolo'    => 'Hai un progetto in mente? Parliamone',
			'contatti_cta_testo' => 'Scrivici una mail',
			'contatti_email'     => 'info@trentinoinvest.it',
			'contatti_indirizzo' => 'Progetto Manifattura, Rovereto (TN)',
			'footer_copyright'   => '© 2020-{anno} Trentino Invest srl | All rights reserved',
			'footer_credits_testo' => 'Credits',
			'footer_credits_link'  => 'https://www.paperpaper.studio/',
			'seo_descrizione'    => 'Trentino Invest investe nel capitale di startup e imprese ad alto potenziale, contribuendo allo sviluppo del territorio trentino.',
		),

		/* Pagine legali: create in bozza, il cliente le completa e le pubblica. */
		'legal'        => array(
			'footer_pagina_cookie'  => array( 'title' => 'Cookie Policy', 'slug' => 'cookie-policy' ),
			'footer_pagina_privacy' => array( 'title' => 'Privacy Policy', 'slug' => 'privacy-policy' ),
			'footer_pagina_etico'   => array( 'title' => 'Ethic Code', 'slug' => 'ethic-code' ),
		),

		/* Menu principale (link alle sezioni della home). */
		'menu'         => array(
			'Chi siamo'           => '#chi-siamo',
			'Il nostro approccio' => '#approccio',
			'I nostri criteri'    => '#criteri',
			'Team'                => '#team',
			'Portfolio'           => '#portfolio',
			'Contatti'            => '#contatti',
		),

		/* ------------------------------------------------------------------
		 * Soci (CPT ti_socio).
		 * ------------------------------------------------------------------ */
		'soci'         => array(
			array( 'title' => 'Trentino Sviluppo', 'logo' => 'Logo-TS---ITA---RGB---Orizzontale-colori---Blu-Verde-1.png', 'sito' => 'https://www.trentinosviluppo.it/it', 'percentuale' => 40, 'colore' => 'orange' ),
			array( 'title' => 'La Finanziaria Trentina', 'logo' => 'Logo-La-Finanziaria-Trentina-SpA-1.png', 'sito' => 'https://www.lafinanziariatrentina.it/', 'percentuale' => 12, 'colore' => 'pistacchio' ),
			array( 'title' => 'ISA', 'logo' => 'ISA_2020_LOGO_colore-1.png', 'sito' => 'https://isa.tn.it/', 'percentuale' => 12, 'colore' => 'blu' ),
			array( 'title' => 'Fincoop', 'logo' => 'Livello_1.png', 'sito' => 'https://fincopp.org/', 'percentuale' => 12, 'colore' => 'giallo' ),
			array( 'title' => 'Fondazione Caritro', 'logo' => 'FC_logo_coloriA-1.png', 'sito' => 'https://www.fondazionecaritro.it/', 'percentuale' => 12, 'colore' => 'verde' ),
			array( 'title' => 'Agriduemila', 'logo' => 'Livello_1-1.png', 'sito' => 'https://www.agriduemilasrl.it/', 'percentuale' => 12, 'colore' => 'verde-chiaro' ),
		),

		/* ------------------------------------------------------------------
		 * Team: gruppi (tassonomia ti_gruppo) e persone (CPT ti_membro).
		 * ------------------------------------------------------------------ */
		'gruppi'       => array(
			'consiglio-di-amministrazione' => array( 'name' => 'Consiglio di Amministrazione', 'titolo' => 'Consiglio di Amministrazione', 'ordine' => 1 ),
			'responsabile-operativo'       => array( 'name' => 'Responsabile Operativo', 'titolo' => "Responsabile\nOperativo", 'ordine' => 2 ),
			'comitato-di-investimento'     => array( 'name' => 'Comitato di investimento', 'titolo' => 'Comitato di investimento', 'ordine' => 3 ),
		),
		'team'         => array(
			array( 'title' => 'Fulvio Rigotti', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Presidente', 'foto' => 'Fulvio-Rigotti_colori-1.jpg', 'linkedin' => 'https://www.linkedin.com/in/fulvio-rigotti-4202031/' ),
			array( 'title' => 'Andrea Berti', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Vice Presidente', 'foto' => 'Andrea-Berti_colori-12x.jpg', 'linkedin' => 'https://www.linkedin.com/in/andrea-berti-a597031bb/' ),
			array( 'title' => 'Claudio Cainelli', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Consigliere', 'foto' => 'Claudio-Cainelli_colori.jpg', 'linkedin' => '', 'foto_in_alto' => 1 ),
			array( 'title' => 'Alessio Filippi', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Consigliere', 'foto' => 'Ritratto-professionale-in-abito-blu-navy.png', 'linkedin' => 'https://www.linkedin.com/in/alessio-filippi-25b686a7/' ),
			array( 'title' => 'Matteo Dolzani', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => '', 'foto' => 'Matteo-Dolzani-12x.jpg', 'linkedin' => '' ),
			array( 'title' => 'Paolo Pretti', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Consigliere', 'foto' => 'Paolo-Pretti_colori.jpg', 'linkedin' => 'https://www.linkedin.com/in/paolo-pretti-81343358/', 'foto_in_alto' => 1 ),
			array( 'title' => 'Stefano Nicolini', 'gruppo' => 'consiglio-di-amministrazione', 'ruolo' => 'Consigliere', 'foto' => 'Ritratto-aziendale-in-bianco-e-nero.png', 'linkedin' => 'https://www.linkedin.com/in/stefano-nicolini-66b741240/' ),
			array( 'title' => 'Giovanni Sussarellu', 'gruppo' => 'responsabile-operativo', 'ruolo' => '', 'foto' => 'Giovanni-Sussarellu.png', 'linkedin' => 'https://www.linkedin.com/in/giovanni-sussarellu-b13325134/', 'icona_chiara' => 1 ),
			array( 'title' => 'Alessio Filippi', 'gruppo' => 'comitato-di-investimento', 'ruolo' => '', 'foto' => 'Alessio-Filippi.jpg', 'linkedin' => 'https://www.linkedin.com/in/alessio-filippi-25b686a7/' ),
			array( 'title' => 'Rita Scottini', 'gruppo' => 'comitato-di-investimento', 'ruolo' => '', 'foto' => 'team_4_fixed-22x.jpg', 'linkedin' => 'https://www.linkedin.com/in/rita-scottini-566b4038/' ),
			array( 'title' => 'Matteo Dolzani', 'gruppo' => 'comitato-di-investimento', 'ruolo' => '', 'foto' => 'e242c1d3-2297-4475-b0c5-01c6e513db40.png', 'linkedin' => '' ),
			array( 'title' => 'Michele Leonardi', 'gruppo' => 'comitato-di-investimento', 'ruolo' => '', 'foto' => 'Michele-Leonardi.jpg', 'linkedin' => 'https://www.linkedin.com/in/mleonardi-1995/' ),
			array( 'title' => 'Sebastiano Gervasi', 'gruppo' => 'comitato-di-investimento', 'ruolo' => '', 'foto' => 'team_5_fixed.jpg', 'linkedin' => 'https://www.linkedin.com/in/sebastiano-gervasi-778075187/', 'foto_in_alto' => 1 ),
		),

		/* ------------------------------------------------------------------
		 * Investimenti (CPT ti_investimento).
		 * ------------------------------------------------------------------ */
		'investimenti' => array(
			array( 'title' => 'Bikeflip S.r.l.', 'logo' => 'bikeflip.svg', 'sito' => 'https://www.bikeflip.com/it', 'descrizione' => 'Bikeflip è l’infrastruttura digitale che semplifica l’acquisto e la vendita di biciclette usate e ricondizionate in tutta Europa. Attraverso pagamenti sicuri e spedizioni assicurate, abilita transazioni tra privati e bike shop.' ),
			array( 'title' => 'Muffin S.r.l', 'logo' => 'muffin.svg', 'sito' => '', 'descrizione' => 'Muffin è un managed marketplace che supporta imprese e professionisti nel finanziamento di progetti di investimento attraverso la finanza agevolata. Grazie a una piattaforma proprietaria e a un network di consulenti certificati, gestisce l’intero processo, dall’analisi all’ottenimento delle risorse.' ),
			array( 'title' => 'Limolane S.r.l.', 'logo' => '03_Logo_LimoLane-4.svg', 'sito' => 'https://www.limolane.com/it/', 'descrizione' => 'LimoLane è una piattaforma di mobilità premium per aziende ed eventi, con presenza globale e una rete di oltre 6.000 driver. Supporta più di 2.000 clienti in diversi settori e gestisce grandi eventi attraverso una piattaforma digitale completa, con assistenza clienti 24/7.' ),
			array( 'title' => 'Nano Tech Projects S.r.l.', 'logo' => 'ntp_it_500.png', 'sito' => 'https://www.ntpsrl.biz/it/', 'descrizione' => 'NTP Nano Tech Projects è una PMI innovativa attiva nel settore della telemedicina. Sviluppa soluzioni di microscopia digitale basate sui sistemi proprietari NED (Nano Eye Device), utilizzati per la ricerca e la diagnostica in ambiti come telepatologia, tele-ematologia e biologia molecolare.' ),
			array( 'title' => 'TA-DAAN S.r.l.', 'logo' => 'logo_TA-DAAN_19ba06de-c0a8-4f19-9c76-13684bb3ae36_280x.png', 'sito' => 'https://ta-daan.com/it', 'descrizione' => 'TA-DAAN è un content e-commerce fondato nel 2020 con l’obiettivo di promuovere e valorizzare la nuova generazione di artigiani europei e l’artigianato contemporaneo. Attraverso un approccio che combina storytelling, design e vendita online, connette una community di oltre 7.000 makers con un pubblico attento a qualità, creatività e sostenibilità.' ),
			array( 'title' => 'Walliance S.p.A.', 'logo' => 'logo-walliance-black.svg', 'sito' => 'https://www.walliance.it/', 'descrizione' => 'Walliance è una piattaforma europea per investimenti in real estate ed energie rinnovabili. Sfrutta tecnologie avanzate per rendere l’investimento più accessibile e offre servizi di advisory per investitori immobiliari.' ),
			array( 'title' => 'Ciocomiti S.p.A.', 'logo' => 'Logo_Ciocomiti-V.Prim-Orizz-Pos-Payoff.png', 'sito' => 'https://ciocomiti.com/', 'descrizione' => 'Ciocomiti nasce nel cuore delle Dolomiti di Brenta e produce cioccolato “bean to bar” di qualità premium, affinato in alta quota. Il brand esprime un forte legame con la montagna e il territorio, con un orientamento verso innovazione, sostenibilità ed eccellenza.' ),
			array( 'title' => 'Onebra S.r.l.', 'logo' => '68f0c9500e4c803dcf9ff986_Onebra---logo.png', 'sito' => 'https://onebra.it/', 'descrizione' => 'ONEBra sviluppa protesi esterne su misura tramite tecnologia 3D per donne con asimmetria dei seni, dovuta a cause naturali o a interventi chirurgici come la mastectomia. Offre dispositivi medici non invasivi, leggeri e traspiranti, pensati per migliorare il benessere e la sicurezza nel proprio corpo.' ),
			array( 'title' => 'Alia Therapeutics S.r.l.', 'logo' => 'Alia-Therapeutics-vettore.jpg', 'sito' => 'https://www.aliatherapeutics.com/en/home/', 'descrizione' => 'Alia Therapeutics è una biotech innovativa nata come spin-off dell\'Università di Trento e specializzata nello sviluppo di terapie basate sulla tecnologia di gene editing CRISPR. Grazie a una piattaforma proprietaria e a un portafoglio di enzimi di nuova generazione, la società sviluppa approcci terapeutici mirati per il trattamento di malattie genetiche rare, con l\'obiettivo di portare la medicina genomica a un nuovo livello di precisione e sicurezza.' ),
			array( 'title' => 'Sirius Game S.r.l.', 'logo' => 'Sirius-Game_Logo_V1.svg', 'sito' => 'https://www.siriusgame.it/', 'descrizione' => 'Sirius Game è un framework educativo che trasforma l’apprendimento in esperienze interattive per scuole e organizzazioni. Attraverso un approccio basato sulla Pedagogia del Playful Learning, unisce ambienti digitali e pratiche analogiche in un modello proprietario sviluppato e validato in collaborazione con università internazionali. Attiva a livello internazionale, collabora con editori, istituzioni e aziende per co-creare percorsi formativi inclusivi e ad alto impatto.' ),
		),
	);

	return $d;
}

/**
 * Un singolo valore di default.
 *
 * @param string $group Gruppo (home, options...).
 * @param string $key   Chiave.
 * @return mixed
 */
function ti_default( $group, $key ) {
	$d = ti_defaults();
	return $d[ $group ][ $key ] ?? '';
}
