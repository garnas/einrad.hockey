import { test, expect } from '@playwright/test';
import { TEAM_NAME, TEAM_PASSWORT, GEBER_TEAM_NAME, VORSAISON_SPIELER_NAME, VORSAISON_SPIELER_NAME_OHNE_DSGVO, UEBERNAHME_SPIELER_NAME } from './fixtures/team';

test.describe('Teamcenter Kaderverwaltung (tc_kader.php)', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/teamcenter/tc_login.php');
        await page.locator('#teamname').fill(TEAM_NAME);
        await page.locator('#passwort').fill(TEAM_PASSWORT);
        await page.locator('button[name="login"]').click();
        await expect(page).toHaveURL(/tc_start\.php/);
    });

    test('neuer Spieler kann eingetragen werden und erscheint im Kader', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Neuen Spieler eintragen' }).click();
        const modal = page.locator('#spieler_eintragen');
        await modal.locator('#vorname').fill('Play');
        await modal.locator('#nachname').fill('Wright');
        await modal.locator('#jahrgang').fill('2001');
        await modal.locator('#geschlecht').selectOption('m');
        await modal.locator('#dsgvo_neu').check();
        await modal.getByRole('button', { name: 'Spieler eintragen' }).click();

        await expect(page).toHaveURL(/tc_kader\.php/);
        await expect(page.getByText('Der Spieler wurde erfolgreich eingetragen.')).toBeVisible();

        const roster = page.locator('table.w3-table.w3-striped').first();
        await expect(roster).toContainText('Play Wright');
        await expect(roster).toContainText('2001');
    });

    test('neuer Spieler ohne DSGVO-Zustimmung wird abgelehnt', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Neuen Spieler eintragen' }).click();
        const modal = page.locator('#spieler_eintragen');
        await modal.locator('#vorname').fill('Kein');
        await modal.locator('#nachname').fill('Zustimmer');
        await modal.locator('#jahrgang').fill('2001');
        await modal.locator('#geschlecht').selectOption('m');
        await modal.getByRole('button', { name: 'Spieler eintragen' }).click();

        await expect(page.getByText(/Datenschutz-Hinweisen muss zugestimmt werden/)).toBeVisible();

        const roster = page.locator('table.w3-table.w3-striped').first();
        await expect(roster).not.toContainText('Kein Zustimmer');
    });

    test('Spieler kann von einem anderen Team übernommen werden', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Von anderem Team übernehmen' }).click();
        const modal = page.locator('#spieler_uebernehmen');
        await modal.locator('#spieler_suche').fill(`${UEBERNAHME_SPIELER_NAME} (${GEBER_TEAM_NAME})`);
        await modal.locator('#dsgvo_uebernahme').check();
        await modal.getByRole('button', { name: 'Spieler übernehmen' }).click();

        await expect(page).toHaveURL(/tc_kader\.php/);
        await expect(
            page.getByText(`Der Spieler wurde erfolgreich vom vorherigen Team (${GEBER_TEAM_NAME}) übernommen.`),
        ).toBeVisible();

        const roster = page.locator('table.w3-table.w3-striped').first();
        await expect(roster).toContainText(UEBERNAHME_SPIELER_NAME);
    });

    test('Spieler ohne Auswahl aus der Liste wird beim Übernehmen von einem anderen Team abgelehnt', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Von anderem Team übernehmen' }).click();
        const modal = page.locator('#spieler_uebernehmen');
        await modal.locator('#spieler_suche').fill('Kein passender Treffer');
        await modal.locator('#dsgvo_uebernahme').check();
        await modal.getByRole('button', { name: 'Spieler übernehmen' }).click();

        await expect(page.getByText('Bitte einen Spieler aus der Liste auswählen.')).toBeVisible();

        const roster = page.locator('table.w3-table.w3-striped').first();
        await expect(roster).not.toContainText('Kein passender Treffer');
    });

    test('Spieler kann aus der Vorsaison übernommen werden', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Aus der Vorsaison übernehmen' }).click();
        const modal = page.locator('#spieler_vorsaison');
        await expect(modal).toContainText(VORSAISON_SPIELER_NAME);

        await modal.getByRole('row', { name: new RegExp(VORSAISON_SPIELER_NAME) }).getByRole('checkbox').check();
        await modal.locator('#dsgvo').check();
        await modal.getByRole('button', { name: 'Ausgewählte Spieler übernehmen' }).click();

        await expect(page).toHaveURL(/tc_kader\.php/);
        await expect(page.getByText('Die Spieler wurden in die neue Saison übernommen.')).toBeVisible();

        const roster = page.locator('table.w3-table.w3-striped').first();
        await expect(roster).toContainText(VORSAISON_SPIELER_NAME);
    });


    test('Übernahme aus der Vorsaison ohne DSGVO-Zustimmung wird abgelehnt', async ({ page }) => {
        await page.goto('/teamcenter/tc_kader.php');

        await page.getByRole('button', { name: 'Aus der Vorsaison übernehmen' }).click();
        const modal = page.locator('#spieler_vorsaison');
        await modal.getByRole('row', { name: new RegExp(VORSAISON_SPIELER_NAME_OHNE_DSGVO) }).getByRole('checkbox').check();
        await modal.getByRole('button', { name: 'Ausgewählte Spieler übernehmen' }).click();

        await expect(
            page.getByText('Den Datenschutz-Hinweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.'),
        ).toBeVisible();
    });
});
