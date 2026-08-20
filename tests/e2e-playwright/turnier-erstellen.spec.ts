import { test, expect } from '@playwright/test';
import { TURNIER_TEAM_NAME, TURNIER_TEAM_PASSWORT } from './fixtures/team';

// toISOString() rechnet in UTC um, was das Datum nahe der lokalen Mitternacht auf den Vortag verschieben kann.
// Daher wird hier direkt aus den lokalen Datumsanteilen formatiert.
function toDateInputValue(datum: Date): string {
    const jahr = datum.getFullYear();
    const monat = String(datum.getMonth() + 1).padStart(2, '0');
    const tag = String(datum.getDate()).padStart(2, '0');
    return `${jahr}-${monat}-${tag}`;
}

function futureDatum(weeksAhead: number): string {
    const datum = new Date();
    datum.setDate(datum.getDate() + weeksAhead * 7);
    return toDateInputValue(datum);
}

// Ligaturniere dürfen nur an einem Samstag, Sonntag oder bundeseinheitlichen Feiertag stattfinden
// und müssen spätestens vier Wochen vor dem Spieltag eingetragen werden (siehe TurnierValidatorService::hasValidDatum).
function naechsterSamstag(minWeeksAhead: number): string {
    const datum = new Date();
    datum.setDate(datum.getDate() + minWeeksAhead * 7);
    const diffZuSamstag = (6 - datum.getDay() + 7) % 7;
    datum.setDate(datum.getDate() + diffZuSamstag);
    return toDateInputValue(datum);
}

test.describe('Teamcenter Turniererstellung (tc_turnier_erstellen.php)', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/teamcenter/tc_login.php');
        await page.locator('#teamname').fill(TURNIER_TEAM_NAME);
        await page.locator('#passwort').fill(TURNIER_TEAM_PASSWORT);
        await page.locator('button[name="login"]').click();
        await expect(page).toHaveURL(/tc_start\.php/);

        await page.goto('/teamcenter/tc_turnier_erstellen.php');
    });

    test('Spaßturnier kann erstellt werden und Details werden angezeigt', async ({ page }) => {
        const turnierName = `Playwright Cup ${Date.now()}`;

        await page.locator('#datum').fill(futureDatum(4));
        await page.locator('#startzeit').fill('11:00');
        await page.locator('#art_block').selectOption('spass');
        await page.locator('#sofort_oeffnen').selectOption('none');
        await page.locator('#min_teams').selectOption('4');
        await page.locator('#plaetze').selectOption('6');
        await page.locator('#hallenname').fill('Playwright Sporthalle');
        await page.locator('#strasse').fill('Teststraße 1');
        await page.locator('#plz').fill('12345');
        await page.locator('#ort').fill('Teststadt');
        await page.locator('#tname').fill(turnierName);
        await page.locator('#startgebuehr').selectOption('keine');
        await page.locator('#organisator').fill('Playwright Bot');
        await page.locator('#handy').fill('0123456789');

        await page.getByRole('button', { name: 'Turnier eintragen' }).click();

        await expect(page).toHaveURL(/\/liga\/turnier_details\.php\?turnier_id=\d+/);
        await expect(page.getByText('Euer Turnier wurde erfolgreich eingetragen.')).toBeVisible();
        await expect(page.getByRole('heading', { name: new RegExp(turnierName) })).toBeVisible();
        const adresse = page.getByRole('cell', { name: 'Playwright Sporthalle' });
        await expect(adresse).toBeVisible();
        await expect(adresse).toContainText('Teststraße 1');
    });

    test('Blockeigenes Ligaturnier kann erstellt werden und Details werden angezeigt', async ({ page }) => {
        const turnierName = `Playwright Liga Cup ${Date.now()}`;

        // Das eigene Blocklevel des Teams (z.B. "F") ist von der Rangtabelle abhängig, daher wird
        // die Option über ihr Label statt eines festen Werts wie "I_F" ausgewählt.
        const blockeigeneOption = page.locator('#art_block option', { hasText: 'Blockeigenes Turnier (I)' });
        const blockeigenerWert = await blockeigeneOption.getAttribute('value');

        await page.locator('#datum').fill(naechsterSamstag(6));
        await page.locator('#startzeit').fill('10:00');
        await page.locator('#art_block').selectOption(blockeigenerWert!);
        await page.locator('#sofort_oeffnen').selectOption('none');
        await page.locator('#min_teams').selectOption('4');
        await page.locator('#plaetze').selectOption('6');
        await page.locator('#hallenname').fill('Playwright Ligahalle');
        await page.locator('#strasse').fill('Ligastraße 1');
        await page.locator('#plz').fill('54321');
        await page.locator('#ort').fill('Ligastadt');
        await page.locator('#tname').fill(turnierName);
        await page.locator('#startgebuehr').selectOption('10 Euro');
        await page.locator('#organisator').fill('Playwright Bot');
        await page.locator('#handy').fill('0123456789');

        await page.getByRole('button', { name: 'Turnier eintragen' }).click();

        await expect(page).toHaveURL(/\/liga\/turnier_details\.php\?turnier_id=\d+/);
        await expect(page.getByText('Euer Turnier wurde erfolgreich eingetragen.')).toBeVisible();
        await expect(page.getByRole('heading', { name: new RegExp(turnierName) })).toBeVisible();
        const adresse = page.getByRole('cell', { name: 'Playwright Ligahalle' });
        await expect(adresse).toBeVisible();
        await expect(adresse).toContainText('Ligastraße 1');
    });

    test('Turnier mit weniger Plätzen als der Mindestteamanzahl wird abgelehnt', async ({ page }) => {
        await page.locator('#datum').fill(futureDatum(4));
        await page.locator('#startzeit').fill('11:00');
        await page.locator('#art_block').selectOption('spass');
        await page.locator('#sofort_oeffnen').selectOption('none');
        await page.locator('#min_teams').selectOption('5');
        await page.locator('#plaetze').selectOption('4');
        await page.locator('#hallenname').fill('Playwright Sporthalle');
        await page.locator('#strasse').fill('Teststraße 1');
        await page.locator('#plz').fill('12345');
        await page.locator('#ort').fill('Teststadt');
        await page.locator('#startgebuehr').selectOption('keine');
        await page.locator('#organisator').fill('Playwright Bot');
        await page.locator('#handy').fill('0123456789');

        await page.getByRole('button', { name: 'Turnier eintragen' }).click();

        await expect(page).toHaveURL(/tc_turnier_erstellen\.php/);
        await expect(
            page.getByText('Anzahl der Plätze ist kleiner als die minimal Anzahl der Teams.'),
        ).toBeVisible();
    });
});
