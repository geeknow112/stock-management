// テスト専用のアカウント。本番の認証情報は使わない。
export const ROLES = ['administrator', 'editor', 'subscriber'] as const;
export type Role = (typeof ROLES)[number];

export const TEST_USERS: Record<Role, { login: string; password: string; email: string }> = {
  administrator: { login: 'admin', password: 'password', email: 'wordpress@example.com' },
  editor: { login: 'e2e_editor', password: 'e2e-password', email: 'e2e_editor@example.com' },
  subscriber: { login: 'e2e_subscriber', password: 'e2e-password', email: 'e2e_subscriber@example.com' },
};

export const storageStatePath = (role: Role) => `e2e/auth/${role}.json`;
