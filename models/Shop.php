<?php
class Shops {
	protected $_name = 'shop';

	/**
	 *
	 **/
	function getTableName() {
		return $this->_name;
	}

	/**
	 *
	 **/
	public function getCurUser() {
                $cur_user = wp_get_current_user();
                return $cur_user;
	}

	/**
	 *
	 **/
	public function getPrm() {
		global $wpdb;
		$cur_user = wp_get_current_user();
//		$this->vd($cur_user->user_login);
//		$this->vd($cur_user->user_email);

		// your_name, your_emailで検索してIDを取得するSQL
		$rows = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_field_your-email'");
//		$this->vd($rows[count($rows) -1]);
		foreach ($rows as $i => $v) {
		        $mails[$v->meta_value]['post_id'] = $v->post_id;
		}
		//var_dump($mails['test@test.com']['post_id']);
		$post_id = $mails[$cur_user->user_email]['post_id'];
//		$this->vd($post_id);

		$prm->post = $post_id;
		return $prm;

	}

	public function getValidElement() {
		return array(
			'rules' => array(
			    'name'                  => 'required|max:2',
			    'email'                 => 'required|email',
			    'password'              => 'required|min:6',
			    'confirm_password'      => 'required|same:password',
			    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
			    'skills'                => 'array',
			    'skills.*.id'           => 'required|numeric',
			    'skills.*.percentage'   => 'required|numeric'
			), 
			'messages' => array(
				'name.required' => 'ユーザー名を入力してください',
				'name.string' => '正しい形式で入力してください',
				'name.max' => '文字数をオーバーしています。',
				'email.required' => 'メールアドレスを入力してください。',
				'email.email' => '正しい形式でメールアドレスを入力してください',
				'email.max' => '文字数をオーバーしています。',
				'email.unique' => '登録済みのユーザーです',
				'password.required' => 'パスワードを入力してください',
				'password.min' => 'パスワードは8文字以上で入力してください。',
				'password.confirmed' => 'パスワードが一致しません。',
			)
		);
//		return 'getValidElement';
	}

