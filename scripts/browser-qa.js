import { firefox } from "playwright";

const BASE_URL = "http://localhost/laravel";
const EMAIL = "sunnyazahar@gmail.com";
const PASSWORD = "Sunny@000";

function stamp() {
  return `AUTO_QA_${Date.now()}`;
}

async function login(page) {
  await page.goto(`${BASE_URL}/login`, { waitUntil: "domcontentloaded" });
  await page.fill('input[name="email"]', EMAIL);
  await page.fill('input[name="password"]', PASSWORD);
  await Promise.all([
    page.waitForLoadState("networkidle"),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);
  if (page.url().includes("/otp")) {
    const bodyText = await page.locator("body").innerText();
    const otpMatch = bodyText.match(/\b(\d{6})\b/);
    if (!otpMatch) {
      throw new Error("OTP page reached but no local OTP code found.");
    }
    const otp = otpMatch[1];
    await page.fill("#otp-value", otp);
    await Promise.all([
      page.waitForLoadState("networkidle"),
      page.click('#otp-form button[type="submit"], #otp-form input[type="submit"]'),
    ]);
  }
  if (page.url().includes("/login") || page.url().includes("/otp")) {
    throw new Error("Login failed or redirected back to login page.");
  }
}

async function checkPage(page, path, report) {
  const res = await page.goto(`${BASE_URL}${path}`, { waitUntil: "domcontentloaded" });
  report.pages.push({ path, status: res ? res.status() : null, ok: !!res && res.status() < 400 });
}

async function checkVisible(page, path, selector, report, label) {
  const res = await page.goto(`${BASE_URL}${path}`, { waitUntil: "domcontentloaded" });
  const visible = (await page.locator(selector).count()) > 0;
  report.pages.push({
    path,
    label,
    status: res ? res.status() : null,
    ok: !!res && res.status() < 400 && visible,
  });
}

async function checkFilters(page, path, query, report) {
  const url = `${BASE_URL}${path}?${query}`;
  const res = await page.goto(url, { waitUntil: "domcontentloaded" });
  report.filters.push({ path, query, status: res ? res.status() : null, ok: !!res && res.status() < 400 });
}

async function createAgentAndCleanup(page, report) {
  const marker = stamp();
  await page.goto(`${BASE_URL}/Agents/create`, { waitUntil: "domcontentloaded" });
  await page.fill('input[name="agent_name"]', marker);
  await page.fill('input[name="contact_person"]', "Automation User");
  if (await page.locator('input[name="email"]').count()) {
    await page.fill('input[name="email"]', `qa+${Date.now()}@example.com`);
  }
  await Promise.all([
    page.waitForLoadState("networkidle"),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);

  // Verify created record appears in list
  const listUrl = `${BASE_URL}/Agents?name=${encodeURIComponent(marker)}&hide_inactive=0`;
  await page.goto(listUrl, { waitUntil: "domcontentloaded" });
  const row = page.locator("tr", { hasText: marker }).first();
  const exists = (await row.count()) > 0;
  report.crud.push({ module: "agents", action: "create", ok: exists, marker });

  // Delete by HTTP request to avoid flaky JS confirmation modals
  if (exists) {
    const editLink = row.locator('a[href*="/Agents/edit/"]').first();
    const href = await editLink.getAttribute("href");
    if (href) {
      const id = href.split("/").pop();
        await page.goto(href, { waitUntil: "domcontentloaded" });
        for (const tabId of [
          "agent-details",
          "billing-details",
          "sop",
          "pricing",
          "agent-users",
          "contacts",
          "email-settings",
          "scan-gun",
        ]) {
          const tab = page.locator(`.tab-item[data-tab="${tabId}"]`).first();
          if ((await tab.count()) > 0) {
            await tab.click();
            const active = await page.locator(`#${tabId}.tab-content-custom.active`).count();
            report.crud.push({ module: "agents", action: `tab:${tabId}`, ok: active > 0, id });
          }
        }

      const token = await page.locator('meta[name="csrf-token"]').getAttribute("content");
      const deleteRes = await page.request.fetch(`${BASE_URL}/Agents/${id}`, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN": token || "",
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json,text/plain,*/*",
        },
      });
      report.crud.push({ module: "agents", action: "delete", ok: deleteRes.ok(), id });
    }
  }
}

async function checkShipmentMailEndpoints(page, report) {
  const res = await page.goto(`${BASE_URL}/shipments`, { waitUntil: "domcontentloaded" });
  if (!res || res.status() >= 400) {
    report.mail.push({ action: "open_shipments", ok: false, status: res ? res.status() : null });
    return;
  }

  const editHref = await page.locator('a[href*="/shipments/edit/"]').first().getAttribute("href");
  if (!editHref) {
    report.mail.push({ action: "find_shipment_edit_link", ok: false });
    return;
  }

  const id = editHref.split("/").pop();
  const token = await page.locator('meta[name="csrf-token"]').getAttribute("content");

  const prepManifest = await page.request.post(`${BASE_URL}/shipments/${id}/manifest-mail/prepare`, {
    headers: { "X-CSRF-TOKEN": token || "", "X-Requested-With": "XMLHttpRequest" },
    form: {},
  });
  report.mail.push({ action: "manifest_prepare", ok: prepManifest.status() < 500, status: prepManifest.status(), shipmentId: id });
}

async function testShipmentEditTabsAndUpdate(page, report) {
  await page.goto(`${BASE_URL}/shipments`, { waitUntil: "domcontentloaded" });
  const editHref = await page.locator('a[href*="/shipments/edit/"]').first().getAttribute("href");
  if (!editHref) {
    report.crud.push({ module: "shipments", action: "open-edit", ok: false });
    return;
  }
  const id = editHref.split("/").pop();
  await page.goto(editHref, { waitUntil: "domcontentloaded" });

  for (const tab of ["shipment-details", "prices-costs", "customs", "repacking-details", "notes", "milestones"]) {
    const tabBtn = page.locator(`.nav-tab-item[data-target="${tab}"]`).first();
    if ((await tabBtn.count()) > 0) {
      await tabBtn.click();
      const active = await page.locator(`#tab-${tab}.tab-panel.active`).count();
      report.crud.push({ module: "shipments", action: `tab:${tab}`, ok: active > 0, id });
    }
  }

  const detailsTab = page.locator('.nav-tab-item[data-target="shipment-details"]').first();
  if ((await detailsTab.count()) > 0) {
    await detailsTab.click();
  }
  const stampValue = `QA-${Date.now()}`;
  await page.fill('input[name="consignee_att"]', stampValue);
  await page.locator('#shipment-edit-form').evaluate((form) => form.submit());
  await page.waitForLoadState("networkidle");
  const savedValue = await page.locator('input[name="consignee_att"]').inputValue();
  report.crud.push({ module: "shipments", action: "update", ok: savedValue === stampValue, id });
}

async function testStockEditTabsAndUpdate(page, report) {
  await page.goto(`${BASE_URL}/stocks`, { waitUntil: "domcontentloaded" });
  const editHref = await page.locator('a[href*="/stocks/edit/"]').first().getAttribute("href");
  if (!editHref) {
    report.crud.push({ module: "stocks", action: "open-edit", ok: false });
    return;
  }
  const id = editHref.split("/").pop();
  await page.goto(editHref, { waitUntil: "domcontentloaded" });

  for (const tab of ["stock-details", "line-items", "irregularities"]) {
    const tabBtn = page.locator(`.stock-tab[data-tab="${tab}"]`).first();
    if ((await tabBtn.count()) > 0) {
      await tabBtn.click();
      const active = await page.locator(`#${tab}.stock-tab-content.active`).count();
      report.crud.push({ module: "stocks", action: `tab:${tab}`, ok: active > 0, id });
    }
  }

  const detailsTab = page.locator('.stock-tab[data-tab="stock-details"]').first();
  if ((await detailsTab.count()) > 0) {
    await detailsTab.click();
  }
  const stampValue = `QA comment ${Date.now()}`;
  await page.fill('textarea[name="internal_comments"]', stampValue);
  await Promise.all([
    page.waitForLoadState("networkidle"),
    page.click('button[type="submit"].btn-save-custom'),
  ]);
  const updatedComment = await page.locator('textarea[name="internal_comments"]').inputValue();
  report.crud.push({ module: "stocks", action: "update", ok: updatedComment.includes("QA comment"), id });
}

async function main() {
  const browser = await firefox.launch({ headless: true });
  const context = await browser.newContext({
    permissions: ["geolocation"],
    geolocation: { latitude: 19.076, longitude: 72.8777 },
  });
  const page = await context.newPage();

  const report = { pages: [], filters: [], crud: [], mail: [], errors: [] };

  try {
    await login(page);

    const paths = [
      "/Agents",
      "/customers",
      "/hubs",
      "/Suppliers",
      "/other-companies",
      "/offices",
      "/Vessels",
      "/shipments",
      "/stocks",
      "/users",
    ];
    for (const path of paths) {
      await checkPage(page, path, report);
    }

    for (const pageCheck of [
      ["/Agents/create", 'input[name="agent_name"]', "agent-create"],
      ["/customers/create", 'input[name="customer_name"]', "customer-create"],
      ["/hubs/create", 'input[name="hub_name"]', "hub-create"],
      ["/Suppliers/create", 'input[name="supplier_name"]', "supplier-create"],
      ["/other-companies/create", 'input[name="company_name"]', "other-company-create"],
      ["/offices/create", 'input[name="office_name"]', "office-create"],
      ["/Vessels/create", "form", "vessel-create"],
      ["/create-shipment", "form", "shipment-create"],
      ["/stocks/create-crr", "form", "crr-create"],
    ]) {
      await checkVisible(page, pageCheck[0], pageCheck[1], report, pageCheck[2]);
    }

    const filterChecks = [
      ["/Agents", "hide_inactive=1&name=a"],
      ["/hubs", "hide_inactive=1&name=a"],
      ["/Suppliers", "search=test"],
      ["/customers", "search=test"],
      ["/shipments", "shipment_number=MI"],
      ["/stocks", "stock_number=STK"],
      ["/users", "search=admin"],
    ];
    for (const [path, query] of filterChecks) {
      await checkFilters(page, path, query, report);
    }

    for (const fn of [createAgentAndCleanup, testStockEditTabsAndUpdate, testShipmentEditTabsAndUpdate, checkShipmentMailEndpoints]) {
      try {
        await fn(page, report);
      } catch (error) {
        report.errors.push(error.message);
      }
    }
  } catch (error) {
    report.errors.push(error.message);
  } finally {
    await browser.close();
  }

  console.log(JSON.stringify(report, null, 2));
}

main();
