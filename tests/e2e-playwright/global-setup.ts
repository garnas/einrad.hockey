import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { TEAM_NAME, TEAM_PASSWORT, TURNIER_TEAM_NAME, TURNIER_TEAM_PASSWORT } from './fixtures/team';

/**
 * Legt vor dem Testlauf die Fixture-Teams für tc_kader.php und tc_turnier_erstellen.php an
 * (siehe fixtures/provision-kader-team.php und fixtures/provision-turnier-team.php).
 * Läuft direkt gegen die lokale Dev-Datenbank, genau wie die PHPUnit-Integrationstests.
 */
export default function globalSetup(): void {
    const phpBinary = process.env.PHP_BINARY ?? 'php83';

    execFileSync(phpBinary, [path.join(__dirname, 'fixtures', 'provision-kader-team.php'), TEAM_NAME, TEAM_PASSWORT], { stdio: 'inherit' });
    execFileSync(phpBinary, [path.join(__dirname, 'fixtures', 'provision-turnier-team.php'), TURNIER_TEAM_NAME, TURNIER_TEAM_PASSWORT], { stdio: 'inherit' });
}
