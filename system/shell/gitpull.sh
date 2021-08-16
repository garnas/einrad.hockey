cd "$(git rev-parse --show-toplevel)" || { echo "Fehler in der Shell: Root nicht gefunden"; exit; }
git fetch origin git-deploy
git diff origin/git-deploy
git reset --hard origin/git-deploy
git clean -fd