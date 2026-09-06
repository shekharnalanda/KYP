#!/bin/bash
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SRC="$ROOT/public"
LIVE="/home4/mcied45x/kyp.mciedu.com"

mkdir -p "$LIVE/images/app-icons"

cp -f "$SRC/manifest.webmanifest" \
"$LIVE/manifest.webmanifest"

cp -f "$SRC/service-worker.js" \
"$LIVE/service-worker.js"

cp -f "$SRC/images/app-icons/kyp-192.png" \
"$LIVE/images/app-icons/kyp-192.png"

cp -f "$SRC/images/app-icons/kyp-512.png" \
"$LIVE/images/app-icons/kyp-512.png"

chmod 644 \
"$LIVE/manifest.webmanifest" \
"$LIVE/service-worker.js" \
"$LIVE/images/app-icons/kyp-192.png" \
"$LIVE/images/app-icons/kyp-512.png"

echo "KYP public PWA assets deployed."
