#!/usr/bin/env bash
# Builds build/hikashop-for-elementor/ and build/hikashop-for-elementor.zip from the files .distignore keeps
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
out="$root/build/hikashop-for-elementor"
rm -rf "$root/build"
mkdir -p "$out"
rsync -a --exclude-from="$root/.distignore" --exclude=/build --exclude=/bin "$root/" "$out/"
( cd "$root/build" && zip -qr hikashop-for-elementor.zip hikashop-for-elementor )
echo "$root/build/hikashop-for-elementor.zip"
