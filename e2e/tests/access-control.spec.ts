import { test, expect } from '../support/test';
import { ROLES, storageStatePath } from '../support/users';
import { ALLOWED_PAGES, deniedPages } from '../support/access-matrix';

// WordPress が権限の無いページに出す拒否画面(wp_die)を判定する。
const DENIED_TEXT = /このページにアクセスする権限がありません|Sorry, you are not allowed to access this page/;

for (const role of ROLES) {
  test.describe(`${role} のアクセス制御`, () => {
    test.use({ storageState: storageStatePath(role) });

    for (const slug of ALLOWED_PAGES[role]) {
      test(`許可: ${slug} にアクセスできる`, async ({ page }) => {
        const res = await page.goto(`/wp-admin/admin.php?page=${slug}`);
        expect(res?.status()).not.toBe(403);
        await expect(page.locator('body')).not.toContainText(DENIED_TEXT);
      });
    }

    for (const slug of deniedPages(role)) {
      test(`拒否: ${slug} にアクセスできない`, async ({ page }) => {
        const res = await page.goto(`/wp-admin/admin.php?page=${slug}`);
        const denied = res?.status() === 403 || (await page.locator('body').innerText()).match(DENIED_TEXT);
        expect(denied).toBeTruthy();
      });
    }
  });
}
