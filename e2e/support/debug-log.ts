import { execFileSync } from 'node:child_process';

// CIでは WP_DEBUG_DISPLAY=false で、PHPの警告・エラーをページに出さず、wp-content/debug.log に残す。
// DBエラーや致命的エラーは、ページの文字列ではなく、このログを読んで検出する。
const LOG_PATH = '/var/www/html/wp-content/debug.log';
const SERIOUS = /WordPress database error|PHP Fatal error|PHP Parse error|Table '.*' doesn't exist|Uncaught/;

/** debug.log の全文を返す。ファイルが無ければ空文字。全文はテストの出力に出さない。 */
export function readDebugLog(): string {
  try {
    return execFileSync('npx', ['wp-env', 'run', 'cli', 'cat', LOG_PATH], {
      encoding: 'utf8',
      shell: process.platform === 'win32',
      stdio: ['ignore', 'pipe', 'ignore'],
      maxBuffer: 64 * 1024 * 1024,
    });
  } catch {
    return '';
  }
}

/** 直前に読んだログ(before)より後に追加された行から、DBエラー・致命的エラーの行だけを返す。 */
export function newSeriousErrors(before: string, after: string = readDebugLog()): string[] {
  const added = after.startsWith(before) ? after.slice(before.length) : after;
  return added
    .split('\n')
    .filter((l) => SERIOUS.test(l))
    .map((l) => l.trim().slice(0, 1500)); // 末尾の「made by ...」(呼び出し元)まで見えるように、長めに残す
}
