import { chromium, FullConfig } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { ROLES, TEST_USERS, storageStatePath } from './support/users';
import { seedUsers } from './support/seed';

/** 3ロールでログインし、ログイン状態(storageState)を保存する。 */
export default async function globalSetup(config: FullConfig) {
  seedUsers();
  mkdirSync('e2e/auth', { recursive: true });
  const baseURL = config.projects[0].use.baseURL ?? 'http://localhost:8888';
  const browser = await chromium.launch();
  for (const role of ROLES) {
    const u = TEST_USERS[role];
    const context = await browser.newContext({ baseURL });
    const page = await context.newPage();
    await page.goto('/wp-login.php');
    await page.fill('#user_login', u.login);
    await page.fill('#user_pass', u.password);
    await page.click('#wp-submit');
    await page.waitForURL(/wp-admin/);
    await context.storageState({ path: storageStatePath(role) });
    await context.close();
  }
  await browser.close();
}
