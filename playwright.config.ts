import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e-playwright',
    globalSetup: './tests/e2e-playwright/global-setup.ts',
    fullyParallel: false,
    retries: 0,
    reporter: 'list',
    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL ?? 'http://localhost',
        trace: 'retain-on-failure',
        headless: false,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
