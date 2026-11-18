import { test as base, expect } from '@playwright/test';
import { newSeriousErrors, readDebugLog } from './debug-log';

// 全テストで使う test。各テストの終了時に debug.log を確認し、
// DBエラーや致命的エラーが出ていたらそのテストを失敗にする(警告・注意は対象外)。
export const test = base.extend<{ debugLogGuard: void }>({
  debugLogGuard: [
    async ({}, use) => {
      const before = readDebugLog();
      await use();
      const errors = newSeriousErrors(before);
      expect(errors, `debug.log にDBエラー/致命的エラーが出ています:\n${errors.join('\n')}`).toEqual([]);
    },
    { auto: true },
  ],
});

export { expect };
