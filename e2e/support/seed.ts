import { execFileSync } from 'node:child_process';
import { ROLES, TEST_USERS } from './users';

const wp = (...args: string[]) =>
  execFileSync('npx', ['wp-env', 'run', 'cli', 'wp', ...args], {
    encoding: 'utf8',
    shell: process.platform === 'win32',
  }).trim();

const userExists = (login: string) => {
  try {
    wp('user', 'get', login, '--field=ID');
    return true;
  } catch {
    return false;
  }
};

/** 3ロールのテストユーザーを用意する。何度実行しても同じ状態になる(冪等)。 */
export function seedUsers() {
  for (const role of ROLES) {
    const u = TEST_USERS[role];
    if (userExists(u.login)) {
      wp('user', 'update', u.login, `--user_pass=${u.password}`, `--role=${role}`);
    } else {
      wp('user', 'create', u.login, u.email, `--role=${role}`, `--user_pass=${u.password}`);
    }
  }
}

/** テストデータを初期化して投入し直す。ユーザーは維持し、このプラグインのデータだけ消す。 */
export function resetTestData() {
  // プラグインのテーブル名は接頭辞 *_sm_ 等で始まる想定。存在するものだけ空にする。
  const tables = wp('db', 'query', "SHOW TABLES LIKE '%stock%'", '--skip-column-names')
    .split('\n')
    .map((t) => t.trim())
    .filter(Boolean);
  for (const t of tables) wp('db', 'query', `TRUNCATE TABLE \`${t}\``);
  seedUsers();
}

if (require.main === module) {
  if (process.argv[2] === 'reset') resetTestData();
  else seedUsers();
  console.log('E2E test data ready');
}
