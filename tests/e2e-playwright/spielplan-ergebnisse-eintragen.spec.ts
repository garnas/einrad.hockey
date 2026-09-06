import { test, expect, type Page } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { LIGAAUSSCHUSS_LOGIN, LIGAAUSSCHUSS_PASSWORT } from './fixtures/team';

// Die Turnier-IDs werden von fixtures/provision-spielplan.php beim globalSetup geschrieben.
// Das Ergebnis-Turnier hat dort bereits einen fertigen 4er-JgJ-Spielplan bekommen.
const { turnierIdErgebnis } = JSON.parse(
    fs.readFileSync(path.join(__dirname, 'fixtures', 'spielplan.fixture.json'), 'utf-8'),
) as { turnierIdSpielplan: number; turnierIdErgebnis: number };

const SPIELPLAN_URL = `/ligacenter/lc_spielplan.php?turnier_id=${turnierIdErgebnis}`;

async function loginLigaausschuss(page: Page): Promise<void> {
    await page.goto('/login.php');
    await page.locator('#login').fill(LIGAAUSSCHUSS_LOGIN);
    await page.locator('#passwort').fill(LIGAAUSSCHUSS_PASSWORT);
    await page.locator('button[name="login"]').click();
    await expect(page).toHaveURL(/lc_start\.php/);
}

test.describe('Ligacenter Ergebniseintragung in den Spielplan (lc_spielplan.php)', () => {
    test.beforeEach(async ({ page }) => {
        await loginLigaausschuss(page);
        await page.goto(SPIELPLAN_URL);
    });

    test('Tore aller Spiele können zwischengespeichert werden', async ({ page }) => {
        await expect(page.getByRole('heading', { name: 'Tore eintragen' })).toBeVisible();

        const toreA = page.locator('input[name^="tore_a["]');
        const toreB = page.locator('input[name^="tore_b["]');
        const anzahlSpiele = await toreA.count();
        expect(anzahlSpiele).toBe(6); // 4er Jeder-gegen-Jeden

        // Team A (obere Zeile) gewinnt jedes Spiel -> eindeutige Abschlusstabelle ohne Penaltys.
        for (let i = 0; i < anzahlSpiele; i++) {
            await toreA.nth(i).fill('3');
            await toreB.nth(i).fill('1');
        }

        await page.getByRole('button', { name: 'Tore zwischenspeichern' }).first().click();

        await expect(page.getByText('Spielergebnisse wurden gespeichert.')).toBeVisible();
        // Werte bleiben nach dem Neuladen erhalten.
        await expect(page.locator('input[name^="tore_a["]').first()).toHaveValue('3');
        await expect(page.locator('input[name^="tore_b["]').first()).toHaveValue('1');
    });

    test('vollständige Ergebnisse können in die Ligatabellen übermittelt werden', async ({ page }) => {
        const toreA = page.locator('input[name^="tore_a["]');
        const toreB = page.locator('input[name^="tore_b["]');
        const anzahlSpiele = await toreA.count();

        for (let i = 0; i < anzahlSpiele; i++) {
            await toreA.nth(i).fill('3');
            await toreB.nth(i).fill('1');
        }
        await page.getByRole('button', { name: 'Tore zwischenspeichern' }).first().click();
        await expect(page.getByText('Spielergebnisse wurden gespeichert.')).toBeVisible();

        await page.getByRole('button', { name: 'In die Ligatabellen eintragen' }).click();

        await expect(
            page.getByText('Das Turnierergebnis wurde dem Ligaausschuss übermittelt und wird jetzt in den Ligatabellen angezeigt.'),
        ).toBeVisible();
        await expect(page.getByText('Dem Ligaausschuss liegt ein Turnierergebnis vor.')).toBeVisible();
    });

    test('Ergebnisse können wieder gelöscht werden', async ({ page }) => {
        const toreA = page.locator('input[name^="tore_a["]');
        const toreB = page.locator('input[name^="tore_b["]');
        const anzahlSpiele = await toreA.count();
        for (let i = 0; i < anzahlSpiele; i++) {
            await toreA.nth(i).fill('3');
            await toreB.nth(i).fill('1');
        }
        await page.getByRole('button', { name: 'Tore zwischenspeichern' }).first().click();
        await expect(page.getByText('Spielergebnisse wurden gespeichert.')).toBeVisible();
        await page.getByRole('button', { name: 'In die Ligatabellen eintragen' }).click();
        await expect(page.getByText('Dem Ligaausschuss liegt ein Turnierergebnis vor.')).toBeVisible();

        // Aufräumen für den nächsten Durchlauf: über lc_spielplan_verwalten.php lässt sich
        // das eingetragene Turnierergebnis wieder entfernen.
        await page.goto(`/ligacenter/lc_spielplan_verwalten.php?turnier_id=${turnierIdErgebnis}`);
        await page.getByRole('button', { name: 'Ergebnis löschen' }).click();
        await expect(
            page.getByText('Ergebnis wurde gelöscht. Das Turnier wurde in die Spielplanphase versetzt.'),
        ).toBeVisible();
    });
});
