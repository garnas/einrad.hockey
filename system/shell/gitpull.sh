cd "$(git rev-parse --show-toplevel)" || { echo "Fehler in der Shell: Root nicht gefunden"; exit; }
git fetch origin/new-ranking
git diff origin/new-ranking
git reset --hard origin/new-ranking
git clean -fd