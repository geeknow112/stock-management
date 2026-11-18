import { test, expect } from '../support/test';
import { storageStatePath } from '../support/users';

// スキーマとサンプルデータを投入した状態で、主要画面がエラー無く開くことを確認する。
const PAGES = ['delivery-graph', 'sales-list', 'goods-list', 'customer-list', 'stock-list'];

test.describe('サンプルデータ投入後の画面表示', () => {
  test.use({ storageState: storageStatePath('administrator') });

  for (const slug of PAGES) {
    test(`${slug} がエラー無く開く`, async ({ page }) => {
      const res = await page.goto(`/wp-admin/admin.php?page=${slug}`);
      expect(res?.status()).toBeLessThan(400);
      // プラグインの出力に #wpbody-content が入れ子で含まれ、2要素に一致するため、最初の1つだけを見る。
      await expect(page.locator('#wpbody-content').first()).toBeVisible();
    });
  }
});