	public function getHeaderByServiceType($serviceType = null) {
		$header = array(
			'veritrans' => array(
				'NO.', 
				'法人個人の区分', 
				'法人番号', 
				'法人名', 
				'法人名（フリガナ）', 
				'会社URL', 
				'事業内容', 
				'上場区分', 
				'会社設立年月日', 
				'資本金（万円）', 
				'本社：郵便番号', 
				'本社：住所（都道府県）', 
				'本社：住所（市区群）', 
				'本社：住所（町名番地）', 
				'本社：住所（ビル名）', 
				'本社：住所（フリガナ）', 
				'本社：電話番号', 
				'代表者名', 
				'代表者名（フリガナ）', 
				'代表者生年月日', 
				'代表者：郵便番号', 
				'代表者：住所（都道府県）', 
				'代表者：住所（市区群）', 
				'代表者：住所（町名番地）', 
				'代表者：住所（ビル名）', 
				'代表者：住所（フリガナ）', 
				'代表者：電話番号', 
				'加盟店舗名', 
				'加盟店舗名（フリガナ）', 
				'加盟店舗名英字', 
				'販売形態', 
				'取扱商品/サービス', 
				'取扱商材', 
				'商品価格帯', 
				'サイト種別', 
				'サイトURL', 
				'サイト区分', 
				'公開予定日', 
				'想定取引件数(月間)(件)', 
				'最大単価', 
				'想定平均単価', 
				'店舗：郵便番号', 
				'店舗：住所（都道府県）', 
				'店舗：住所（市区群）', 
				'店舗：住所（町名番地）', 
				'店舗：住所（ビル名）', 
				'店舗：住所（フリガナ）', 
				'消費者問い合わせ窓口名', 
				'消費者問い合わせ電話番号', 
				'消費者問い合わせメールアドレス', 
				'消費者問い合わせ受付時間(開始時間）', 
				'消費者問い合わせ受付時間(終了時間）', 
				'運用担当者名（姓）', 
				'運用担当者名（名）', 
				'運用担当者名（フリガナ）', 
				'運用担当者電話番号', 
				'運用担当者メールアドレス', 
				'販売業者名', 
				'運営統括責任者', 
				'商品以外の必要料金', 
				'送料', 
				'不良品の取扱', 
				'不良品の取扱詳細', 
				'引渡し時期(国内)：在庫ある場合', 
				'引渡し時期(国内)：在庫ない場合', 
				'販売数量', 
				'返品について', 
				'返品について詳細', 
				'返品期限', 
				'訪問販売(有・無)', 
				'電話勧誘販売(有・無)', 
				'連鎖販売取引(有・無)', 
				'業務提供誘引販売(有・無)', 
				'カード情報保持状況', 
				'非保持化予定年月', 
				'PCIDSS準拠状況', 
				'PCIDSS準拠予定年月', 
				'本人認証サービス実施状況', 
				'本人認証サービス実施予定年月', 
				'セキュリティコード実施状況', 
				'セキュリティコード実施予定年月', 
				'不正配送先情報活用状況', 
				'不正配送先情報活用実施予定年月', 
				'属性・行動分析実施状況', 
				'属性・行動分析実施予定年月', 
				'その他独自対策', 
				'その他独自対策実施予定年月', 
				'その他独自対策の詳細', 
				'未敗訴チェック', 
				'金融機関名', 
				'金融機関コード', 
				'支店名', 
				'支店コード', 
				'口座種別', 
				'口座番号', 
				'口座名義', 
				'口座名義カナ'
			), 
			'mf-kessai' => array(	//00P-000158,株式会社フォーミックス,03-5715-5551,140-0002,東京都,品川区東品川3-32-42ISビル12F,,,,,,smrj_4mixinner@fourmix.co.jp,,,,,,,,,,1000000,,,0
				'顧客番号', 
				'顧客名', 
				'電話番号', 
				'郵便番号', 
				'都道府県', 
				'住所1', 
				'住所2', 
				'部署名', 
				'担当者肩書', 
				'担当者名', 
				'担当者名（カナ）', 
				'メールアドレス', 
				'CCメールアドレス1', 
				'CCメールアドレス2', 
				'CCメールアドレス3', 
				'CCメールアドレス4', 
				'事業所区分', 
				'法人番号', 
				'代表者名', 
				'顧客の主なサービス、商材', 
				'顧客Webサイト', 
				'希望与信額', 
				'初月希望与信額', 
				'その他情報', 
				'口振依頼書送付'
			)
		);
		return $header[$serviceType];
	}

	/**
	 *
	 **/
	private function getPostIds($s = null) {
		$p = (object) $s;

		global $wpdb;
		$cur_user = wp_get_current_user();
		
		$sql  = "SELECT p.ID FROM ".$wpdb->prefix."posts as p ";
		$sql .= "WHERE p.post_type = 'flamingo_inbound' ";
		if ($p->post) {
			$sql .= sprintf("AND p.ID = '%s' ", $p->post);
		}
		if ($p->no) {
			$sql .= sprintf("AND p.ID = '%s' ", $p->no);
		}
		if ($p->sdt) {
			$sql .= sprintf("AND p.post_date >= '%s' ", $p->sdt);
		}
		if ($p->sdt) {
			$sql .= sprintf("AND p.post_date < '%s' ", $p->edt);
		}
		$sql .= ";";

		$pids = $wpdb->get_results($sql);
		return $pids;
	}

