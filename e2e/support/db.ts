import { execFileSync } from 'node:child_process';

/** テスト用DBへSQLを実行し、結果(タブ区切り・見出し無し)を返す。本番DBには接続しない。 */
export function dbQuery(sql: string): string {
  return execFileSync('npx', ['wp-env', 'run', 'cli', 'wp', 'db', 'query', sql, '--skip-column-names'], {
    encoding: 'utf8',
    shell: process.platform === 'win32',
  }).trim();
}

/** SQLを実行し、1行目の1列目を数値で返す。 */
export const dbScalar = (sql: string): number => Number(dbQuery(sql).split('\n')[0].split('\t')[0]);
