import { test, expect, type Page } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { LIGAAUSSCHUSS_LOGIN, LIGAAUSSCHUSS_PASSWORT } from './fixtures/team';

// Die Turnier-IDs werden von fixtures/provision-spielplan.php beim globalSetup geschrieben.
const { turnierIdSpielplan } = JSON.parse(
    fs.readFileSync(path.join(__dirname, 'fixtures', 'spielplan.fixture.json'), 'utf-8'),
) as { turnierIdSpielplan: number; turnierIdErgebnis: number };

const VERWALTEN_URL = `/ligacenter/lc_spielplan_verwalten.php?turnier_id=${turnierIdSpielplan}`;

async function loginLigaausschuss(page: Page): Promise<void> {
    await page.goto('/login.php');
    await page.locator('#login').fill(LIGAAUSSCHUSS_LOGIN);
    await page.locator('#passwort').fill(LIGAAUSSCHUSS_PASSWORT);
    await page.locator('button[name="login"]').click();
    await expect(page).toHaveURL(/lc_start\.php/);
}

// Setzt das Turnier zurück in die Setzphase (ohne Spielplan), damit der Test unabhängig von
// vorherigen Durchläufen startet.
async function spielplanZuruecksetzen(page: Page): Promise<void> {
    await page.goto(VERWALTEN_URL);
    const loeschen = page.getByRole('button', { name: 'JgJ-Spielplan löschen' });
    if (await loeschen.isVisible()) {
        await loeschen.click();
        await expect(page.getByText('Der dynamisch erstellte Spielplan wurde gelöscht')).toBeVisible();
    }
}

test.describe('Ligacenter Spielplanerstellung (lc_spielplan_verwalten.php)', () => {
    test.beforeEach(async ({ page }) => {
        await loginLigaausschuss(page);
        await spielplanZuruecksetzen(page);
    });

    test('Spielen-Liste zeigt die vier gesetzten Teams', async ({ page }) => {
        await page.goto(VERWALTEN_URL);

        await expect(page.getByRole('heading', { name: /Spielplan\/Ergebnis/ })).toBeVisible();

        const spielenliste = page.locator('table').filter({ hasText: 'Teamblock' }).first();
        for (const teamname of [
            'Playwright SP Team 1',
            'Playwright SP Team 2',
            'Playwright SP Team 3',
            'Playwright SP Team 4',
        ]) {
            await expect(spielenliste.getByRole('cell', { name: teamname })).toBeVisible();
        }
    });

    test('JgJ-Spielplan wird erstellt und in der Liga angezeigt', async ({ page }) => {
        await page.goto(VERWALTEN_URL);

        await page.getByRole('button', { name: 'JgJ-Spielplan erstellen' }).click();

        // Nach dem Erstellen leitet die Seite auf den öffentlichen Spielplan um.
        await expect(page).toHaveURL(
            new RegExp(`/liga/spielplan\\.php\\?turnier_id=${turnierIdSpielplan}`),
        );
        await expect(page.getByRole('heading', { name: '4er-Spielplan' })).toBeVisible();
        await expect(page.getByText('Das Turnier wurde in die Spielplan-Phase versetzt.')).toBeVisible();

        // Der Spielplan listet die Begegnungen aller vier Teams auf.
        await expect(page.getByRole('heading', { name: 'Spiele' })).toBeVisible();
        for (const teamname of [
            'Playwright SP Team 1',
            'Playwright SP Team 2',
            'Playwright SP Team 3',
            'Playwright SP Team 4',
        ]) {
            await expect(page.getByText(teamname).first()).toBeVisible();
        }
    });

    test('erstellter JgJ-Spielplan kann wieder gelöscht werden', async ({ page }) => {
        await page.goto(VERWALTEN_URL);
        await page.getByRole('button', { name: 'JgJ-Spielplan erstellen' }).click();
        await expect(page).toHaveURL(/\/liga\/spielplan\.php/);

        await page.goto(VERWALTEN_URL);
        await page.getByRole('button', { name: 'JgJ-Spielplan löschen' }).click();

        await expect(
            page.getByText('Der dynamisch erstellte Spielplan wurde gelöscht. Das Turnier wurde in die Setzphase versetzt!'),
        ).toBeVisible();
        await expect(page.getByRole('button', { name: 'JgJ-Spielplan erstellen' })).toBeVisible();
    });
});
