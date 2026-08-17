import { test, expect } from '@playwright/test';
import { TEAM_NAME, TEAM_PASSWORT } from './fixtures/team';

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
});
