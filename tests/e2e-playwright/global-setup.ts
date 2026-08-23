import { execFileSync } from 'node:child_process';
import path from 'node:path';
import {
    TEAM_NAME,
    TEAM_PASSWORT,
    TURNIER_TEAM_NAME,
    TURNIER_TEAM_PASSWORT,
    TEAMDATEN_TEAM_NAME,
    TEAMDATEN_TEAM_PASSWORT,
} from './fixtures/team';

/**
 * Legt vor dem Testlauf die Fixture-Teams für tc_kader.php, tc_turnier_erstellen.php und
 * tc_teamdaten_aendern.php an (siehe fixtures/provision-*.php).
 * Läuft direkt gegen die lokale Dev-Datenbank, genau wie die PHPUnit-Integrationstests.
 */
export default function globalSetup(): void {
    const phpBinary = process.env.PHP_BINARY ?? 'php83';

    execFileSync(phpBinary, [path.join(__dirname, 'fixtures', 'provision-kader-team.php'), TEAM_NAME, TEAM_PASSWORT], { stdio: 'inherit' });
    execFileSync(phpBinary, [path.join(__dirname, 'fixtures', 'provision-turnier-team.php'), TURNIER_TEAM_NAME, TURNIER_TEAM_PASSWORT], { stdio: 'inherit' });
    execFileSync(phpBinary, [path.join(__dirname, 'fixtures', 'provision-teamdaten-team.php'), TEAMDATEN_TEAM_NAME, TEAMDATEN_TEAM_PASSWORT], { stdio: 'inherit' });
}
