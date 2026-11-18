import { test, expect } from '../support/test';
import { ROLES, storageStatePath } from '../support/users';
import { ALLOWED_PAGES, deniedPages } from '../support/access-matrix';

// 一時的に除外: 次のページは、開くとSQLの構文エラーが debug.log に出る(アプリ本体の不具合。#1440)。直ったら、ここから外す。
const KNOWN_BROKEN = new Set(['administrator:stock-export-day', 'administrator:sales-summary', 'editor:sales-summary']);

// WordPress が権限の無いページに出す拒否画面(wp_die)を判定する。
const DENIED_TEXT = /このページにアクセスする権限がありません|Sorry, you are not allowed to access this page/;

for (const role of ROLES) {
  test.describe(`${role} のアクセス制御`, () => {
    test.use({ storageState: storageStatePath(role) });

    for (const slug of ALLOWED_PAGES[role]) {
      // 既知の不具合(KNOWN_BROKEN)のページは、一時的に除外する。
      const run = KNOWN_BROKEN.has(`${role}:${slug}`) ? test.fixme : test;
      run(`許可: ${slug} にアクセスできる`, async ({ page }) => {
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
