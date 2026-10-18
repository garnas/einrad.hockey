import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: '.',
    globalSetup: './global-setup.ts',
    fullyParallel: false,
    retries: 0,
    reporter: 'list',
    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL ?? 'http://localhost',
        trace: 'retain-on-failure',
        headless: true,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
