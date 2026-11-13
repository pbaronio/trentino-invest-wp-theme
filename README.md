# Trentino Invest — tema WordPress

Tema su misura per [trentinoinvest.it](https://www.trentinoinvest.it/), convertito dall'export Webflow.
Markup, classi, CSS e animazioni sono quelli di Webflow; i contenuti si gestiscono da WordPress.

- **Home a sezioni**: testi e immagini modificabili dalla pagina Home.
- **Custom post type**: Soci, Team (con i gruppi), Portfolio (investimenti).
- **Impostazioni sito**: contatti, footer, pagine legali e SEO.
- **Solo plugin gratuiti**: [Secure Custom Fields](https://wordpress.org/plugins/secure-custom-fields/) (il fork gratuito di ACF mantenuto da WordPress.org).
- **Nessuna dipendenza esterna** tranne il kit Adobe Fonts (`hua0oug`): script Netlify, GSAP, Lenis, Swiper e jQuery sono nel tema.

Requisiti: WordPress 6.4+ (testato su 7.1), PHP 8.0+.

---

## Anteprima locale (prima di andare su Aruba)

Serve solo [Node.js](https://nodejs.org/) 20 o superiore: niente MAMP, Docker o database.

```bash
git clone https://github.com/pbaronio/trentino-invest-wp-theme.git
cd trentino-invest-wp-theme
./preview.sh
```

Il comando avvia WordPress Playground su `http://127.0.0.1:9400` e lo apre nel browser, già loggato come admin.
Il blueprint (`.playground/blueprint.json`) imposta la lingua italiana, installa Secure Custom Fields, attiva il tema e
apre **Aspetto → Importa contenuti**: clicca **Avvia importazione** e poi visita il sito.

- Il tema è collegato dal vivo: modifichi un file, ricarichi la pagina.
- Il sito resta salvato tra un avvio e l'altro; `./preview.sh --reset` riparte da zero.
- Per fermarlo: `Ctrl+C` nel terminale.

---

## Installazione su Aruba

1. **Installa WordPress** dal pannello Aruba (installazione automatica) oppure a mano via FTP, usando il database già creato.
   Per non toccare il sito statico online, puoi installarlo su un sottodominio di prova (es. `wp.trentinoinvest.it`).
2. In WordPress: **Plugin → Aggiungi nuovo** → cerca *Secure Custom Fields* → Installa → Attiva.
3. **Aspetto → Temi → Aggiungi nuovo → Carica tema** → scegli `trentino-invest.zip` → Installa → **Attiva**.
   Se il caricamento fallisce per il limite di dimensione (lo zip pesa circa 20 MB), carica la cartella
   `trentino-invest` via FTP in `wp-content/themes/` e attiva il tema da Aspetto → Temi.
4. Dopo l'attivazione si apre **Aspetto → Importa contenuti**: clicca **Avvia importazione** e aspetta che la barra arrivi in fondo
   (carica le 34 immagini una alla volta, 1–3 minuti). Crea:
   - la pagina **Home**, impostata come home page, con tutti i testi e le immagini;
   - 6 **Soci**, 13 persone nel **Team** divise in 3 gruppi, 10 aziende nel **Portfolio**;
   - il **Menu principale** con le ancore delle sezioni;
   - le pagine **Cookie Policy**, **Privacy Policy**, **Ethic Code** in **bozza**.

   L'importazione si può rilanciare: non crea doppioni e non sovrascrive i testi già modificati.
5. Completa e **pubblica** le pagine legali: i link nel footer compaiono solo quando la pagina è pubblicata.
6. Facoltativo: elimina la "Pagina di esempio" creata da WordPress.

### Go-live sul dominio principale

Se hai lavorato su un sottodominio, sposta il sito con un plugin gratuito di migrazione (es. *All-in-One WP Migration* o *Duplicator*),
oppure installa WordPress nella root del dominio: su Aruba `index.html` di solito ha la precedenza su `index.php`,
quindi il sito statico resta visibile finché non rinomini o elimini `index.html`.

---

## Dove si modifica cosa

| Sul sito | In bacheca |
| --- | --- |
| Apertura (titolo, immagine, animazione iniziale) | Pagine → Home → tab **Apertura** |
| Intro, titoli di sezione, Approccio, Criteri | Pagine → Home → tab corrispondenti |
| Loghi soci e barre delle quote | **Soci** (logo, sito, quota %, colore) |
| Persone del team | **Team** (foto, ruolo, LinkedIn, gruppo) |
| Gruppi del team (titolo e ordine) | **Team → Gruppi** |
| Aziende del portfolio | **Portfolio** (logo, descrizione, sito) |
| Contatti, footer, pagine legali, SEO | **Impostazioni sito** |
| Voci del menu | Aspetto → Menu |
| Favicon | Aspetto → Personalizza → Identità del sito (se vuota usa quelle del tema) |

L'ordine di Soci, Team e Portfolio si imposta nel box **Attributi → Ordine** (0 = primo).
Una persona presente in due gruppi va inserita due volte (ruolo e foto possono essere diversi).
Per nascondere un elemento senza cancellarlo, spostalo in bozza.

I loghi possono essere caricati anche in **SVG**: il tema li accetta per chi può caricare file e li ripulisce da script e codice attivo.

---

## Struttura

```
style.css               intestazione del tema
functions.php           carica i file in inc/
header.php / footer.php navbar, bottone "torna su", script
front-page.php          home (sezioni in template-parts/home/)
page.php                pagine interne (legali), con la sezione Contatti in fondo
template-parts/
  home/                 hero, intro, chi-siamo, approccio, criteri, team, portfolio
  contatti.php          contatti + footer
inc/
  defaults.php          contenuti iniziali (usati dall'importatore e come testo di riserva)
  helpers.php           lettura campi, immagini, icone, bottoni
  setup.php             supporti del tema, menu, meta SEO, favicon
  assets.php            CSS/JS nell'ordine dell'export Webflow
  post-types.php        CPT ti_socio, ti_membro, ti_investimento + tassonomia ti_gruppo
  fields.php            campi Secure Custom Fields registrati via codice
  admin.php             avvisi, colonne in bacheca, editor disattivato sulla home
  svg-upload.php        caricamento SVG con pulizia
  importer.php          Aspetto → Importa contenuti
assets/
  css/                  normalize, webflow, trentino-invest.webflow (export) + custom/ (ex Netlify) + embeds + theme
  js/                   webflow.js (export) + custom/ (ex Netlify)
  vendor/               jQuery 3.5.1, GSAP 3.15 (+ plugin), Lenis 1.2.3, Swiper 12
  svg/                  icone e logo inline
  images/               favicon
seed/images/            immagini originali per l'importazione (non usate dal sito)
```

I campi sono registrati in PHP (`inc/fields.php`), quindi non compaiono in *SCF → Gruppi di campi* e non si possono rompere da lì.
I template leggono i valori direttamente dai meta: se Secure Custom Fields viene disattivato il sito resta visibile, si perde solo la possibilità di modificarlo.

### Aggiornare CSS o interazioni da Webflow

Se cambi lo stile in Webflow, esporta di nuovo e sostituisci `assets/css/trentino-invest.webflow.css` (e `assets/js/webflow.js` se hai toccato le interazioni).
Le classi nel markup sono le stesse di Webflow. `webflow.js` contiene le interazioni (navbar, barre, linee): gli attributi
`data-wf-page`, `data-wf-site` e `data-w-id` nel markup servono a quelle, non vanno rimossi.

### Differenze rispetto all'export Webflow

- Script e CSS che venivano da `trentino-invest.netlify.app` sono in `assets/*/custom/`, copiati dal repo `pbaronio/trentino-invest`.
  Il riferimento a `css/style.css` è stato tolto: era un file di prova già cancellato dal repo (dava 404).
- GSAP veniva caricato due volte (CDN Webflow + jsDelivr): ora una volta sola, in locale.
- `loader.js`: aggiunto un controllo nello slideshow, che andava in errore perché nel markup non ci sono miniature.
  Lo script viene caricato solo in home.
- Hero: tolte le 4 slide nascoste rimaste dal template Webflow (immagini di prova mai visibili). Il titolo è un `<h1>`.
- Bottone mobile "Scopri dove ci troviamo": prima puntava a `#`, ora usa lo stesso link del bottone desktop.
- Cerchietti freccia sotto i loghi dei soci: prima `#`, ora aprono il sito del socio.
- Muffin: senza sito, quindi la freccia "visita" non viene mostrata invece di puntare a `#`.
- Copyright con anno automatico (`{anno}`), link legali visibili solo se la pagina è pubblicata.
- Pagine interne: navbar bianca fissa, visto che non c'è l'immagine scura sotto.
