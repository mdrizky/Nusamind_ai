import puppeteer from 'puppeteer-core';
import { spawn } from 'child_process';
import { mkdirSync, writeFileSync } from 'fs';
import { resolve } from 'path';

const CHROME_PATH = '/usr/bin/google-chrome';
const BASE_URL = 'http://127.0.0.1:8000';
const OUT_DIR = resolve('public/screenshots');

mkdirSync(OUT_DIR, { recursive: true });

const USER_CREDENTIALS = { email: 'user@nusamind.test', password: 'password' };
const ADMIN_CREDENTIALS = { email: 'admin@nusamind.test', password: 'password' };

const PAGES = [
  { path: '/login', name: '00-login', desktop: true },
  { path: '/register', name: '01-register', desktop: true },
  { path: '/user/dashboard', name: '02-user-dashboard', desktop: false },
  { path: '/user/features', name: '03-user-features', desktop: false },
  { path: '/user/finance', name: '04-nusafinance', desktop: false },
  { path: '/user/content', name: '05-nusamarketing', desktop: false },
  { path: '/user/content/create', name: '06-content-create', desktop: false },
  { path: '/user/insight', name: '07-nusainsight', desktop: false },
  { path: '/user/reply', name: '08-nusareply', desktop: false },
  { path: '/user/reply/faq', name: '09-nusareply-faq', desktop: false },
  { path: '/user/reply/saved', name: '10-nusareply-saved', desktop: false },
  { path: '/user/stock', name: '11-nusastock', desktop: false },
  { path: '/user/stock/movements', name: '12-nusastock-movements', desktop: false },
  { path: '/user/campaign', name: '13-nusacampaign', desktop: false },
  { path: '/user/loyal', name: '14-nusaloyal', desktop: false },
  { path: '/user/price', name: '15-nusaprice', desktop: false },
  { path: '/user/price/hpp', name: '16-nusaprice-hpp', desktop: false },
  { path: '/user/catalog', name: '17-nusacatalog', desktop: false },
  { path: '/user/global', name: '18-nusaglobal', desktop: false },
  { path: '/user/score', name: '19-nusascore', desktop: false },
  { path: '/user/score/history', name: '20-nusascore-history', desktop: false },
  { path: '/user/coach', name: '21-nusacoach', desktop: false },
  { path: '/user/transactions', name: '22-transactions', desktop: false },
  { path: '/user/business', name: '23-business-profile', desktop: false },
  { path: '/user/profile', name: '24-user-profile', desktop: false },
  { path: '/admin/dashboard', name: '25-admin-dashboard', desktop: true },
  { path: '/admin/users', name: '26-admin-users', desktop: true },
  { path: '/admin/ai-usage', name: '27-admin-ai-usage', desktop: true },
  { path: '/admin/content-reports', name: '28-admin-reports', desktop: true },
  { path: '/admin/categories', name: '29-admin-categories', desktop: true },
  { path: '/admin/notifications', name: '30-admin-notifications', desktop: true },
];

function sleep(ms) {
  return new Promise(r => setTimeout(r, ms));
}

let serverProcess = null;
function startServer() {
  return new Promise((resolvePromise) => {
    serverProcess = spawn('php', ['artisan', 'serve', '--port=8000', '--host=127.0.0.1'], {
      cwd: process.cwd(),
      env: { ...process.env, PHP_INI_SCAN_DIR: '/etc/php/8.3/cli/conf.d:/home/daffarizky/php_conf' },
      stdio: 'pipe',
    });
    let output = '';
    serverProcess.stdout.on('data', (data) => {
      output += data.toString();
      if (output.includes('Development Server')) {
        resolvePromise();
      }
    });
    serverProcess.stderr.on('data', (data) => {
      if (data.toString().includes('started')) {
        resolvePromise();
      }
    });
    setTimeout(() => resolvePromise(), 3000);
  });
}

async function takeScreenshots() {
  console.log('Starting Laravel dev server...');
  await startServer();
  await sleep(2000);

  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu'],
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 390, height: 844 });

  // Log in as user first
  console.log('Logging in as user...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
  await page.type('input[name="email"]', USER_CREDENTIALS.email);
  await page.type('input[name="password"]', USER_CREDENTIALS.password);
  await page.click('button[type="submit"]');
  await page.waitForNavigation({ waitUntil: 'networkidle2' });
  console.log('User logged in');

  // Take user mobile screenshots
  for (const p of PAGES) {
    if (p.desktop) continue;
    await takeSingleScreenshot(page, p.path, p.name);
  }

  // Log in as admin via separate browser context
  console.log('Logging in as admin...');
  const adminContext = await browser.createBrowserContext();
  const adminPage = await adminContext.newPage();
  await adminPage.setViewport({ width: 1280, height: 800 });
  await adminPage.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
  await adminPage.type('input[name="email"]', ADMIN_CREDENTIALS.email);
  await adminPage.type('input[name="password"]', ADMIN_CREDENTIALS.password);
  await adminPage.click('button[type="submit"]');
  await adminPage.waitForNavigation({ waitUntil: 'networkidle2' });
  console.log('Admin logged in');

  for (const p of PAGES) {
    if (!p.desktop) continue;
    if (p.path.startsWith('/admin/')) {
      await takeSingleScreenshot(adminPage, p.path, p.name);
    }
  }

  // Also take desktop screenshots of some key user pages
  const desktopPage = await browser.newPage();
  await desktopPage.setViewport({ width: 1280, height: 800 });
  for (const p of PAGES) {
    if (p.desktop) continue;
    if (['02-user-dashboard', '03-user-features', '04-nusafinance', '05-nusamarketing'].includes(p.name)) {
      await takeSingleScreenshot(desktopPage, p.path, `${p.name}-desktop`);
    }
  }

  await adminContext.close();
  await browser.close();
  if (serverProcess) serverProcess.kill();
  console.log('All screenshots done!');
}

async function takeSingleScreenshot(page, path, name) {
  try {
    const url = `${BASE_URL}${path}`;
    console.log(`  📸 ${name}...`);
    await page.goto(url, { waitUntil: 'networkidle2', timeout: 15000 });
    await sleep(1000);
    await page.screenshot({
      path: `${OUT_DIR}/${name}.png`,
      fullPage: false,
    });
    console.log(`  ✅ ${name}`);
  } catch (e) {
    console.error(`  ❌ ${name}: ${e.message}`);
    // Save error screenshot
    try {
      await page.screenshot({ path: `${OUT_DIR}/${name}-error.png` });
    } catch (_) {}
  }
}

takeScreenshots().catch(console.error);
