import { test, expect } from '@playwright/test';
import { storageStatePath } from '../support/users';
import { dbQuery, dbScalar } from '../support/db';

// 在庫管理・顧客管理のテスト。
// 共通のサンプルデータ(customer=1,2 / stock=1,2)は変更せず、このテスト専用のデータ(customer=201〜、名前に E2E を含む)を作って使う。
const FATAL = /Fatal error|Parse error|WordPress database error|Table '.*' doesn't exist|Uncaught/;
const ADMIN = '/wp-admin/admin.php';
const NEW_NAME = 'E2E新規顧客';
const EDIT_ID = 201;
const EDIT_NAME_BEFORE = 'E2E編集前顧客';
const EDIT_NAME_AFTER = 'E2E編集後顧客';
const BULK_DATE = '2027-02-10'; // 一括登録テストで使う入庫予定日(他のテストと重ならない日)

const cleanup = () => {
  dbQuery(`DELETE FROM yc_customer_detail WHERE customer = ${EDIT_ID} OR customer IN (SELECT customer FROM (SELECT customer FROM yc_customer WHERE name = '${NEW_NAME}') t)`);
  dbQuery(`DELETE FROM yc_customer WHERE customer = ${EDIT_ID} OR name = '${NEW_NAME}'`);
  dbQuery(`DELETE FROM yc_stock_detail WHERE stock IN (SELECT stock FROM (SELECT stock FROM yc_stock WHERE arrival_dt = '${BULK_DATE}') t)`);
  dbQuery(`DELETE FROM yc_stock WHERE arrival_dt = '${BULK_DATE}'`);
};

test.describe.configure({ mode: 'serial' });

test.describe('在庫管理・顧客管理', () => {
  test.use({ storageState: storageStatePath('administrator') });

  test.beforeAll(() => {
    cleanup();
    dbQuery(`REPLACE INTO yc_customer (customer, name, rgdt) VALUES (${EDIT_ID}, '${EDIT_NAME_BEFORE}', '2026-11-01 00:00:00')`);
    dbQuery(`REPLACE INTO yc_customer_detail (customer, detail, tank, rgdt) VALUES (${EDIT_ID}, 1, 'E2Eタンク-1', '2026-11-01 00:00:00')`);
  });

  test.afterAll(() => {
    cleanup();
  });

  test('在庫一覧が表示され、サンプルの在庫が出る', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-list`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('#goods_name')).toBeVisible();
    // 共通のサンプルデータ(stock=1,2)のリンクが一覧にある。
    await expect(page.locator('a[href*="page=stock-detail"][href*="stock=1&"]').first()).toBeVisible();
  });

  test('在庫一覧を入庫日で検索すると、範囲外の在庫は出ない', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-list&action=search&cmd=search&s[arrival_s_dt]=2030-01-01&s[arrival_e_dt]=2030-12-31`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('a[href*="page=stock-detail"]')).toHaveCount(0);
  });

  test('在庫の登録画面(一括登録)が表示される', async ({ page }) => {
    await page.goto(`${ADMIN}?page=stock-bulk`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('#arrival_dt')).toBeVisible();
    await expect(page.locator('#outgoing_warehouse')).toBeVisible();
    // 明細は20行ぶん、品名のプルダウンがある。
    await expect(page.locator('select[name="goods_list[]"]')).toHaveCount(20);
  });

  // 一時的に除外: 確認・登録ボタンは画面のJavaScriptに依存し、CIのPHP警告表示で動かない可能性が高い(#1435)。直ったら、test に戻す。
  test.fixme('在庫を一括登録できる(入庫予定日・倉庫・品名・個数 → 確認 → 登録)', async ({ page }) => {
    page.on('dialog', (d) => d.accept());
    await page.goto(`${ADMIN}?page=stock-bulk`);
    await page.locator('#arrival_dt').fill(BULK_DATE);
    await page.locator('#outgoing_warehouse').selectOption('1');
    await page.locator('#goods_0').selectOption('1');
    await page.locator('#qty_0').fill('3');
    await page.locator('#cmd_regist').click(); // 「確認」
    await page.waitForURL(/action=confirm/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    await page.locator('#cmd_regist').click(); // 「登録」
    await page.waitForURL(/action=save/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    expect(dbScalar(`SELECT COUNT(*) FROM yc_stock WHERE arrival_dt = '${BULK_DATE}'`)).toBe(1);
    expect(dbScalar(`SELECT goods_total FROM yc_stock WHERE arrival_dt = '${BULK_DATE}'`)).toBe(3);
  });

  test('顧客一覧が表示される', async ({ page }) => {
    await page.goto(`${ADMIN}?page=customer-list`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('#customer_name')).toBeVisible();
  });

  test('顧客の編集画面に、登録済みの顧客名と配送先(タンク)が入っている', async ({ page }) => {
    await page.goto(`${ADMIN}?page=customer-detail&customer=${EDIT_ID}&action=edit`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('#customer')).toHaveValue(String(EDIT_ID));
    await expect(page.locator('#customer_name')).toHaveValue(EDIT_NAME_BEFORE);
    await expect(page.locator('#tank_0')).toHaveValue('E2Eタンク-1');
  });

  // 一時的に除外: 確認・登録ボタンは画面のJavaScriptに依存し、CIのPHP警告表示で動かない可能性が高い(#1435)。直ったら、test に戻す。
  test.fixme('顧客を新規登録できる(顧客名・タンク → 確認 → 登録)', async ({ page }) => {
    page.on('dialog', (d) => d.accept());
    await page.goto(`${ADMIN}?page=customer-detail`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await page.locator('#customer_name').fill(NEW_NAME);
    await page.locator('#tank_0').fill('E2Eタンク-N');
    await page.locator('#cmd_regist').click(); // 「確認」
    await page.waitForURL(/action=confirm/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    await page.locator('#cmd_regist').click(); // 「登録」
    await page.waitForURL(/action=save/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    expect(dbScalar(`SELECT COUNT(*) FROM yc_customer WHERE name = '${NEW_NAME}'`)).toBe(1);
    expect(dbScalar(`SELECT COUNT(*) FROM yc_customer_detail d JOIN yc_customer c ON c.customer = d.customer WHERE c.name = '${NEW_NAME}'`)).toBe(1);
  });

  // 一時的に除外: 確認・更新ボタンは画面のJavaScriptに依存し、CIのPHP警告表示で動かない可能性が高い(#1435)。直ったら、test に戻す。
  test.fixme('顧客名を編集して更新できる', async ({ page }) => {
    page.on('dialog', (d) => d.accept());
    await page.goto(`${ADMIN}?page=customer-detail&customer=${EDIT_ID}&action=edit`);
    await page.locator('#customer_name').fill(EDIT_NAME_AFTER);
    await page.locator('#cmd_regist').click(); // 「確認」
    await page.waitForURL(/action=confirm/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    await page.locator('#cmd_update').click(); // 「更新」
    await page.waitForURL(/action=edit-exe/);
    await expect(page.locator('body')).not.toContainText(FATAL);

    expect(dbQuery(`SELECT name FROM yc_customer WHERE customer = ${EDIT_ID}`)).toBe(EDIT_NAME_AFTER);
  });
});
