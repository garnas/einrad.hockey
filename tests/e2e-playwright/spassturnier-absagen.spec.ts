import { test, expect } from '@playwright/test';
import { TURNIER_TEAM_NAME, TURNIER_TEAM_PASSWORT } from './fixtures/team';

function datumInWochen(wochen: number): string {
    const datum = new Date();
    datum.setDate(datum.getDate() + wochen * 7);
    const monat = String(datum.getMonth() + 1).padStart(2, '0');
    const tag = String(datum.getDate()).padStart(2, '0');
    return `${datum.getFullYear()}-${monat}-${tag}`;
}

test.describe('Spaßturnier absagen (tc_turnier_absagen.php)', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/teamcenter/tc_login.php');
        await page.locator('#teamname').fill(TURNIER_TEAM_NAME);
        await page.locator('#passwort').fill(TURNIER_TEAM_PASSWORT);
        await page.locator('button[name="login"]').click();
        await expect(page).toHaveURL(/tc_start\.php/);
    });

    test('Ausrichter kann sein Spaßturnier absagen', async ({ page }) => {
        const turnierName = `Playwright Absage Cup ${Date.now()}`;

        await page.goto('/teamcenter/tc_turnier_erstellen.php');
        await page.locator('#datum').fill(datumInWochen(3));
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
        const turnierId = new URL(page.url()).searchParams.get('turnier_id');

        await page.goto(`/teamcenter/tc_turnier_absagen.php?turnier_id=${turnierId}`);
        await page.locator('#grund').fill('Halle nicht verfügbar');
        await page.locator('#send_mail').uncheck(); // keine Mails aus dem Testlauf
        page.once('dialog', (dialog) => dialog.accept());
        await page.getByRole('button', { name: 'Turnier absagen' }).click();

        await expect(page).toHaveURL(/\/teamcenter\/tc_turnierliste_verwalten\.php/);
        await expect(page.getByText('Turnier wurde abgesagt.')).toBeVisible();

        // Ein abgesagtes Turnier kann nicht erneut aufgerufen werden
        await page.goto(`/teamcenter/tc_turnier_absagen.php?turnier_id=${turnierId}`);
        await expect(page).toHaveURL(/\/teamcenter\/tc_turnierliste_verwalten\.php/);
        await expect(page.getByText('Turnier wurde abgesagt.')).toBeVisible();
    });
});
