import lighthouse from "lighthouse";
import { launch } from "chrome-launcher";
import { chromium } from "@playwright/test";
import { mkdirSync, writeFileSync } from "node:fs";

mkdirSync(".tools", { recursive: true });
const chrome = await launch({
    chromePath: chromium.executablePath(),
    chromeFlags: ["--headless", "--no-sandbox", "--disable-gpu"],
});
try {
    const result = await lighthouse(
        process.argv[2] || "http://127.0.0.1:8000/casa-luma",
        {
            port: chrome.port,
            output: "json",
            onlyCategories: ["performance", "accessibility", "seo"],
            logLevel: "error",
        },
    );
    writeFileSync(".tools/lighthouse.json", result.report);
    const { lhr } = result;
    console.log(
        JSON.stringify(
            {
                categories: Object.fromEntries(
                    Object.entries(lhr.categories).map(([key, value]) => [
                        key,
                        value.score,
                    ]),
                ),
                metrics: Object.fromEntries(
                    [
                        "first-contentful-paint",
                        "largest-contentful-paint",
                        "total-blocking-time",
                        "cumulative-layout-shift",
                        "speed-index",
                    ].map((key) => [key, lhr.audits[key].displayValue]),
                ),
            },
            null,
            2,
        ),
    );
} finally {
    await chrome.kill();
}
