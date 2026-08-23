import { test, expect } from '@playwright/test';
import path from 'node:path';
import {
    TEAMDATEN_TEAM_NAME,
    TEAMDATEN_TEAM_PASSWORT,
    TEAMDATEN_EMAIL_PERSISTENT,
    TEAMDATEN_EMAIL_DELETABLE,
    TEAMDATEN_SOLO_TEAM_NAME,
    TEAMDATEN_SOLO_EMAIL,
} from './fixtures/team';

const TEAMFOTO = path.join(__dirname, '..', '..', 'public', 'bilder', 'logo_kurz.jpg');

async function login(page: import('@playwright/test').Page, teamname: string, passwort: string): Promise<void> {
    await page.goto('/teamcenter/tc_login.php');
    await page.locator('#teamname').fill(teamname);
    await page.locator('#passwort').fill(passwort);
    await page.locator('button[name="login"]').click();
    await expect(page).toHaveURL(/tc_start\.php/);
}

test.describe('Teamcenter Teamdaten (tc_teamdaten_aendern.php)', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, TEAMDATEN_TEAM_NAME, TEAMDATEN_TEAM_PASSWORT);
        await page.goto('/teamcenter/tc_teamdaten_aendern.php');
    });

    test('Teamdetails können geändert und gespeichert werden', async ({ page }) => {
        await page.locator('#ligavertreter').fill('Neuer Ligavertreter');
        await page.locator('#plz').fill('12345');
        await page.locator('#ort').fill('Teststadt');
        await page.locator('#verein').fill('Playwright Verein e.V.');
        await page.locator('#homepage').fill('https://example.com');

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(page).toHaveURL(/tc_teamdaten_aendern\.php/);
        await expect(page.getByText('Teamdaten wurden gespeichert.')).toBeVisible();
        await expect(page.locator('#ligavertreter')).toHaveValue('Neuer Ligavertreter');
        await expect(page.locator('#plz')).toHaveValue('12345');
        await expect(page.locator('#ort')).toHaveValue('Teststadt');
        await expect(page.locator('#verein')).toHaveValue('Playwright Verein e.V.');
        await expect(page.locator('#homepage')).toHaveValue('https://example.com');
    });

    test('leerer Ligavertreter wird abgelehnt und nicht gespeichert', async ({ page }) => {
        const vorherigerWert = await page.locator('#ligavertreter').inputValue();

        // Das required-Attribut verhindert das Absenden im Browser; die serverseitige
        // Prüfung soll hier direkt getestet werden.
        await page.locator('#ligavertreter').evaluate((el) => el.removeAttribute('required'));
        await page.locator('#ligavertreter').fill('');

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(page.getByText('Bitte gebt einen Ligavertreter an.')).toBeVisible();
        await expect(page.locator('#ligavertreter')).toHaveValue(vorherigerWert);
    });

    test('Speichern ohne DSGVO-Zustimmung wird abgelehnt und nicht gespeichert', async ({ page }) => {
        await page.locator('#ligavertreter').fill('Sollte nicht gespeichert werden');
        await page.locator('#dsgvo').uncheck();

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(
            page.getByText('Der Ligavertreter hat noch nicht den Datenschutz-Hinweisen zugestimmt.'),
        ).toBeVisible();
        await expect(page.locator('#ligavertreter')).not.toHaveValue('Sollte nicht gespeichert werden');
    });

    test('eine neue E-Mail-Adresse kann hinzugefügt werden', async ({ page }) => {
        const neueEmail = `playwright-${Date.now()}@playwright-test.de`;

        await page.locator('#email').fill(neueEmail);
        await page.getByRole('button', { name: 'Email hinzufügen' }).click();

        await expect(page.getByText('E-Mail-Adresse wurde hinzugefügt')).toBeVisible();
        await expect(page.getByRole('row', { name: new RegExp(neueEmail) })).toBeVisible();
    });

    test('eine ungültige E-Mail-Adresse wird abgelehnt', async ({ page }) => {
        // input[type=email] verhindert das Absenden im Browser bereits clientseitig;
        // die serverseitige Prüfung soll hier direkt getestet werden.
        await page.locator('#email').evaluate((el) => el.setAttribute('type', 'text'));
        await page.locator('#email').fill('keine-email-adresse');
        await page.getByRole('button', { name: 'Email hinzufügen' }).click();

        await expect(page.getByText('E-Mail-Adresse wurde nicht akzeptiert')).toBeVisible();
        await expect(page.getByRole('row', { name: /keine-email-adresse/ })).toHaveCount(0);
    });

    test('Sichtbarkeit und Infomail-Einstellung einer E-Mail-Adresse können geändert werden', async ({ page }) => {
        const zeile = page.getByRole('row', { name: new RegExp(TEAMDATEN_EMAIL_PERSISTENT) });
        await zeile.locator('select').nth(0).selectOption('Nein'); // Öffentlich?
        await zeile.locator('select').nth(1).selectOption('Nein'); // Infomails?

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(page.getByText('Teamdaten wurden gespeichert.')).toBeVisible();
        const zeileNachher = page.getByRole('row', { name: new RegExp(TEAMDATEN_EMAIL_PERSISTENT) });
        await expect(zeileNachher.locator('select').nth(0)).toHaveValue('Nein');
        await expect(zeileNachher.locator('select').nth(1)).toHaveValue('Nein');
    });

    test('eine E-Mail-Adresse kann gelöscht werden, solange mindestens eine weitere bestehen bleibt', async ({ page }) => {
        const zeile = page.getByRole('row', { name: new RegExp(TEAMDATEN_EMAIL_DELETABLE) });
        await zeile.locator('select').nth(2).selectOption('Ja'); // Löschen?

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(page.getByText(`${TEAMDATEN_EMAIL_DELETABLE} wurde gelöscht`)).toBeVisible();
        await expect(page.getByRole('row', { name: new RegExp(TEAMDATEN_EMAIL_DELETABLE) })).toHaveCount(0);
    });

    test('Trikotfarbe 1 kann gesetzt und wieder entfernt werden', async ({ page }) => {
        const swatch = page.locator('label[for="color_1"] span.w3-card-4');

        // input[type=color] löst "change" (und damit onchange="this.form.submit()") erst beim Verlassen des Felds aus.
        await page.locator('#color_1').fill('#3355ff');
        await page.locator('#color_1').blur();
        await expect(page).toHaveURL(/tc_teamdaten_aendern\.php/);
        await expect(swatch).toHaveCSS('background-color', 'rgb(51, 85, 255)');

        await page.locator('button[name="no_color_1"]').click();
        await expect(page.getByText('Trikotfarbe geändert.')).toBeVisible();
        await expect(swatch).toHaveCSS('background-color', 'rgb(187, 187, 187)');
    });

    test('Teamfoto kann hochgeladen und wieder gelöscht werden', async ({ page }) => {
        await page.locator('#jpgupload').setInputFiles(TEAMFOTO);
        await page.getByRole('button', { name: 'Teamfoto hochladen' }).click();

        await expect(page.getByText('Teamfoto wurde hochgeladen.')).toBeVisible();
        await expect(page.getByRole('img', { name: TEAMDATEN_TEAM_NAME })).toBeVisible();

        await page.getByRole('button', { name: 'Neues Teamfoto / Teamfoto löschen' }).click();

        await expect(page.getByText('Teamfoto wurde gelöscht.')).toBeVisible();
        await expect(page.getByRole('img', { name: TEAMDATEN_TEAM_NAME })).toHaveCount(0);
    });
});

test.describe('Teamcenter Teamdaten - letzte E-Mail-Adresse (tc_teamdaten_aendern.php)', () => {
    test('die letzte verbleibende E-Mail-Adresse eines Teams kann nicht gelöscht werden', async ({ page }) => {
        await login(page, TEAMDATEN_SOLO_TEAM_NAME, TEAMDATEN_TEAM_PASSWORT);
        await page.goto('/teamcenter/tc_teamdaten_aendern.php');

        const zeile = page.getByRole('row', { name: new RegExp(TEAMDATEN_SOLO_EMAIL) });
        await zeile.locator('select').nth(2).selectOption('Ja'); // Löschen?

        await page.getByRole('button', { name: 'Teamdaten speichern' }).click();

        await expect(page.getByText('Es muss mindestens eine E-Mail-Adresse hinterlegt sein')).toBeVisible();
        await expect(page.getByRole('row', { name: new RegExp(TEAMDATEN_SOLO_EMAIL) })).toBeVisible();
    });
});
