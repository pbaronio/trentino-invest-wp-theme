#!/usr/bin/env bash
# Anteprima locale del tema con WordPress Playground (serve solo Node.js 20+).
# Il tema è collegato "dal vivo": se modifichi un file, ricarica la pagina.
#   ./preview.sh           avvia (il sito resta salvato tra un avvio e l'altro)
#   ./preview.sh --reset   riparte da un sito vuoto
set -e
cd "$(dirname "$0")"
npx -y @wp-playground/cli@latest start \
  --no-auto-mount \
  --mount="$PWD:/wordpress/wp-content/themes/trentino-invest" \
  --blueprint=.playground/blueprint.json \
  "$@"
