cd "$(git rev-parse --show-toplevel)" || { echo "Fehler in der Shell: Root nicht gefunden"; exit; }
git fetch origin schiri_php8
git origin/schiri_php8
git reset --hard origin/schiri_php8
git clean -fd