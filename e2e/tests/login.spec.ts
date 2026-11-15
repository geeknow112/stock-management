import { test, expect } from '@playwright/test';
import { ROLES, storageStatePath } from '../support/users';

for (const role of ROLES) {
  test.describe(`${role} のログイン状態`, () => {
    test.use({ storageState: storageStatePath(role) });

    test('管理画面に入れる', async ({ page }) => {
      await page.goto('/wp-admin/');
      await expect(page.locator('body.wp-admin')).toBeVisible();
      await expect(page.locator('#wp-admin-bar-my-account')).toBeVisible();
    });
  });
}
