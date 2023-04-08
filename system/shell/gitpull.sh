cd "$(git rev-parse --show-toplevel)" || { echo "Fehler in der Shell: Root nicht gefunden"; exit; }
git fetch origin stats-extension
git diff origin/stats-extension
git reset --hard origin/stats-extension
git clean -fd