import { test, expect } from "@playwright/test";
import AxeBuilder from "@axe-core/playwright";

test("un comercio publica, actualiza, recibe y gestiona un pedido desde el celular", async ({
    page,
    browser,
}) => {
    const tag = Date.now().toString();
    const slug = "piloto-" + tag;
    const email = `piloto-${tag}@example.test`;
    const errors = [];
    page.on("pageerror", (e) => errors.push(e.message));
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto("/register");
    await page.getByLabel("Tu nombre", { exact: true }).fill("Ana Piloto");
    await page.getByLabel("Email", { exact: true }).fill(email);
    await page
        .getByLabel("Contraseña", { exact: true })
        .fill("PilotoSeguro123");
    await page
        .getByLabel("Repetí la contraseña", { exact: true })
        .fill("PilotoSeguro123");
    await page.getByRole("checkbox").check();
    await page.getByRole("button", { name: "Crear mi cuenta" }).click();
    await expect(page).toHaveURL(/onboarding/);
    await page
        .getByLabel("Nombre del negocio", { exact: true })
        .fill("Objetos de Ana");
    await page
        .getByLabel("Tu dirección permanente", { exact: true })
        .fill(slug);
    await page
        .getByLabel("WhatsApp del negocio", { exact: true })
        .fill("11 0000 0000");
    await page.getByRole("button", { name: "Crear mi espacio" }).click();
    await page.goto("/dev/mail");
    const verifyUrl = await page
        .frameLocator("iframe")
        .first()
        .getByRole("link", { name: "Verificar mi email" })
        .getAttribute("href");
    await page.goto(verifyUrl);
    await page.goto("/app/categories");
    await page.getByLabel("Nombre", { exact: true }).fill("Hogar");
    await page.getByRole("button", { name: "Crear categoría" }).click();
    await page.goto("/app/products/new");
    await page
        .getByLabel("Nombre del producto", { exact: true })
        .fill("Taza del piloto");
    await page
        .getByLabel("Descripción", { exact: false })
        .fill("Cerámica y buenos momentos.");
    await page.getByLabel("Precio en pesos", { exact: true }).fill("12500,50");
    await page
        .getByLabel("Categoría", { exact: true })
        .selectOption({ label: "Hogar" });
    await page
        .getByLabel("Foto principal", { exact: true })
        .setInputFiles("public/demo/mug.webp");
    await page
        .getByRole("button", { name: "Agregar producto", exact: true })
        .click();
    await expect(page).toHaveURL(/\/app\/products$/);
    await page
        .getByLabel("Precio de Taza del piloto", { exact: true })
        .fill("13900");
    await page
        .getByRole("button", {
            name: "Guardar precio de Taza del piloto",
            exact: true,
        })
        .click();
    await expect(
        page.getByText("Precio guardado", { exact: true }),
    ).toBeVisible();
    await page.goto("/app/catalog");
    await page
        .getByRole("button", { name: "Publicar catálogo", exact: true })
        .click();
    await expect(
        page.getByText("Tu catálogo está publicado. Compartí el enlace."),
    ).toBeVisible();
    await page.screenshot({
        path: "test-results/mvp-configuracion-movil.png",
        fullPage: true,
    });
    const buyer = await browser.newContext({
        viewport: { width: 390, height: 844 },
    });
    const shop = await buyer.newPage();
    await shop.goto("/" + slug);
    await expect(shop.getByText("$ 13.900", { exact: true })).toBeVisible();
    await expect(shop.locator("html")).toHaveJSProperty("scrollWidth", 390);
    await shop
        .getByRole("button", { name: "Agregar Taza del piloto", exact: true })
        .click();
    await shop
        .getByRole("button", { name: "Abrir mi pedido", exact: true })
        .click();
    await shop
        .getByRole("button", { name: "Revisar pedido", exact: true })
        .click();
    await shop
        .getByLabel("Tu nombre", { exact: true })
        .fill("Comprador Piloto");
    await shop.getByLabel("Tu WhatsApp", { exact: true }).fill("11 0000 0000");
    await shop
        .getByRole("button", { name: "Confirmar solicitud", exact: true })
        .click();
    await expect(shop).toHaveURL(/\/solicitud\//);
    await expect(
        shop.getByRole("heading", { name: "Tu solicitud quedó registrada." }),
    ).toBeVisible();
    await expect(shop.getByTestId("open-whatsapp")).toHaveAttribute(
        "href",
        /^https:\/\/wa.me\/5491100000000\?text=/,
    );
    await page.goto("/app/orders");
    await page.getByText("#000001 · Comprador Piloto", { exact: true }).click();
    await page
        .getByRole("button", { name: "Confirmar pedido", exact: true })
        .click();
    await page
        .getByRole("button", { name: "Marcar completado", exact: true })
        .click();
    await expect(
        page.getByText("Estado del pedido actualizado."),
    ).toBeVisible();
    await expect(page.locator(".badge.completed").first()).toBeVisible();
    await page.goto("/app/account");
    await page.getByRole("link", { name: "Ver correos de desarrollo" }).click();
    // Cron is checked independently; account mail already proved verification without external SMTP.
    await expect(
        page.getByRole("heading", { name: "Verificá tu email · Cataloguito" }),
    ).toBeVisible();
    await buyer.close();
    expect(errors).toEqual([]);
});

test("la demo es navegable, accesible y sin desbordamiento en ambos tamaños", async ({
    page,
}) => {
    await page.goto("/casa-luma");
    await expect(
        page.getByRole("heading", {
            name: "Objetos para los pequeños rituales.",
        }),
    ).toBeVisible();
    const results = await new AxeBuilder({ page })
        .withTags(["wcag2a", "wcag2aa", "wcag21aa"])
        .analyze();
    expect(
        results.violations.map((v) => ({
            id: v.id,
            description: v.description,
            nodes: v.nodes.map((n) => n.target),
        })),
    ).toEqual([]);
    await page.screenshot({
        path: "test-results/catalogo-escritorio.png",
        fullPage: true,
    });
    await page.setViewportSize({ width: 390, height: 844 });
    expect(
        await page.evaluate(
            () => document.documentElement.scrollWidth <= innerWidth,
        ),
    ).toBe(true);
    await page.screenshot({
        path: "test-results/catalogo-movil.png",
        fullPage: true,
    });
    await page
        .getByRole("button", {
            name: "Agregar Taza de cerámica Arena",
            exact: true,
        })
        .click();
    await page
        .getByRole("button", { name: "Abrir mi pedido", exact: true })
        .click();
    await expect(page.getByRole("dialog")).toBeVisible();
    await page.keyboard.press("Escape");
    await expect(page.getByRole("dialog")).not.toBeVisible();
});

test("la landing y el registro mobile muestran plantillas y errores claros", async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto("/");
    await expect(page.getByRole("heading", { name: /Tu catálogo online/ })).toBeVisible();
    await expect(page.getByRole("tab")).toHaveCount(7);
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await page.getByRole("tab", { name: "Tienda de ropa" }).click();
    await expect(page.getByRole("heading", { name: "Hilo", exact: true })).toBeVisible();
    await page.waitForTimeout(250);
    const landingAxe = await new AxeBuilder({ page }).withTags(["wcag2a", "wcag2aa", "wcag21aa"]).analyze();
    expect(landingAxe.violations.map((violation) => violation.id)).toEqual([]);

    await page.goto("/register");
    const buttonBottom = await page.getByRole("button", { name: "Crear mi cuenta" }).evaluate((button) => button.getBoundingClientRect().bottom);
    expect(buttonBottom).toBeLessThanOrEqual(667);
    await expect(page.getByLabel("País")).toHaveCount(0);
    await page.getByLabel("Tu nombre", { exact: true }).fill("Persona prueba");
    await page.getByLabel("Email", { exact: true }).fill("registro@example.test");
    await page.getByLabel("Contraseña", { exact: true }).fill("Password12345");
    await page.getByLabel("Repetí la contraseña", { exact: true }).fill("Password54321");
    await page.getByRole("checkbox").check();
    await page.getByRole("button", { name: "Crear mi cuenta" }).click();
    await expect(page.locator("#password-error")).toHaveText("Las contraseñas no coinciden.");
    await expect(page.getByLabel("Contraseña", { exact: true })).toHaveAttribute("aria-invalid", "true");
});