	/**
	 *
	 **/
	private function getMetaDataByPids($pids = null, $s = null) {
		global $wpdb;
		$cur_user = wp_get_current_user();

		// 検索条件にマッチするpost_idを抽出
		foreach ($pids as $i => $pid) {
			if ($s['company-name']) {
				if (current(get_post_meta($pid, '_field_company-name')) != $s['company-name']) {
					unset($pids[$i]);
				}
			}
		}

		$sql  = "SELECT pm.* FROM ".$wpdb->prefix."postmeta as pm ";
		$sql .= "WHERE pm.post_id IN ('". implode("', '", $pids). "') ";
		$sql .= ";";
		$metas = $wpdb->get_results($sql);

		return $metas;
	}

	/**
	 *
	 **/
	public function getShopListFromPostmeta($s = null, $serviceType = null) {
		// 対象のpost_id を取得
		$pidArr = $this->getPostIds($s);

		// 配列整形
		foreach ($pidArr as $i => $d) {
			$pids[] = $d->ID;
		}

		// post_idからpostmetaを取得
		$metaArr = $this->getMetaDataByPids($pids, $s);

		// 配列整形
		foreach ($metaArr as $i => $meta) {
			$metas[$meta->post_id][$meta->meta_key] = $meta->meta_value;
		}

		foreach ($metas as $pid => $meta) {
			switch ($serviceType) {
				case 'veritrans':
				default:
					$ret[$pid] = sprintf('%s,%s', 
						$pid, 
						$meta['_field_company-name']
						//TODO:
					);
					break;

				case 'mf-kessai':
					$ret[$pid] = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s', 
						$pid, 
						$meta['_field_company-name'], 
						$meta['_field_tel'], 
						$meta['_field_init-zip'], 
						$meta['_field_init-pref'], //json
						$meta['_field_init-addr1']. $meta['_field_init-addr2'], 
						$meta['_field_init-addr3'], 
						$meta['_field_staff-section'], 
						$meta['_field_staff-post'], 
						$meta['_field_staff-name-sei'].	$meta['_field_staff-name-mei'], 
						$meta['_field_staff-name-kana-se'].	$meta['_field_staff-name-kana-mei'], 
						$meta['_field_staff-mail'], 
						$meta['_field_staff-mail'], 
						$meta['_field_staff-mail'], 
						$meta['_field_staff-mail'], 
						$meta['_field_staff-mail'], 
						'', //'事業所区分', 
						$meta['_field_biz-form'], //'法人番号', 
						$meta['_field_ceo-name-sei']. $meta['_field_ceo-name-mei'], 
						$meta['_field_goods'], 
						$meta['_field_url'], 
						'', //'希望与信額', 
						'', //'初月希望与信額', 
						'', //'その他情報', 
						'' //'口振依頼書送付'
					);
					break;
			}
		}
		
		return $ret;
	}

	/**
	 *
	 **/
	public function getShopList($prm = null) {
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);
/*
		
		// your_name, your_emailで検索してIDを取得するSQL
		//$rows = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_field_your-email'");
		$cnt_sql = "(select count(*) From wp_postmeta where post_id = p.ID) as cnt";
		$sub_sql = "(select meta_value From wp_postmeta where post_id = p.ID and meta_key = '_field_your-email') as mail";
		$sql  = "SELECT p.*, ". $cnt_sql. ", ". $sub_sql. " FROM ".$wpdb->prefix."posts as p ";
		$sql .= "WHERE p.post_type = 'flamingo_inbound' ";

		if (is_null($prm)) {
			$sql .= ";";
		} else {
			if ($prm->action == 'search') {
				$sql .= sprintf("AND p.ID = %s ", $prm->s['no']);
				$sql .= ";";
			} else {
				$sql .= "AND p.ID = ". $prm->post. ";";
			}
		}
		$rows = $wpdb->get_results($sql);
*/
		if ($cur_user->roles[0] != 'administrator') {
			$_GET['s']['post'] = $prm->post;
		}

		// 対象のpost_id を取得
		$pidArr = $this->getPostIds($_GET['s']);

		// 配列整形
		foreach ($pidArr as $i => $d) {
			$pids[] = $d->ID;
		}

		// post_idからpostmetaを取得
		$metaArr = $this->getMetaDataByPids($pids, $_GET['s']);

		// 配列整形
		foreach ($metaArr as $i => $meta) {
			$metas[$meta->post_id][$meta->meta_key] = $meta->meta_value;
		}

		// 配列整形
		foreach ($metas as $id => $d) {
			$d['ID'] = $id;
			foreach ($d as $k => $v) {
				$r[str_replace('-', '_', $k)] = $v;
			}
			$ret[$d['_field_your-email']] = (object) $r;
		}
		return (object) $ret;
	}

	/**
	 *
	 **/
	public function getShopDetail($prm = null) {
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);
		
		// post_idで検索してIDを取得するSQL
		$sql  = "SELECT pm.* FROM ".$wpdb->prefix."postmeta as pm ";
