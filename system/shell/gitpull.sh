cd "$(git rev-parse --show-toplevel)" || { echo "Fehler in der Shell: Root nicht gefunden"; exit; }
git fetch origin master
git diff origin/master
git reset --hard origin/master
git clean -fd