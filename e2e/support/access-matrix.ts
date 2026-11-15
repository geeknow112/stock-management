import type { Role } from './users';

// stock-management.php の add_sub_menu() に基づく、ロール別のアクセス可否。
// 値は管理画面の ?page= のスラッグ。
export const ALL_PAGES = [
  'goods-detail',
  'customer-detail',
  'sales-detail',
  'stock-bulk',
  'stock-transfer',
  'goods-list',
  'customer-list',
  'sales-list',
  'stock-list',
  'stock-receive',
  'delivery-graph',
  'delivery-graph-stg',
  'stock-export',
  'stock-export-day',
  'sales-summary',
] as const;

export const ALLOWED_PAGES: Record<Role, readonly string[]> = {
  administrator: ALL_PAGES,
  editor: ['sales-detail', 'sales-list', 'delivery-graph', 'delivery-graph-stg', 'sales-summary'],
  subscriber: ['delivery-graph', 'delivery-graph-stg'],
};

export const deniedPages = (role: Role) =>
  ALL_PAGES.filter((p) => !ALLOWED_PAGES[role].includes(p));
