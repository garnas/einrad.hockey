import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { TEAM_NAME, TEAM_PASSWORT } from './fixtures/team';

/**
 * Legt vor dem Testlauf das Fixture-Team für tc_kader.php an (siehe fixtures/provision-kader-team.php).
 * Läuft direkt gegen die lokale Dev-Datenbank, genau wie die PHPUnit-Integrationstests.
 */
export default function globalSetup(): void {
    const script = path.join(__dirname, 'fixtures', 'provision-kader-team.php');
    const phpBinary = process.env.PHP_BINARY ?? 'php83';
    execFileSync(phpBinary, [script, TEAM_NAME, TEAM_PASSWORT], { stdio: 'inherit' });
}
