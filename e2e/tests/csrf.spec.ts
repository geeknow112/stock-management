import { test, expect } from '../support/test';
import { storageStatePath } from '../support/users';

// CSRF対策(nonce)の確認。ログイン済みの管理者のCookieで、POSTを送る。
// ブラウザは、罠のページからのリクエストにも、Cookieを自動で付ける。ここでは、その状態を、request で再現する。
test.use({ storageState: storageStatePath('administrator') });

const PAGE = '/wp-admin/admin.php?page=goods-list';

test.describe('CSRF対策(nonce)', () => {
  test('nonceなしのPOSTは、拒否される', async ({ page }) => {
    const res = await page.request.post(PAGE, { form: { cmd_search: '1' } });
    expect(res.status()).toBe(403);
  });

  test('でたらめなnonceのPOSTは、拒否される', async ({ page }) => {
    const res = await page.request.post(PAGE, { form: { cmd_search: '1', sm_nonce: 'abcdef1234' } });
    expect(res.status()).toBe(403);
  });

  test('画面に出たnonceを付けたPOSTは、受け付けられる', async ({ page }) => {
    await page.goto(PAGE);
    const nonce = await page.locator('input[name="sm_nonce"]').first().getAttribute('value');
    expect(nonce).toBeTruthy();
    const res = await page.request.post(PAGE, { form: { cmd_search: '1', sm_nonce: nonce! } });
    expect(res.status()).not.toBe(403);
  });

  test('固定値だったnonce(5647b2c250)は、もう使えない', async ({ page }) => {
    const res = await page.request.post(PAGE, { form: { cmd_search: '1', sm_nonce: '5647b2c250', _wpnonce: '5647b2c250' } });
    expect(res.status()).toBe(403);
  });
});
