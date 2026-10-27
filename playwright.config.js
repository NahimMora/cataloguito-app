import { defineConfig } from "@playwright/test";
export default defineConfig({
    testDir: "./tests/Browser",
    fullyParallel: false,
    workers: 1,
    timeout: 60000,
    use: {
        baseURL: "http://127.0.0.1:8001",
        viewport: { width: 1440, height: 1000 },
        trace: "retain-on-failure",
        screenshot: "only-on-failure",
    },
    reporter: [["list"], ["html", { open: "never" }]],
    webServer: {
        command:
            "node scripts/php.cjs artisan serve --host=127.0.0.1 --port=8001 --env=e2e --no-reload",
        env: { APP_ENV: "e2e" },
        url: "http://127.0.0.1:8001/up",
        reuseExistingServer: false,
        timeout: 45000,
    },
});
