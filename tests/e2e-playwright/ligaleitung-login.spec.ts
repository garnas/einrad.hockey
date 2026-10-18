import { test, expect, type Page } from '@playwright/test';
import {
    LIGALEITUNG_PASSWORT,
    LA_LOGIN,
    LA_PW_LOGIN,
    LA_VORNAME,
    OA_LOGIN,
    OA_PW_LOGIN,
    OA_VORNAME,
    SCHIRIAUSBILDER_LOGIN,
} from './fixtures/team';

const NEUES_PASSWORT = 'NeuesPwTest!2026';

async function loginAbschicken(page: Page, login: string, passwort: string): Promise<void> {
    await page.goto('/login.php');
    await page.locator('#login').fill(login);
    await page.locator('#passwort').fill(passwort);
    await page.locator('button[name="login"]').click();
}

async function passwortAendern(page: Page, url: string, alt: string, neu: string): Promise<void> {
    await page.goto(url);
    await page.locator('#passwort_alt').fill(alt);
    await page.locator('#passwort_neu').fill(neu);
    await page.getByRole('button', { name: 'Passwort ändern' }).click();
}

test.describe('Login Ligaleitung (login.php)', () => {
    test('Ligaausschuss kann sich einloggen und wird begrüßt', async ({ page }) => {
        await loginAbschicken(page, LA_LOGIN, LIGALEITUNG_PASSWORT);

        await expect(page).toHaveURL(/ligacenter\/lc_start\.php/);
        await expect(page.getByRole('heading', { name: `Hallo ${LA_VORNAME}!` })).toBeVisible();
    });

    test('Öffentlichkeitsausschuss kann sich einloggen und wird begrüßt', async ({ page }) => {
        await loginAbschicken(page, OA_LOGIN, LIGALEITUNG_PASSWORT);

        await expect(page).toHaveURL(/oefficenter\/oc_start\.php/);
        await expect(page.getByRole('heading', { name: `Hallo ${OA_VORNAME}!` })).toBeVisible();
    });

    test('falsches Passwort wird abgelehnt', async ({ page }) => {
        await loginAbschicken(page, LA_LOGIN, 'falsches-passwort');

        await expect(page).toHaveURL(/\/login\.php/);
        await expect(page.getByText('Falsches Passwort')).toBeVisible();

        await page.goto('/ligacenter/lc_start.php');
        await expect(page).toHaveURL(/\/login\.php\?redirect/);
    });

    test('unbekannter Loginname wird abgelehnt', async ({ page }) => {
        await loginAbschicken(page, 'playwright-gibt-es-nicht', LIGALEITUNG_PASSWORT);

        await expect(page).toHaveURL(/\/login\.php/);
        await expect(page.getByText('Unbekannter Loginname').first()).toBeVisible();
    });

    test('Login mit einer Funktion ohne eigenen Bereich schlägt fehl, ohne dass die Seite abstürzt', async ({ page }) => {
        const antwort = page.waitForResponse(
            (response) => response.url().includes('/login.php') && response.request().method() === 'POST',
        );
        await loginAbschicken(page, SCHIRIAUSBILDER_LOGIN, LIGALEITUNG_PASSWORT);

        expect((await antwort).status()).toBeLessThan(500);
        await expect(page).toHaveURL(/\/login\.php/);
        await expect(page.getByRole('heading', { name: 'Login Ligaleitung' })).toBeVisible();
    });

    test('nach dem Login wird auf die ursprünglich aufgerufene Ligacenter-Seite weitergeleitet', async ({ page }) => {
        await page.goto('/ligacenter/lc_pw_aendern.php');
        await expect(page).toHaveURL(/\/login\.php\?redirect/);
        await expect(page.getByText('Bitte logge dich als Ligaausschuss ein.')).toBeVisible();

        await page.locator('#login').fill(LA_LOGIN);
        await page.locator('#passwort').fill(LIGALEITUNG_PASSWORT);
        await page.locator('button[name="login"]').click();

        await expect(page).toHaveURL(/ligacenter\/lc_pw_aendern\.php/);
    });

    test('eingeloggter Ligaausschuss wird von der Login-Seite ins Ligacenter weitergeleitet', async ({ page }) => {
        await loginAbschicken(page, LA_LOGIN, LIGALEITUNG_PASSWORT);
        await expect(page).toHaveURL(/lc_start\.php/);

        await page.goto('/login.php');
        await expect(page).toHaveURL(/ligacenter\/lc_start\.php/);
    });

    test('Öffentlichkeitsausschuss hat keinen Zugriff auf das Ligacenter', async ({ page }) => {
        await loginAbschicken(page, OA_LOGIN, LIGALEITUNG_PASSWORT);
        await expect(page).toHaveURL(/oc_start\.php/);

        await page.goto('/ligacenter/lc_start.php');
        await expect(page).not.toHaveURL(/ligacenter\//);
    });

    test('Ligaausschuss kann sich ausloggen', async ({ page }) => {
        await loginAbschicken(page, LA_LOGIN, LIGALEITUNG_PASSWORT);
        await expect(page).toHaveURL(/lc_start\.php/);

        await page.goto('/ligacenter/lc_logout.php');
        await expect(page).toHaveURL(/liga\/neues\.php/);
        await expect(page.getByText('Logout erfolgreich')).toBeVisible();

        await page.goto('/ligacenter/lc_start.php');
        await expect(page).toHaveURL(/\/login\.php\?redirect/);
    });

    test('Öffentlichkeitsausschuss kann sich ausloggen', async ({ page }) => {
        await loginAbschicken(page, OA_LOGIN, LIGALEITUNG_PASSWORT);
        await expect(page).toHaveURL(/oc_start\.php/);

        await page.goto('/oefficenter/oc_logout.php');
        await expect(page).toHaveURL(/liga\/neues\.php/);
        await expect(page.getByText('Logout erfolgreich')).toBeVisible();

        await page.goto('/oefficenter/oc_start.php');
        await expect(page).toHaveURL(/\/login\.php\?redirect/);
    });
});

const PASSWORT_BEREICHE = [
    {
        name: 'Ligacenter (lc_pw_aendern.php)',
        login: LA_PW_LOGIN,
        url: '/ligacenter/lc_pw_aendern.php',
        start: /ligacenter\/lc_start\.php/,
        logout: '/ligacenter/lc_logout.php',
    },
    {
        name: 'Öffentlichkeitscenter (oc_pw_aendern.php)',
        login: OA_PW_LOGIN,
        url: '/oefficenter/oc_pw_aendern.php',
        start: /oefficenter\/oc_start\.php/,
        logout: '/oefficenter/oc_logout.php',
    },
];

for (const bereich of PASSWORT_BEREICHE) {
    // Seriell, da der letzte Test das Passwort tatsächlich ändert.
    test.describe.serial(`Passwort ändern im ${bereich.name}`, () => {
        test.beforeEach(async ({ page }) => {
            await loginAbschicken(page, bereich.login, LIGALEITUNG_PASSWORT);
            await expect(page).toHaveURL(bereich.start);
        });

        test('zu kurzes neues Passwort wird abgelehnt', async ({ page }) => {
            await passwortAendern(page, bereich.url, LIGALEITUNG_PASSWORT, 'kurz');

            await expect(page.getByText('Das Passwort muss mindestens acht Zeichen lang sein.')).toBeVisible();
        });

        test('falsches altes Passwort wird abgelehnt', async ({ page }) => {
            await passwortAendern(page, bereich.url, 'falsches-passwort', NEUES_PASSWORT);

            await expect(page.getByText('Falsches Passwort')).toBeVisible();
            await expect(page.getByText('Dein Passwort wurde geändert')).toHaveCount(0);
        });

        test('Passwort kann geändert werden und gilt danach für den Login', async ({ page }) => {
            await passwortAendern(page, bereich.url, LIGALEITUNG_PASSWORT, NEUES_PASSWORT);

            await expect(page).toHaveURL(bereich.start);
            await expect(page.getByText('Dein Passwort wurde geändert')).toBeVisible();

            await page.goto(bereich.logout);
            await loginAbschicken(page, bereich.login, LIGALEITUNG_PASSWORT);
            await expect(page.getByText('Falsches Passwort')).toBeVisible();

            await loginAbschicken(page, bereich.login, NEUES_PASSWORT);
            await expect(page).toHaveURL(bereich.start);
        });
    });
}
