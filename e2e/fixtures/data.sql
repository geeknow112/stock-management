-- E2E用の架空のサンプルデータ。実在の人名・取引先名・住所は含まない。
-- 主キーを固定した REPLACE INTO なので、何度実行しても同じ結果になる。
SET NAMES utf8mb4;

REPLACE INTO yc_goods (goods, name, qty, separately_fg, remark, rgdt) VALUES
  (1, 'テスト商品A', '500', 0, NULL, '2026-01-01 00:00:00'),
  (2, 'テスト商品B', '500', 1, NULL, '2026-01-01 00:00:00');

REPLACE INTO yc_customer (customer, name, rgdt) VALUES
  (1, 'テスト顧客X', '2026-01-01 00:00:00'),
  (2, 'テスト顧客Y', '2026-01-01 00:00:00');

REPLACE INTO yc_customer_detail (customer, detail, tank, rgdt) VALUES
  (1, 1, 'タンクX-1', '2026-01-01 00:00:00'),
  (1, 2, 'タンクX-2', '2026-01-01 00:00:00'),
  (2, 1, 'タンクY-1', '2026-01-01 00:00:00');

REPLACE INTO yc_customer_goods (customer, goods, rgdt) VALUES
  (1, 1, '2026-01-01 00:00:00'),
  (1, 2, '2026-01-01 00:00:00'),
  (2, 1, '2026-01-01 00:00:00');

REPLACE INTO yc_sales (sales, class, customer, name, goods, qty, cars_tank, outgoing_warehouse, ship_addr, delivery_dt, arrival_dt, status, lot_fg, receipt_fg, repeat_fg, use_stock, remark, rgdt) VALUES
  (1, 1, 1, 'テスト顧客X', 1, 1.0, '1', '1', '1', '2026-11-16', '2026-11-16', 0, 0, 0, 0, 0, NULL, '2026-11-01 00:00:00'),
  (2, 1, 2, 'テスト顧客Y', 2, 2.0, '1', '1', '1', '2026-11-17', '2026-11-17', 1, 1, 0, 0, 0, NULL, '2026-11-01 00:00:00'),
  (3, 1, 1, 'テスト顧客X', 1, 0.5, '1', '2', '2', '2026-11-18', '2026-11-18', 0, 0, 0, 1, 0, NULL, '2026-11-01 00:00:00');

REPLACE INTO yc_schedule_repeat (`repeat`, sales, period, span, week, repeat_s_dt, repeat_e_dt, rgdt) VALUES
  (1, 3, 1, 1, '1,3', '2026-11-01', '2026-12-31', '2026-11-01 00:00:00');

REPLACE INTO yc_goods_detail (id, sales, goods, lot, barcode, tank, rgdt) VALUES
  (1, 2, 2, NULL, NULL, NULL, '2026-11-01 00:00:00'),
  (2, 2, 2, NULL, NULL, NULL, '2026-11-01 00:00:00'),
  (3, 2, 2, NULL, NULL, NULL, '2026-11-01 00:00:00'),
  (4, 2, 2, NULL, NULL, NULL, '2026-11-01 00:00:00');

REPLACE INTO yc_stock (stock, goods, arrival_dt, warehouse, goods_total, rgdt) VALUES
  (1, 1, '2026-11-10', '1', 2, '2026-11-10 00:00:00'),
  (2, 2, '2026-11-10', '2', 1, '2026-11-10 00:00:00');

REPLACE INTO yc_stock_detail (id, stock, lot, barcode, transfer_fg, rgdt) VALUES
  (1, 1, NULL, NULL, 0, '2026-11-10 00:00:00'),
  (2, 1, NULL, NULL, 0, '2026-11-10 00:00:00'),
  (3, 2, NULL, NULL, 0, '2026-11-10 00:00:00');