//		$sql .= "WHERE p.post_type = 'flamingo_inbound';";
		$sql .= "WHERE pm.post_id = '". $prm->post. "'";
/*
		$rows = $wpdb->get_results($sql);

		// 配列整形
		foreach ($rows as $i => $d) {
			$ret[str_replace('-', '_', $d->meta_key)] = $d->meta_value;
		}
*/

		$rows = get_post_meta($prm->post);

		// 配列整形
		foreach ($rows as $k => $v) {
			$ret[str_replace('-', '_', $k)] = $v[0];
		}

		$r = (object) $ret;
//		print_r($r->_hash);
		return $r;
	}

	/**
	 *
	 **/
	public function updShopDetail($prm = null, $p = null) {
/*
		update_post_meta($prm->post, '_field_company-name', $p->company_name, '');
		update_post_meta($prm->post, '_field_company-name-kana', $p->company_name_kana, '');
		update_post_meta($prm->post, '_field_ceo-name-sei', $p->ceo_name_sei, '');
		update_post_meta($prm->post, '_field_ceo-name-mei', $p->ceo_name_mei, '');
		update_post_meta($prm->post, '_field_ceo-name-kana-sei', $p->ceo_name_kana_sei, '');
		update_post_meta($prm->post, '_field_ceo-name-kana-mei', $p->ceo_name_kana_mei, '');
		update_post_meta($prm->post, '_field_init-addr-kana1', $p->init_addr_kana1, '');
*/
		update_post_meta($prm->post, '_field_apply-service', $p->apply_service, '');
		update_post_meta($prm->post, '_field_apply-plan', $p->apply_plan, '');
		update_post_meta($prm->post, '_field_biz-form', $p->biz_form, '');
		update_post_meta($prm->post, '_field_company-name', $p->company_name, '');
		update_post_meta($prm->post, '_field_company-name-kana', $p->company_name_kana, '');
		update_post_meta($prm->post, '_field_init-zip', $p->init_zip, '');
		update_post_meta($prm->post, '_field_init-pref', $p->init_pref, '');
		update_post_meta($prm->post, '_field_init-addr1', $p->init_addr1, '');
		update_post_meta($prm->post, '_field_init-addr2', $p->init_addr2, '');
		update_post_meta($prm->post, '_field_init-addr3', $p->init_addr3, '');
		update_post_meta($prm->post, '_field_init-addr-kana1', $p->init_addr_kana1, '');
		update_post_meta($prm->post, '_field_tel', $p->tel, '');
		update_post_meta($prm->post, '_field_fax', $p->fax, '');
		update_post_meta($prm->post, '_field_est-dt', $p->est_dt, '');
		update_post_meta($prm->post, '_field_num-employ', $p->num_employ, '');
		update_post_meta($prm->post, '_field_capital', $p->capital, '');
		update_post_meta($prm->post, '_field_annual-sales', $p->annual_sales, '');
		update_post_meta($prm->post, '_field_goods', $p->goods, '');
		update_post_meta($prm->post, '_field_delivery-company', $p->delivery_company, '');
		update_post_meta($prm->post, '_field_url', $p->url, '');
		update_post_meta($prm->post, '_field_ceo-name-sei', $p->ceo_name_sei, '');
		update_post_meta($prm->post, '_field_ceo-name-mei', $p->ceo_name_mei, '');
		update_post_meta($prm->post, '_field_ceo-name-kana-sei', $p->ceo_name_kana_sei, '');
		update_post_meta($prm->post, '_field_ceo-name-kana-mei', $p->ceo_name_kana_mei, '');
		update_post_meta($prm->post, '_field_ceo-birth', $p->ceo_birth, '');
		update_post_meta($prm->post, '_field_ceo-addr', $p->ceo_addr, '');
		update_post_meta($prm->post, '_field_ceo-zip', $p->ceo_zip, '');
		update_post_meta($prm->post, '_field_ceo-pref', $p->ceo_pref, '');
		update_post_meta($prm->post, '_field_ceo-addr1', $p->ceo_addr1, '');
		update_post_meta($prm->post, '_field_ceo-addr2', $p->ceo_addr2, '');
		update_post_meta($prm->post, '_field_ceo-addr3', $p->ceo_addr3, '');
		update_post_meta($prm->post, '_field_ceo-add-kana', $p->ceo_add_kana, '');
		update_post_meta($prm->post, '_field_ceo-tel', $p->ceo_tel, '');
		update_post_meta($prm->post, '_field_check-corp-name', $p->check_corp_name, '');
		update_post_meta($prm->post, '_field_staff-company-name', $p->staff_company_name, '');
		update_post_meta($prm->post, '_field_staff-company-name-kana', $p->staff_company_name_kana, '');
		update_post_meta($prm->post, '_field_staff-name-sei', $p->staff_name_sei, '');
		update_post_meta($prm->post, '_field_staff-name-mei', $p->staff_name_mei, '');
		update_post_meta($prm->post, '_field_staff-name-kana-sei', $p->staff_name_kana_sei, '');
		update_post_meta($prm->post, '_field_staff-name-kana-mei', $p->staff_name_kana_mei, '');
		update_post_meta($prm->post, '_field_staff-mail', $p->staff_mail, '');
		update_post_meta($prm->post, '_field_staff-section', $p->staff_section, '');
		update_post_meta($prm->post, '_field_staff-post', $p->staff_post, '');
		update_post_meta($prm->post, '_field_staff-tel', $p->staff_tel, '');
		update_post_meta($prm->post, '_field_staff-fax', $p->staff_fax, '');
		update_post_meta($prm->post, '_field_check-staff-sub-addr', $p->check_staff_sub_addr, '');
		update_post_meta($prm->post, '_field_staff-sub-zip', $p->staff_sub_zip, '');
		update_post_meta($prm->post, '_field_staff-sub-pref', $p->staff_sub_pref, '');
		update_post_meta($prm->post, '_field_staff-sub-addr1', $p->staff_sub_addr1, '');
		update_post_meta($prm->post, '_field_staff-sub-addr2', $p->staff_sub_addr2, '');
		update_post_meta($prm->post, '_field_staff-sub-addr3', $p->staff_sub_addr3, '');
		update_post_meta($prm->post, '_field_staff-add-kana', $p->staff_add_kana, '');
		update_post_meta($prm->post, '_field_cf7msm-no-ss', $p->cf7msm_no_ss, '');
		update_post_meta($prm->post, '_field_cf7msm-options', $p->cf7msm_options, '');
		update_post_meta($prm->post, '_field_multistep-916', $p->multistep_916, '');
		update_post_meta($prm->post, '_field_check-invoice', $p->check_invoice, '');
		update_post_meta($prm->post, '_field_invoice-company-name', $p->invoice_company_name, '');
		update_post_meta($prm->post, '_field_invoice-company-name-kana', $p->invoice_company_name_kana, '');
		update_post_meta($prm->post, '_field_invoice-name-sei', $p->invoice_name_sei, '');
		update_post_meta($prm->post, '_field_invoice-name-mei', $p->invoice_name_mei, '');
		update_post_meta($prm->post, '_field_invoice-name-kana-sei', $p->invoice_name_kana_sei, '');
		update_post_meta($prm->post, '_field_invoice-name-kana-mei', $p->invoice_name_kana_mei, '');
		update_post_meta($prm->post, '_field_invoice-section', $p->invoice_section, '');
		update_post_meta($prm->post, '_field_invoice-post', $p->invoice_post, '');
		update_post_meta($prm->post, '_field_invoice-tel', $p->invoice_tel, '');
		update_post_meta($prm->post, '_field_invoice-fax', $p->invoice_fax, '');
		update_post_meta($prm->post, '_field_check-invoice-sub-addr', $p->check_invoice_sub_addr, '');
		update_post_meta($prm->post, '_field_invoice-sub-zip', $p->invoice_sub_zip, '');
		update_post_meta($prm->post, '_field_invoice-sub-pref', $p->invoice_sub_pref, '');
		update_post_meta($prm->post, '_field_invoice-sub-addr1', $p->invoice_sub_addr1, '');
		update_post_meta($prm->post, '_field_invoice-sub-addr2', $p->invoice_sub_addr2, '');
		update_post_meta($prm->post, '_field_invoice-sub-addr3', $p->invoice_sub_addr3, '');
		update_post_meta($prm->post, '_field_invoice-add-kana', $p->invoice_add_kana, '');
		update_post_meta($prm->post, '_field_fin-name', $p->fin_name, '');
		update_post_meta($prm->post, '_field_fin-branch-name', $p->fin_branch_name, '');
		update_post_meta($prm->post, '_field_account-type', $p->account_type, '');
		update_post_meta($prm->post, '_field_fin-account-number', $p->fin_account_number, '');
		update_post_meta($prm->post, '_field_fin-account-name', $p->fin_account_name, '');
		update_post_meta($prm->post, '_field_fin-account-name-kana', $p->fin_account_name_kana, '');
		update_post_meta($prm->post, '_field_acc-select-type', $p->acc_select_type, '');
		update_post_meta($prm->post, '_field_goods-name-1', $p->goods_name_1, '');
		update_post_meta($prm->post, '_field_goods-price-1', $p->goods_price_1, '');
		update_post_meta($prm->post, '_field_goods-name-2', $p->goods_name_2, '');
		update_post_meta($prm->post, '_field_goods-price-2', $p->goods_price_2, '');
		update_post_meta($prm->post, '_field_goods-name-3', $p->goods_name_3, '');
		update_post_meta($prm->post, '_field_goods-price-3', $p->goods_price_3, '');
		update_post_meta($prm->post, '_field_goods-price-range', $p->goods_price_range, '');
		update_post_meta($prm->post, '_field_other-site-url', $p->other_site_url, '');
		update_post_meta($prm->post, '_field_distributor', $p->distributor, '');
		update_post_meta($prm->post, '_field_corp-name', $p->corp_name, '');
		update_post_meta($prm->post, '_field_corp-name-kana', $p->corp_name_kana, '');
		update_post_meta($prm->post, '_field_corp-name-en', $p->corp_name_en, '');
		update_post_meta($prm->post, '_field_check-location', $p->check_location, '');
		update_post_meta($prm->post, '_field_supervisor-zip', $p->supervisor_zip, '');
		update_post_meta($prm->post, '_field_supervisor-pref', $p->supervisor_pref, '');
		update_post_meta($prm->post, '_field_supervisor-addr1', $p->supervisor_addr1, '');
		update_post_meta($prm->post, '_field_supervisor-addr2', $p->supervisor_addr2, '');
		update_post_meta($prm->post, '_field_supervisor-addr3', $p->supervisor_addr3, '');
		update_post_meta($prm->post, '_field_supervisor-add-kana', $p->supervisor_add_kana, '');
		update_post_meta($prm->post, '_field_check-supervisor', $p->check_supervisor, '');
		update_post_meta($prm->post, '_field_supervisor-name-sei', $p->supervisor_name_sei, '');
		update_post_meta($prm->post, '_field_supervisor-name-mei', $p->supervisor_name_mei, '');
		update_post_meta($prm->post, '_field_supervisor-mail', $p->supervisor_mail, '');
		update_post_meta($prm->post, '_field_supervisor-tel', $p->supervisor_tel, '');
		update_post_meta($prm->post, '_field_supervisor-fax', $p->supervisor_fax, '');
		update_post_meta($prm->post, '_field_contact-time-1', $p->contact_time_1, '');
		update_post_meta($prm->post, '_field_contact-time-2', $p->contact_time_2, '');
		update_post_meta($prm->post, '_field_expenses', $p->expenses, '');
		update_post_meta($prm->post, '_field_expenses-other', $p->expenses_other, '');
		update_post_meta($prm->post, '_field_defective', $p->defective, '');
		update_post_meta($prm->post, '_field_defective-other', $p->defective_other, '');
		update_post_meta($prm->post, '_field_sales-qty', $p->sales_qty, '');
		update_post_meta($prm->post, '_field_sales-qty-other', $p->sales_qty_other, '');
		update_post_meta($prm->post, '_field_delivery-time-other-1', $p->delivery_time_other_1, '');
		update_post_meta($prm->post, '_field_delivery-time-other-2', $p->delivery_time_other_2, '');
		update_post_meta($prm->post, '_field_payment', $p->payment, '');
		update_post_meta($prm->post, '_field_payment-other', $p->payment_other, '');
		update_post_meta($prm->post, '_field_due-payment', $p->due_payment, '');
		update_post_meta($prm->post, '_field_about-returns', $p->about_returns, '');
		update_post_meta($prm->post, '_field_about-returns-other', $p->about_returns_other, '');
		update_post_meta($prm->post, '_field_due-returns', $p->due_returns, '');
		update_post_meta($prm->post, '_field_return-shipping', $p->return_shipping, '');
		update_post_meta($prm->post, '_field_return-shipping-other', $p->return_shipping_other, '');
		update_post_meta($prm->post, '_field_check-vt-1', $p->check_vt_1, '');
		update_post_meta($prm->post, '_field_check-vt-2', $p->check_vt_2, '');
		update_post_meta($prm->post, '_field_check-vt-3', $p->check_vt_3, '');
		update_post_meta($prm->post, '_field_check-vt-4', $p->check_vt_4, '');
		update_post_meta($prm->post, '_field_check-vt-5', $p->check_vt_5, '');
		update_post_meta($prm->post, '_field_check-vt-6', $p->check_vt_6, '');
		update_post_meta($prm->post, '_field_check-vt-7', $p->check_vt_7, '');
		update_post_meta($prm->post, '_field_check-vt-8', $p->check_vt_8, '');
		update_post_meta($prm->post, '_field_check-vt-9', $p->check_vt_9, '');
		update_post_meta($prm->post, '_field_check-vt-10', $p->check_vt_10, '');
		update_post_meta($prm->post, '_field_check-vt-11', $p->check_vt_11, '');
		update_post_meta($prm->post, '_field_your-subject', $p->your_subject, '');
		update_post_meta($prm->post, '_field_your-name', $p->your_name, '');
		update_post_meta($prm->post, '_field_your-email', $p->your_email, '');
		update_post_meta($prm->post, '_field_goods-image-1', $p->goods_image_1, '');
		update_post_meta($prm->post, '_field_goods-image-2', $p->goods_image_2, '');
		update_post_meta($prm->post, '_field_goods-image-3', $p->goods_image_3, '');
		return true;
	}
}
?>
