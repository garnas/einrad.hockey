import { test, expect } from '@playwright/test';

test.describe('Liga Tabellen (tabelle.php)', () => {
    test('Meisterschafts- und Rangtabelle werden für die aktuelle Saison angezeigt', async ({ page }) => {
        await page.goto('/liga/tabelle.php');

        await expect(page.getByRole('heading', { name: 'Meisterschaftstabelle' })).toBeVisible();
        await expect(page.getByRole('heading', { name: 'Rangtabelle' })).toBeVisible();
    });

    test('eine vergangene Saison kann über den Query-Parameter ausgewählt werden', async ({ page }) => {
        await page.goto('/liga/tabelle.php?saison=31');

        await expect(page.getByRole('heading', { name: 'Meisterschaftstabelle' })).toBeVisible();
        await expect(page.getByText('Saison 25/26')).toHaveCount(2);
    });

    test('ein bestimmter Spieltag kann über den Query-Parameter ausgewählt werden', async ({ page }) => {
        await page.goto('/liga/tabelle.php?saison=31&spieltag=1');

        await expect(page.getByRole('heading', { name: 'Spieltag 1' })).toHaveCount(2);
    });

    test('Meisterschaftstabelle zeigt saisonabhängige Punktestände für Stichproben-Teams', async ({ page }) => {
        await page.goto('/liga/tabelle.php?saison=31');
        await expect(page.locator('#large-meister-head-8')).toContainText('BTC Baukau Boogaloos');
        await expect(page.locator('#large-meister-head-8')).toContainText('4.167');
        await expect(page.locator('#large-meister-head-30')).toContainText('SKV Mörfelden Phönix');
        await expect(page.locator('#large-meister-head-30')).toContainText('3.825');

        await page.goto('/liga/tabelle.php?saison=29');
        await expect(page.locator('#large-meister-head-30')).toContainText('SKV Mörfelden Phönix');
        await expect(page.locator('#large-meister-head-30')).toContainText('5.248');
        await expect(page.locator('#large-meister-head-16')).toContainText('SKV Mörfelden Titans');
        await expect(page.locator('#large-meister-head-16')).toContainText('4.965');
    });

    test('Rangtabelle zeigt saisonabhängige Mittelwerte für Stichproben-Teams', async ({ page }) => {
        await page.goto('/liga/tabelle.php?saison=31');
        await expect(page.locator('#large-rang-head-8')).toContainText('BTC Baukau Boogaloos');
        await expect(page.locator('#large-rang-head-8')).toContainText('997,0');
        await expect(page.locator('#large-rang-head-30')).toContainText('SKV Mörfelden Phönix');
        await expect(page.locator('#large-rang-head-30')).toContainText('825,4');

        await page.goto('/liga/tabelle.php?saison=29');
        await expect(page.locator('#large-rang-head-30')).toContainText('SKV Mörfelden Phönix');
        await expect(page.locator('#large-rang-head-30')).toContainText('989,0');
        await expect(page.locator('#large-rang-head-8')).toContainText('BTC Baukau Boogaloos');
        await expect(page.locator('#large-rang-head-8')).toContainText('897,6');
    });
});
