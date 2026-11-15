import { test, expect, type Page } from '@playwright/test';
import { storageStatePath } from '../support/users';

// 配送予定表(delivery-graph)の表示と日付検索のテスト。
// サンプルデータ(e2e/fixtures/data.sql)の受注は 2026-11-16 / 11-17 / 11-18 の配送予定日。
// 過去に、日付検索ボタン(cmd_search)が、JSファイル内のBladeテンプレート構文が展開されず、
// 不正なURLへ遷移する不具合があった。その再発を防ぐテストを含む。
const FATAL = /Fatal error|Parse error|WordPress database error|Table '.*' doesn't exist|Uncaught/;
const BASE = '/wp-admin/admin.php?page=delivery-graph';

// 日付ごとの行の見出しセル(先頭列)。
const dateCell = (page: Page, date: string) =>
  page.locator('#the-list td._sticky', { hasText: date }).first();

test.describe('配送予定表', () => {
  test.use({ storageState: storageStatePath('administrator') });

  test('開始日を指定すると、その日から日付ごとの行が並ぶ', async ({ page }) => {
    await page.goto(`${BASE}&action=search&cmd=search&s[sdt]=2026-11-16`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    // このプラグインの画面は、WordPress標準の空の #the-list と、配送予定表本体の #the-list の2つを含むため、日付の行を持つほうに絞る。
    await expect(page.locator('#the-list', { hasText: '2026-11-16' }).first()).toBeVisible();
    await expect(dateCell(page, '2026-11-16')).toBeVisible();
    await expect(dateCell(page, '2026-11-17')).toBeVisible();
    await expect(dateCell(page, '2026-11-18')).toBeVisible();
  });

  test('開始日の入力欄に、指定した日付が残る', async ({ page }) => {
    await page.goto(`${BASE}&action=search&cmd=search&s[sdt]=2026-11-17`);
    await expect(page.locator('input[name="s[sdt]"]')).toHaveValue('2026-11-17');
  });

  test('受注の無い日を開始日にしても、エラー無く表示される', async ({ page }) => {
    await page.goto(`${BASE}&action=search&cmd=search&s[sdt]=2026-02-01`);
    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('#wpbody-content').first()).toBeVisible();
    await expect(page.locator('input[name="s[sdt]"]')).toHaveValue('2026-02-01');
  });

  // 過去の不具合の再発防止: 検索ボタンで、正しいURLへ遷移し、結果が表示されること。
  test('検索ボタンで日付検索が動作する(cmd_searchの再発防止)', async ({ page }) => {
    await page.goto(BASE);
    await page.locator('input[name="s[sdt]"]').fill('2026-11-17');
    await page.locator('#search-submit').click();

    await page.waitForURL(/page=delivery-graph/);
    const url = decodeURIComponent(page.url());
    expect(url).toContain('/wp-admin/admin.php');
    expect(url).toContain('s[sdt]=2026-11-17');
    // Bladeの未展開文字列がURLに残っていないこと。
    expect(url).not.toMatch(/\{\{|\}\}|admin_url|SM_ADMIN_URL|SM_FORM_PAGE|undefined/);

    await expect(page.locator('body')).not.toContainText(FATAL);
    await expect(page.locator('input[name="s[sdt]"]')).toHaveValue('2026-11-17');
    await expect(dateCell(page, '2026-11-17')).toBeVisible();
  });

  test('ページ内で、検索用のJS変数が展開されている', async ({ page }) => {
    await page.goto(BASE);
    const vars = await page.evaluate(() => ({
      page: (window as any).SM_FORM_PAGE,
      adminUrl: (window as any).SM_ADMIN_URL,
    }));
    expect(vars.page).toBe('delivery-graph');
    expect(vars.adminUrl).toMatch(/\/wp-admin\/$/);
  });
});
