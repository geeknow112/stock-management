import { test, expect } from '../support/test';
import { storageStatePath } from '../support/users';

// 帳票(在庫証明書・倉出伝票)のテスト。
// これらの帳票はPDF/CSVではなく、管理画面のHTML表示(印刷・コピー用)なので、画面に出た表の中身を確認する。
// 共通のサンプルデータ(goods=1,2 / customer=1,2 / sales=1,2 / stock=1,2)を読むだけで、変更しない。
const ADMIN = '/wp-admin/admin.php';

test.describe('帳票出力', () => {
  test.use({ storageState: storageStatePath('administrator') });

  test('在庫証明書: 検索すると、在庫のある商品が、荷姿・個数・数量つきで出る', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-export&action=search&cmd=search&s[arrival_e_dt]=2026-12-31`);
    await expect(page.getByRole('heading', { name: '【在庫証明書】' })).toBeVisible();
    // 見出し
    for (const h of ['製品名', '荷姿・容量', '個数', '数量(kg)', '備考']) {
      await expect(page.locator('th', { hasText: h }).first()).toBeVisible();
    }
    // 商品A(在庫2個、配送済みの受注なし): 500kgTB、個数2、数量1,000kg
    const row = page.locator('tr', { hasText: 'テスト商品A' }).first();
    await expect(row).toContainText('500kgTB');
    await expect(row.locator('td').nth(2)).toHaveText(/^\s*2\s*$/);
    await expect(row.locator('td').nth(3)).toHaveText(/^\s*1,000\s*$/);
    // 合計行
    await expect(page.locator('th', { hasText: '合計' }).first()).toBeVisible();
  });

  test('在庫証明書: 入庫日が在庫より前だと、「検索対象は見つかりませんでした」になる', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-export&action=search&cmd=search&s[arrival_e_dt]=2020-01-01`);
    await expect(page.locator('body')).toContainText('検索対象は見つかりませんでした');
    await expect(page.locator('tr', { hasText: 'テスト商品A' })).toHaveCount(0);
  });

  test('在庫証明書: 印刷・コピーのボタンがある', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-export`);
    await expect(page.locator('#btn_print')).toBeVisible();
    await expect(page.locator('#btn_copy')).toBeVisible();
  });

  test('倉出伝票: 配送日を指定すると、その日の受注が、品名・容量・量目・顧客名つきで出る', async ({ page }) => {
    // sales=2: 2026-11-17 / テスト商品B(バラ) / 2.0t / テスト顧客Y
    await page.goto(`${ADMIN}?page=stock-export-day&action=search&cmd=search&s[delivery_s_dt]=2026-11-17`);
    await expect(page.getByRole('heading', { name: '【倉出伝票】' })).toBeVisible();
    const row = page.locator('tr', { hasText: 'テスト顧客Y' }).first();
    await expect(row).toContainText('テスト商品B');
    await expect(row).toContainText('（B）');
    await expect(row).toContainText('2.0');
    // 別の日の受注(テスト顧客X)は出ない。
    await expect(page.locator('tr', { hasText: 'テスト顧客X' })).toHaveCount(0);
    // 運送会社の欄
    await expect(page.locator('th', { hasText: '運送会社' }).first()).toBeVisible();
  });

  test('倉出伝票: 別の配送日を指定すると、その日の受注に切り替わる', async ({ page }) => {
    // sales=1: 2026-11-16 / テスト商品A(TB) / 1.0t / テスト顧客X
    await page.goto(`${ADMIN}?page=stock-export-day&action=search&cmd=search&s[delivery_s_dt]=2026-11-16`);
    const row = page.locator('tr', { hasText: 'テスト顧客X' }).first();
    await expect(row).toContainText('テスト商品A');
    await expect(row).toContainText('（T）');
    await expect(row).toContainText('1.0');
    await expect(page.locator('tr', { hasText: 'テスト顧客Y' })).toHaveCount(0);
  });

  test('倉出伝票: 受注の無い日を指定すると、明細が出ない', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-export-day&action=search&cmd=search&s[delivery_s_dt]=2030-01-01`);
    await expect(page.locator('tr', { hasText: 'テスト顧客' })).toHaveCount(0);
  });
});
