<?php
class Applicants {
	protected $_name = 'wp_applicant';

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
	 * Emailで申込者コードを取得
	 **/
	public function getApplicantByEmail($email = null) {
		global $wpdb;
		if (!empty($email)) {
			$sql = "SELECT ap.applicant FROM ".$wpdb->prefix."applicant as ap WHERE ap.mail = '". $email. "';";
			$app = $wpdb->get_results($sql);
		}
		return ($app) ? current($app)->applicant : null;
	}

	/**
	 *
	 **/
	public function getPrm() {
		global $wpdb;
		$cur_user = wp_get_current_user();
//		$this->vd($cur_user->user_login);
//		$this->vd($cur_user->user_email);
		$prm = (object) $_GET;

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

	/**
	 * 
	 **/
	public function getValidElement($step_num = null) {
		$agreement = array(
			'rules' => array(
			), 
			'messages' => array(
			)
		);

		$step1 = array(
			'rules' => array(
				'apply_service'				=> 'required|max:100',
				'apply_plan'				=> 'required|max:100',

				'biz_fg'					=> 'required|max:100',
				'biz_number'				=> 'required|regex:/^[0-9]{13}+$/i',
				'company_name'				=> 'required|max:100',
				'company_name_kana'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'zip'						=> 'required|max:100',
				'pref'						=> 'required|max:100',
				'addr'						=> 'required|max:100',
				'addr2'						=> 'required|max:100',
				'addr3'						=> 'max:100',
				'addr_kana'					=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'tel'						=> 'required|max:100',
				'fax'						=> 'max:100',
				'est_dt'					=> 'required|max:100',
				'num_employ'				=> 'required|max:100',
				'capital'					=> 'required|max:100',
				'annual_sales'				=> 'max:100',
				'goods_class'				=> 'required|max:100',
				'goods'						=> 'required|max:100',
				'delivery_company'			=> 'max:100',
				'url'						=> 'max:100',
				'ceo_name_sei'				=> 'required|max:100',
				'ceo_name_kana_sei'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'ceo_name_mei'				=> 'required|max:100',
				'ceo_name_kana_mei'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'ceo_birth'					=> 'required|max:100',
				'ceo_addr_fg'				=> 'required|max:100',
				'ceo_zip'					=> 'required|max:100',
				'ceo_pref'					=> 'required|max:100',
				'ceo_addr1'					=> 'required|max:100',
				'ceo_addr2'					=> 'required|max:100',
				'ceo_addr3'					=> 'max:100',
				'ceo_addr_kana'				=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'ceo_tel'					=> 'required|max:100',

				'corp_fg'					=> 'required|max:100',
				'staff_company_name'		=> 'required|max:100',
				'staff_company_name_kana'	=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'staff_addr_fg'				=> 'required|max:100',
				'staff_zip'					=> 'required|max:100',
				'staff_pref'				=> 'required|max:100',
				'staff_addr1'				=> 'required|max:100',
				'staff_addr2'				=> 'required|max:100',
				'staff_addr3'				=> 'max:100',
				'staff_addr_kana'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'staff_name_sei'			=> 'required|max:100',
				'staff_name_kana_sei'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'staff_name_mei'			=> 'required|max:100',
				'staff_name_kana_mei'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'staff_section'				=> 'max:100',
//				'staff_post'				=> 'required|max:100',
				'staff_mail'				=> 'required|max:100',
				'staff_tel'					=> 'required|max:100',
				'staff_fax'					=> 'max:100',
/*
				'name'                  => 'required|max:2',
				'email'                 => 'required|email',
				'password'              => 'required|min:6',
				'confirm_password'      => 'required|same:password',
				'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
				'skills'                => 'array',
				'skills.*.id'           => 'required|numeric',
				'skills.*.percentage'   => 'required|numeric'
*/
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

		$step2 = array(
			'rules' => array(
				'invoice_fg'				=> 'required|max:100',
				'invoice_company_name_fg'	=> 'required|max:100',
				'invoice_company_name'		=> 'required|max:100',
				'invoice_company_name_kana'	=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'invoice_addr_fg'			=> 'required|max:100',
				'invoice_zip'				=> 'required|max:100',
				'invoice_pref'				=> 'required|max:100',
				'invoice_addr1'				=> 'required|max:100',
				'invoice_addr2'				=> 'required|max:100',
				'invoice_addr3'				=> 'max:100',
				'invoice_addr_kana'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'invoice_name_sei'			=> 'required|max:100',
				'invoice_name_mei'			=> 'required|max:100',
				'invoice_name_kana_sei'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'invoice_name_kana_mei'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'invoice_section'			=> 'max:100',
				'invoice_tel'				=> 'required|max:100',
				'invoice_fax'				=> 'max:100',

				'fin_name'					=> 'required|max:100',
				'fin_branch_name'			=> 'required|max:100',
				'fin_account_type'			=> 'required|max:100',
				'fin_account_number'		=> 'required|max:100',
				'fin_account_name'			=> 'required|max:100',
				'fin_account_name_kana'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー 　]+$/u',
			), 
			'messages' => array(
				'name.required' => 'ユーザー名を入力してください',
			)
		);

		$step3 = array(
			'rules' => array(
				'goods_name1'				=> 'required|max:100',
				'goods_price1'				=> 'required|max:100',
//				'goods_image'				=> 'required|max:100',
//				'goods_name2'				=> 'required|max:100',
//				'goods_price2'				=> 'required|max:100',
//				'goods_image'				=> 'required|max:100',
//				'goods_name3'				=> 'required|max:100',
//				'goods_price3'				=> 'required|max:100',
//				'goods_image'				=> 'required|max:100',
				'price_range_min'			=> 'required|max:100',
				'price_range_max'			=> 'required|max:100',
//				'other_site_url'			=> 'required|max:100',

				'distributor'				=> 'required|max:100',
				'corp_name'					=> 'required|max:100',
				'corp_name_kana'			=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'corp_name_en'				=> 'required|max:100',
				'location_fg'				=> 'required|max:100',
				'supervisor_zip'			=> 'required|max:100',
				'supervisor_pref'			=> 'required|max:100',
				'supervisor_addr'			=> 'required|max:100',
				'supervisor_addr2'			=> 'required|max:100',
				'supervisor_addr3'			=> 'max:100',
				'supervisor_addr_kana'		=> 'required|max:100|regex:/^[ァ-ヶｦ-ﾟー]+$/u',
				'supervisor_fg'				=> 'required|max:100',
				'supervisor_name_sei'		=> 'required|max:100',
				'supervisor_name_mei'		=> 'required|max:100',
				'supervisor_mail'			=> 'required|max:100',
				'supervisor_tel'			=> 'required|max:100',
				'supervisor_fax'			=> 'max:100',
				'contact_s_time'			=> 'required|max:100',
				'contact_e_time'			=> 'required|max:100',
				'expenses'					=> 'required|max:100',
				'expenses_other'			=> 'max:100',
				'defective'					=> 'required|max:100',
				'defective_other'			=> 'max:100',
				'sales_qty'					=> 'required|max:100',
				'sales_qty_other'			=> 'max:100',
				'delivery_time'				=> 'required|max:100',
				'delivery_time_none'		=> 'required|max:100',
				'payment'					=> 'required|max:100',
				'payment_other'				=> 'max:100',
				'due_payment'				=> 'required|max:100',
				'about_returns'				=> 'required|max:100',
				'about_returns_other'		=> 'max:100',
				'due_returns'				=> 'required|max:100',
				'return_shipping'			=> 'required|max:100',
				'return_shipping_other'		=> 'max:100',

			), 
			'messages' => array(
				'name.required' => 'ユーザー名を入力してください',
			)
		);

		$step4 = array(
			'rules' => array(
				'vt_ch1'				=> 'required|max:1',
				'vt_ch2'				=> 'required|max:1',
				'vt_ch3'				=> 'required|max:1',
				'vt_ch4'				=> 'required|max:1',
				'vt_ch5'				=> 'required|max:1',
				'vt_ch6'				=> 'required|max:1',
				'vt_ch7'				=> 'required|max:1',
				'vt_ch8'				=> 'required|max:1',
				'vt_ch9'				=> 'required|max:1',
				'vt_ch10'				=> 'required|max:1',
				'vt_ch11'				=> 'required|max:1',
			), 
			'messages' => array(
				'name.required' => 'ユーザー名を入力してください',
			)
		);

		$all = array(
			'rules' => array_merge($step1['rules'], $step2['rules'], $step3['rules'], $step4['rules']), 
			'messages' => array_merge($step1['messages'], $step2['messages'], $step3['messages'], $step4['messages'])
		);

		switch ($step_num) {
			case 0:
				return $agreement;
				break;

			case 1:
				return $step1;
				break;

			case 2:
				return $step2;
				break;

			case 3:
				return $step3;
				break;

			case 4:
				return $step4;
				break;

			default:
				return $all;
				break;
		}
	}

	/**
	 * 
	 **/
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
			'mf-kessai' => array(
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
	 * 申込者情報一覧取得
	 **/
	public function getList($prm = null) {
		$prm = (object) $prm;
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);
		
		// your_name, your_emailで検索してIDを取得するSQL
		//$rows = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_field_your-email'");
//		$sql  = "SELECT ap.* FROM wp_applicant as ap ";
//		$sql  = "SELECT ap.applicant, ap.biz_fg, ap.biz_number, ap.company_name, ap.ceo_name_sei, ap.ceo_name_mei, ap.mail, ap.tel FROM wp_applicant as ap ";
		$sql  = "SELECT ap.applicant, ap.mail, ap.biz_fg, ap.biz_number, ap.company_name, ap.ceo_name_sei, ap.ceo_name_mei, ap.tel, ap.status, ap.field1, ap.rgdt, ap.updt FROM wp_applicant as ap ";
		$sql .= "WHERE ap.applicant is not null ";

		if (current($cur_user->roles) != 'administrator') {
			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}
//		$sql .= "WHERE ap.applicant = 'test';";

		if (empty($prm->action)) {
			$sql .= "ORDER BY ap.rgdt desc";
			$sql .= ";";
		} else {
			if ($prm->action == 'search') {
				if (!empty($prm->s['no'])) { $sql .= sprintf("AND ap.applicant = '%s' ", $prm->s['no']); }
				if (!empty($prm->s['company_name'])) { $sql .= sprintf("AND ap.company_name = '%s' ", $prm->s['company_name']); }
				if (!empty($prm->s['sdt'])) { $sql .= sprintf("AND ap.rgdt > '%s 00:00:00' ", $prm->s['sdt']); }
				if (!empty($prm->s['edt'])) { $sql .= sprintf("AND ap.rgdt <= '%s 23:59:59' ", $prm->s['edt']); }
				$sql .= "ORDER BY ap.rgdt desc";
				$sql .= ";";
			} else {
				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);
		return $rows;
/*
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
*/
	}

	/**
	 * 申込者情報詳細取得
	 **/
	public function getDetail($prm = null) {
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);
		
		// post_idで検索してIDを取得するSQL
		$sql  = "SELECT ap.* FROM ".$wpdb->prefix."applicant as ap ";
//		$sql .= "WHERE p.post_type = 'flamingo_inbound';";
		$sql .= "WHERE ap.applicant = '". $prm->post. "'";

		if (current($cur_user->roles) != 'administrator') {
			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		$sql .= "LIMIT 1;";
		$rows = $wpdb->get_results($sql);

/*
		// 配列整形
		foreach ($rows as $i => $d) {
			$ret[str_replace('-', '_', $d->meta_key)] = $d->meta_value;
		}
*/
		return $rows[0];
	}

	/**
	 * 申込者情報一覧取得 (エクスポート用)
	 **/
	public function getListForExport($s = null, $service_type = null) {
		$prm = (object) $prm;
		global $wpdb;
		$cur_user = wp_get_current_user();
		//var_dump($cur_user->user_login);
		//var_dump($cur_user->user_email);

		//$this->vd(array($s, $service_type, $prm, $cur_user));exit;
		
		switch ($service_type) {
			case 'veritrans' :
			default :
				$sql  = "SELECT 
							ap.applicant, 
							CASE WHEN ap.biz_fg = 1 THEN '法人' WHEN ap.biz_fg = 2 THEN '個人' ELSE '' END AS biz_fg, 
							ap.biz_number, 
							ap.company_name, 
							ap.company_name_kana, 
							ap.url, 
							'物販' as biz_content, 
							'-' as listed_class, 
							DATE_FORMAT(ap.est_dt, '%Y/%m/%d') as est_dt, 
							ap.capital, 
							ap.zip, 
							ap.pref, 
							ap.addr, 
							ap.addr2, 
							ap.addr3, 
							ap.addr_kana, 
							ap.tel, 
							concat(ap.ceo_name_sei, ' ', ap.ceo_name_mei) as ceo_name, 
							concat(ap.ceo_name_kana_sei, ' ', ap.ceo_name_kana_mei) as ceo_name_kana, 
							DATE_FORMAT(ap.ceo_birth, '%Y/%m/%d') as ceo_birth, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_zip ELSE '' END AS ceo_zip, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_pref ELSE '' END AS ceo_pref, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_addr1 ELSE '' END AS ceo_addr1, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_addr2 ELSE '' END AS ceo_addr2, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_addr3 ELSE '' END AS ceo_addr3, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_addr_kana ELSE '' END AS ceo_addr_kana, 
							CASE WHEN ap.biz_fg = 1 THEN '' WHEN ap.biz_fg = 2 THEN ap.ceo_tel ELSE '' END AS ceo_tel, 
							ap.corp_name, 
							ap.corp_name_kana, 
							ap.corp_name_en, 
							'通信販売' as sales_form, 
							ap.goods_class, 
							ap.goods, 
							concat(ap.price_range_min, '～', ap.price_range_max) as price_range, 
							'PC' as site_type, 
							'https://www.47club.jp/' as other_site_url, 
							'準備中' as site_class, 
							'-' as release_dt, 
							'30' as assumed_number_of_transactions, 
							ap.price_range_max as price_max, 
							((ap.price_range_min + ap.price_range_max) / 2) as price_average, 
							ap.zip as shop_zip, 
							ap.pref as shop_pref, 
							ap.addr as shop_addr, 
							ap.addr2 as shop_addr2, 
							ap.addr3 as shop_addr3, 
							ap.addr_kana as shop_addr_kana, 
							'-' as unknown_5, 
							ap.supervisor_tel, 
							ap.supervisor_mail, 
							DATE_FORMAT(ap.contact_s_time, '%H:%i') as contact_s_time, 
							DATE_FORMAT(ap.contact_e_time, '%H:%i') as contact_e_time, 
							ap.staff_name_sei, 
							ap.staff_name_mei, 
							concat(ap.staff_name_kana_sei, ' ', ap.staff_name_kana_mei) as staff_name_kana, 
							ap.staff_tel, 
							ap.staff_mail, 
							ap.company_name as company_name2, 
							concat(ap.supervisor_name_sei, ' ', ap.supervisor_name_mei) as supervisor_name, 
							ap.expenses, 
							ap.expenses_other as expenses_detail, 
							'-' as unknown_6, 
							ap.defective, 
							ap.defective_other as defective_detail, 
							'-' as unknown_7, 
							ap.delivery_time, 
							ap.delivery_time_none, 
							ap.sales_qty, 
							ap.sales_qty_other as sales_qty_detail, 
							ap.about_returns, 
							ap.about_returns_other as about_returns_detail, 
							'' as unknown_18, 
							ap.due_returns, 
							CASE WHEN ap.vt_ch1 = 1 THEN '有' WHEN ap.vt_ch1 = 2 THEN '無' ELSE '-' END AS vt_ch1, 
							CASE WHEN ap.vt_ch2 = 1 THEN '有' WHEN ap.vt_ch2 = 2 THEN '無' ELSE '-' END AS vt_ch2, 
							CASE WHEN ap.vt_ch3 = 1 THEN '有' WHEN ap.vt_ch3 = 2 THEN '無' ELSE '-' END AS vt_ch3, 
							CASE WHEN ap.vt_ch4 = 1 THEN '有' WHEN ap.vt_ch4 = 2 THEN '無' ELSE '-' END AS vt_ch4, 
							CASE WHEN ap.vt_ch5 = 1 THEN '有' WHEN ap.vt_ch5 = 2 THEN '無' ELSE '-' END AS vt_ch5, 
							'-' as unknown_8, 
							'-' as unknown_9, 
							'-' as unknown_10, 
							CASE WHEN ap.vt_ch6 = 1 THEN '有' WHEN ap.vt_ch6 = 2 THEN '無' ELSE '-' END AS vt_ch6, 
							'-' as unknown_11, 
							CASE WHEN ap.vt_ch7 = 1 THEN '有' WHEN ap.vt_ch7 = 2 THEN '無' ELSE '-' END AS vt_ch7, 
							'-' as unknown_12, 
							CASE WHEN ap.vt_ch8 = 1 THEN '有' WHEN ap.vt_ch8 = 2 THEN '無' ELSE '-' END AS vt_ch8, 
							'-' as unknown_13, 
							CASE WHEN ap.vt_ch9 = 1 THEN '有' WHEN ap.vt_ch9 = 2 THEN '無' ELSE '-' END AS vt_ch9, 
							'-' as unknown_14, 
							CASE WHEN ap.vt_ch10 = 1 THEN '有' WHEN ap.vt_ch10 = 2 THEN '無' ELSE '-' END AS vt_ch10, 
							'-' as unknown_15, 
							'-' as unknown_16, 
							'保証する' as ch_undefeated_lawsuit, 
							ap.fin_name, 
							'-' as fin_code, 
							ap.fin_branch_name, 
							'-' as fin_branch_code, 
							CASE WHEN ap.fin_account_type = 1 THEN '当座' WHEN ap.fin_account_type = 2 THEN '普通' ELSE '' END AS fin_account_type, 
							ap.fin_account_number, 
							ap.fin_account_name, 
							ap.fin_account_name_kana
						 FROM wp_applicant as ap ";
				break;
			case 'mf-kessai' :
				$sql  = "SELECT 
							ap.applicant, 
							ap.company_name, 
							ap.tel, 
							ap.zip, 
							ap.pref, 
							concat(ap.addr, ' ', ap.addr2) as addr, 
							ap.addr3, 
							ap.staff_section, 
							ap.staff_post, 
							concat(ap.staff_name_sei, ' ', ap.staff_name_mei) as staff_name, 
							concat(ap.staff_name_kana_sei, ' ', ap.staff_name_kana_mei) as staff_name_kana, 
							ap.mail as mail, 
							'' as mail1, 
							'' as mail2, 
							'' as mail3, 
							'' as mail4, 
							ap.biz_fg, 
							ap.biz_number, 
							concat(ap.ceo_name_sei, ' ', ap.ceo_name_mei) as ceo_name, 
							ap.goods_name1, 
							ap.url, 
							'1000000' as credit_line, 
							'' as first_month_credit_line, 
							'' as remarks, 
							'0' as send_request
						 FROM wp_applicant as ap ";
				break;
		}
		$sql .= "WHERE ap.applicant is not null ";
		$sql .= "AND ap.applicant <> '' ";

		if (current($cur_user->roles) != 'administrator') {
			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}
//		$sql .= "WHERE ap.applicant = 'test';";

		if (empty($prm->action)) {
			$sql .= ";";
		} else {
			if ($prm->action == 'search') {
				if (!empty($prm->s['no'])) { $sql .= sprintf("AND ap.applicant = '%s' ", $prm->s['no']); }
				if (!empty($prm->s['company_name'])) { $sql .= sprintf("AND ap.company_name = '%s' ", $prm->s['company_name']); }
				if (!empty($prm->s['sdt'])) { $sql .= sprintf("AND ap.rgdt > '%s 00:00:00' ", $prm->s['sdt']); }
				if (!empty($prm->s['edt'])) { $sql .= sprintf("AND ap.rgdt <= '%s 23:59:59' ", $prm->s['edt']); }
				$sql .= ";";
			} else {
				$sql .= "AND ap.applicant = '". $prm->post. "';";
			}
		}

		$rows = $wpdb->get_results($sql);

		switch ($service_type) {
			case 'veritrans' :
				// 「取扱商品分類」配列を取得
				$arr_goods_class = $this->getPartsGoodsClass();
				// 「商品以外の必要料金」配列を取得
				$arr_expenses = $this->getPartsExpenses();
				// 「不良品の取扱」配列を取得
				$arr_defective = $this->getPartsDefective();
				// 「販売数量」配列を取得
				$arr_sales_qty = $this->getPartsSalesQty();
				// 「返品について」配列を取得
				$arr_about_returns = $this->getPartsAboutReturns();

				foreach ($rows as $i => $d) {
					// 「取扱商品分類」を数値から文字列へ変換
					if (!empty($d->goods_class)) {
						$d->goods_class = $arr_goods_class[$d->goods_class];
					}

					// 「商品以外の必要料金」をJSONから文字列へ変換
					if (!empty($d->expenses)) {
						$expenses = json_decode($d->expenses);
						if (is_array($expenses) == true) {
							$tmp = null;
							foreach ($expenses as $num) {
								switch ($num) {
									case '9': 
										// 「その他」欄の文言表示
										$tmp[] = (!empty($d->expenses_detail)) ? sprintf('%s「%s」', $arr_expenses[$num], $d->expenses_detail) : '';
										break;
									default :
										$tmp[] = $arr_expenses[$num];
										break;
								}
							}
							$d->expenses = implode(';', $tmp);
						}
					}

					// 「不良品の取扱」を数値から文字列へ変換
					if (!empty($d->defective)) {
						// 「その他」欄の文言表示
						$d->defective = (!empty($d->defective_detail)) ? sprintf('%s「%s」', $arr_defective[$d->defective], $d->defective_detail) : $arr_defective[$d->defective];
					}

					// 「販売数量」を数値から文字列へ変換
					if (!empty($d->sales_qty)) {
						// 「その他」欄の文言表示
						$d->sales_qty = (!empty($d->sales_qty_detail)) ? sprintf('%s「%s」', $arr_sales_qty[$d->sales_qty], $d->sales_qty_detail) : $arr_sales_qty[$d->sales_qty];
					}

					// 「返品について」を数値から文字列へ変換
					if (!empty($d->about_returns)) {
						// 「その他」欄の文言表示
						$d->about_returns = (!empty($d->about_returns_detail)) ? sprintf('%s「%s」', $arr_about_returns[$d->about_returns], $d->about_returns_detail) : $arr_about_returns[$d->about_returns];
					}

					// 「その他」欄の削除(xlsxエクスポート時の列ずれを解消するため)
					unset($rows[$i]->expenses_detail);
					unset($rows[$i]->defective_detail);
					unset($rows[$i]->sales_qty_detail);
					unset($rows[$i]->about_returns_detail);
				}
				break;
		}

		return $rows;
	}

	/**
	 * 申込者情報詳細取得
	 * - 申込者コード(applicant)から抽出
	 **/
	public function getDetailByApplicantCode($applicant = null) {
		global $wpdb;
//$this->vd($applicant);exit;
		$sql  = "SELECT ap.* FROM ".$wpdb->prefix."applicant as ap ";
		$sql .= sprintf("WHERE ap.applicant = '%s' ", $applicant);
		$sql .= "LIMIT 1;";

		$rows = $wpdb->get_results($sql);
		return $rows[0];
	}

	/**
	 * 申込者情報詳細取得
	 * - ログインしているユーザーの情報(mail)から抽出
	 **/
	public function getDetailByMail() {
		global $wpdb;
		$cur_user = wp_get_current_user();
		
		// mailで検索してIDを取得するSQL
		$sql  = "SELECT ap.* FROM ".$wpdb->prefix."applicant as ap ";
		$sql .= "WHERE ap.mail is not null ";

		if (current($cur_user->roles) != 'administrator') {
			$sql .= "AND ap.mail = '". $cur_user->user_email. "'";
		}

		$sql .= "LIMIT 1;";
		$rows = $wpdb->get_results($sql);

		return $rows[0];
	}

	/**
	 * 申込者情報登録
	 **/
	public function regDetail($prm = null, $p = null) {
		global $wpdb;
		$cur_user = $this->getCurUser();

		if (current($cur_user->roles) != 'administrator') {
			$app = $this->getApplicantByEmail($cur_user->user_email);
		} else {
			$app = null;
		}

//var_dump($app);

/*
$wpdb->query($wpdb->prepare(
"
 INSERT INTO $wpdb->table_name
 (column1, column2,column3)
 VALUES(%d,%s,%s)
 ON DUPLICATE KEY UPDATE
 column2 = %s,
 column3 = %s
 ",
$value1,
$value2,
$value3,
$value4,
$value5
));
*/

		if (!empty($app)) {
/*
			$sql = "UPDATE wp_applicant ";
			$sql .= "SET company_name = 'test company', ";
			$sql .= sprintf("updt = '%s' ", date('Y-m-d H:i:s')); // updt
			$sql .= "WHERE applicant = '". $app. "' ";
			$sql .= "AND mail = '". $cur_user->user_email. "' ";
			$sql .= ";";
*/

			$where = array(
				'applicant' => $app, 
				'mail' => $cur_user->user_email
			);

			$p->updt = date('Y-m-d H:i:s'); // updt

			$exist_columns = $wpdb->get_col("DESC wp_applicant;", 0);
			foreach ($exist_columns as $i => $col) {
					// 更新除外カラムをスキップ
					if (in_array($col, array('applicant', 'mail'))) { continue; }

					if (isset($p->$col)) {
						$data[$col] = $p->$col;
					}
			}

//unset($data['invoice_addr_fg']);

/*
unset($data['location_fg']);
unset($data['supervisor_fg']);
unset($data['defective']);
unset($data['sales_qty']);
unset($data['about_returns']);
*/
			// JSON形式で登録している項目のコード
			if (!empty($data['expenses'])) { $data['expenses'] = json_encode($data['expenses']); }
			if (!empty($data['payment'])) { $data['payment'] = json_encode($data['payment']); }
//			if (!empty($data['return_shipping'])) { $data['return_shipping'] = json_encode($data['return_shipping']); }

			// 入力欄「その他」がある項目の制御
			$data['expenses_other'] = (!empty($data['expenses']) && in_array('9', json_decode($data['expenses']))) ? $p->expenses_other : '';
			$data['payment_other'] = (!empty($data['payment']) && in_array('9', json_decode($data['payment']))) ? $p->payment_other : '';
			$data['defective_other'] = (!empty($data['defective']) && $data['defective'] == 9) ? $p->defective_other : '';
			$data['sales_qty_other'] = (!empty($data['sales_qty']) && $data['sales_qty'] == 9) ? $p->sales_qty_other : '';
			$data['about_returns_other'] = (!empty($data['about_returns']) && $data['about_returns'] == 9) ? $p->about_returns_other : '';
			$data['return_shipping_other'] = (!empty($data['return_shipping_other']) && $data['return_shipping'] == 9) ? $p->return_shipping_other : '';

			// ファイル(商品画像等)アップ時の制御
//			$r_goods_image = (!empty($_FILES['goods_image']['name'][0])) ? $_FILES['goods_image']['name'][0] : null;
			$data['goods_image1'] = (!empty($p->goods_image1)) ? $p->goods_image1 : '';

			$ret = $wpdb->update(
				'wp_applicant', 
				$data, 
				$where
			);
//$this->vd(array($ret, $data, $where));exit;
			return $ret;

		} else {
			$sql = "INSERT INTO wp_applicant VALUES (";
			$date = date('md-His');
			$sql .= sprintf("'test-%s', ", $date); // applicant
			$sql .="'1','biz_number','company_name','company_name_kana','zip','pref','addr','addr2','addr3','addr_kana','tel',";
			$sql .="'fax','est_dt','num_employ','capital','annual_sales','goods_class','goods','delivery_company','url','逸品','太郎','ceo_name_kana_sei',";
			$sql .="'ceo_name_kana_mei','ceo_birth','ceo_addr_fg','ceo_zip','ceo_pref','ceo_addr1','ceo_addr2','ceo_addr3','ceo_addr_kana','ceo_tel',";
			$sql .="'1','staff_company_name','staff_company_name_kana','staff_name_sei','staff_name_mei','staff_name_kana_sei','staff_name_kana_mei',";
			$sql .="'staff_mail','staff_section','staff_post','staff_tel','staff_fax','1','staff_zip','staff_pref','staff_addr1','staff_addr2',";
			$sql .="'staff_addr3','staff_addr_kana','1','1','invoice_company_name','invoice_company_name_kana','invoice_name_sei','invoice_name_mei',";
			$sql .="'invoice_name_kana_sei','invoice_name_kana_mei','invoice_section','invoice_post','invoice_tel','invoice_fax','1','invoice_zip',";
			$sql .="'invoice_pref','invoice_addr1','invoice_addr2','invoice_addr3','invoice_addr_kana','fin_name','fin_branch_name','fin_account_type','fin_account_number',";
			$sql .="'fin_account_name','fin_account_name_kana','goods_name1','goods_price1','goods_image1','goods_name2','goods_price2','goods_image2','goods_name3',";
			$sql .="'goods_price3','goods_image3','price_range_min','price_range_max','other_site_url','distributor','corp_name','corp_name_kana','corp_name_en','1',";
			$sql .="'supervisor_zip','supervisor_pref','supervisor_addr','supervisor_addr2','supervisor_addr3','supervisor_addr_kana','1',";
			$sql .="'supervisor_name_sei','supervisor_name_mei','supervisor_mail','supervisor_tel','supervisor_fax','contact_s_time','contact_e_time','expenses',";
			$sql .="'1','1','delivery_time','delivery_time_none','1','due_payment','1','due_returns','1',";
			$sql .="'2','2','2','2','2','2','1','2','2','2','2','status','shop_category','open_dt','close_dt','remark',";
			$sql .="'field1','field2','field3','message',";
			$sql .= sprintf("'%s',", date('Y-m-d H:i:s')); // rgdt
			$sql .= "'updt','test'";
			$sql .= ");";
		}

		$ret = $wpdb->query($sql);
		return $ret;
	}

	/**
	 * 申込者情報更新
	 **/
	public function updDetail($prm = null, $p = null) {
		$p = (object) $p;
		global $wpdb;

		$exist_columns = $wpdb->get_col("DESC wp_applicant;", 0);
		foreach ($exist_columns as $i => $col) {
				if(!empty($p->$col)) {
					$data[$col] = $p->$col;
				}
		}

//$this->vd($data);
/*
		$ret_sql = $wpdb->prepare(
		"UPDATE wp_applicant 
		 set 
			company_name = %s, 
			company_name_kana = %s
		 WHERE applicant = %s
		 ",
		$p->company_name, 
		$p->company_name_kana, 
		$p->applicant
		);
*/
		$ret = $wpdb->update(
			'wp_applicant', 
/*
			array(
				'company_name' => $p->company_name,
				'company_name_kana' => $p->company_name_kana,
			), 
*/
			$data, 
			array('applicant' => $p->applicant)
		);
//var_dump($ret);
		return true;
	}

	public function vd($d) {
		echo '<pre>';
		var_dump($d);
		echo '</pre>';
	}

	/**
	 * STATUS(状態)管理
	 **/
	public $_status = null;

	/**
	 * STATUS(状態)の設定
	 **/
	public function setStatus($cmd = null, $step = null) {
		// DBからstatusを取得
		$status = $this->getStatusByApplicantCode();

		// 一桁ずつ配列に格納
		$sts = str_split($status,1);

		switch ($cmd) {
			case 'cmd_confirm': // 「確定」の場合のみ処理
				if (!is_null($step)) { $sts[$step] = true; } // stepの対応要素を「0:未確定」から「1:確定」にする
				break;

			default:
			case 'cmd_regist':
				break;
		}

		$this->_status = implode($sts); // 配列を文字列に変換
	}

	/**
	 * STATUS(状態)の取得
	 **/
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * 申込者コードでSTATUS(状態)取得
	 **/
	public function getStatusByApplicantCode($applicant = null) {
		global $wpdb;

		// 申込者コードがPOST値から取れなかったら、ユーザー情報のemailから取る
		if (is_null($applicant)) {
			$cur_user = wp_get_current_user();
			//var_dump($cur_user->user_login);
			//var_dump($cur_user->user_email);
			$applicant = $this->getApplicantByEmail($cur_user->user_email);
		}

		if (!empty($applicant)) {
			$sql = "SELECT ap.status FROM ".$wpdb->prefix."applicant as ap WHERE ap.applicant = '". $applicant. "';";
			$status = $wpdb->get_results($sql);
		}
		return ($status) ? current($status)->status : null;
	}

	/**
	 * STATUS(状態)をメニューに表示するための文言を取得
	 **/
	public function getStatusForMenu($applicant = null) {
		$status = $this->getStatusByApplicantCode();

		// 一桁ずつ配列に格納
		$sts = str_split($status,1);

		foreach ($sts as $i => $st) {
			$sts[$i] = ($st != true) ? '未確定' : '確定'; // 「0:未確定」、「1:確定」を代入
		}
		return $sts;
	}

	/**
	 * 申込者コードでSTATUS(状態)が全確定かどうか確認
	 **/
	public function checkAllStatus() {
		$status = $this->getStatusByApplicantCode();
		// "0"が入ってないか確認。"0"が入っていたら(false)を、入ってない(=全て"1")なら全確定(true)を返す
		return (strpos($status, "0") !== false) ? false : true;
	}

	/**
	 * 申込者コードのSTATUS(状態)を初期化
	 **/
	public function initStatus($applicant = null) {
		global $wpdb;

		// 現在の状態が初期値'00000'の場合、処理をせず false を返す
		$status = $this->getStatusByApplicantCode($applicant);
		$init_status = '00000';

		if ($status !== $init_status) {
			$data = array(
				'status' => $init_status
			);

			$where = array(
				'applicant' => $applicant
			);

			$ret = $wpdb->update(
				'wp_applicant', 
				$data, 
				$where
			);
		} else {
			$ret = false;
		}

		return $ret;
	}

	/**
	 * 各STEPの判定フラグを基に申込情報のデータをコピーする
	 **/
	public function copyDataByFlag($p = null) {

		$row = $this->getDetailByMail();

		//STEP 1
/*
		if ($_POST['biz_fg'] == '2') { $ve['rules']['biz_number'] = ''; }
*/
		if ($p->ceo_addr_fg == '1') {
			$p->ceo_zip = $p->zip;
			$p->ceo_pref = $p->pref;
			$p->ceo_addr1 = $p->addr;
			$p->ceo_addr2 = $p->addr2;
			$p->ceo_addr3 = $p->addr3;
			$p->ceo_addr_kana = $p->addr_kana;
			$p->ceo_tel = $p->tel;
		}

		if ($p->corp_fg == '1') {
			$p->staff_company_name = $p->company_name; 
			$p->staff_company_name_kana = $p->company_name_kana;
		}
		if ($p->staff_addr_fg == '1') {
			$p->staff_company_name_kana = $p->company_name_kana;
			$p->staff_zip = $p->zip;
			$p->staff_pref = $p->pref;
			$p->staff_addr1 = $p->addr;
			$p->staff_addr2 = $p->addr2;
			$p->staff_addr3 = $p->addr3;
			$p->staff_addr_kana = $p->addr_kana;
		}

		//STEP 2 TODO: 保留
		if ($p->invoice_fg == '1') {
			if ($p->invoice_company_name_fg == '1') {
				$p->invoice_company_name = (!empty($p->invoice_company_name)) ? $p->invoice_company_name : $row->invoice_company_name;
				$p->invoice_company_name_kana = (!empty($p->invoice_company_name_kana)) ? $p->invoice_company_name_kana : $row->invoice_company_name_kana;
			}
			if ($p->invoice_addr_fg == '1') {
				$p->invoice_zip = '';
				$p->invoice_pref = '';
				$p->invoice_addr1 = '';
				$p->invoice_addr2 = '';
				$p->invoice_addr3 = '';
				$p->invoice_addr_kana = '';
				$p->invoice_name_sei = '';
				$p->invoice_name_mei = '';
				$p->invoice_name_kana_sei = '';
				$p->invoice_name_kana_mei = '';
				$p->invoice_section = '';
				$p->invoice_tel = '';
				$p->invoice_fax = '';
			}
		}

		//STEP 3
		if ($p->location_fg == '1') {
			$p->supervisor_zip = (!empty($p->zip)) ? $p->zip : $row->zip;
			$p->supervisor_pref = (!empty($p->pref)) ? $p->pref : $row->pref;
			$p->supervisor_addr = (!empty($p->addr)) ? $p->addr : $row->addr;
			$p->supervisor_addr2 = (!empty($p->addr2)) ? $p->addr2 : $row->addr2;
			$p->supervisor_addr3 = (!empty($p->addr3)) ? $p->addr3 : $row->addr3;
			$p->supervisor_addr_kana = (!empty($p->addr_kana)) ? $p->addr_kana : $row->addr_kana;
		}

		if ($p->supervisor_fg == '1') {
			$p->supervisor_name_sei = (!empty($p->ceo_name_sei)) ? $p->ceo_name_sei : $row->ceo_name_sei;
			$p->supervisor_name_mei = (!empty($p->ceo_name_mei)) ? $p->ceo_name_mei : $row->ceo_name_mei;
		}

		return $p;
	}

	/**
	 * 項目コピーのradioにチェックが入ってる場合、rulesを削除してValidation不要にする
	 **/
	public function initValidationRules($p = null, $ve = null) {
		//STEP 1
		if ($p['biz_fg'] == '2') { $ve['rules']['biz_number'] = ''; }
		if ($p['ceo_addr_fg'] == '1') {
			$ve['rules']['ceo_zip'] = ''; 
			$ve['rules']['ceo_pref'] = ''; 
			$ve['rules']['ceo_addr1'] = ''; 
			$ve['rules']['ceo_addr2'] = ''; 
			$ve['rules']['ceo_addr3'] = ''; 
			$ve['rules']['ceo_addr_kana'] = ''; 
			$ve['rules']['ceo_tel'] = ''; 
		}
		if ($p['corp_fg'] == '1') {
			$ve['rules']['staff_company_name'] = ''; 
			$ve['rules']['staff_company_name_kana'] = '';
		}
		if ($p['staff_addr_fg'] == '1') {
			$ve['rules']['staff_company_name_kana'] = '';
			$ve['rules']['staff_zip'] = '';
			$ve['rules']['staff_pref'] = '';
			$ve['rules']['staff_addr1'] = '';
			$ve['rules']['staff_addr2'] = '';
			$ve['rules']['staff_addr3'] = '';
			$ve['rules']['staff_addr_kana'] = '';
		}
		//STEP 2
		if (in_array($p['invoice_fg'], array('1', '2'))) {
			if ($p['invoice_company_name_fg'] == '1') {
				$ve['rules']['invoice_company_name'] = '';
				$ve['rules']['invoice_company_name_kana'] = '';
			}
			if ($p['invoice_addr_fg'] == '1') {
				$ve['rules']['invoice_zip'] = '';
				$ve['rules']['invoice_pref'] = '';
				$ve['rules']['invoice_addr1'] = '';
				$ve['rules']['invoice_addr2'] = '';
				$ve['rules']['invoice_addr3'] = '';
				$ve['rules']['invoice_addr_kana'] = '';
				$ve['rules']['invoice_name_sei'] = '';
				$ve['rules']['invoice_name_mei'] = '';
				$ve['rules']['invoice_name_kana_sei'] = '';
				$ve['rules']['invoice_name_kana_mei'] = '';
				$ve['rules']['invoice_section'] = '';
				$ve['rules']['invoice_tel'] = '';
				$ve['rules']['invoice_fax'] = '';
			}
		}
		//STEP 3
		if ($p['location_fg'] == '1') {
				$ve['rules']['supervisor_zip'] = '';
				$ve['rules']['supervisor_pref'] = '';
				$ve['rules']['supervisor_addr'] = '';
				$ve['rules']['supervisor_addr2'] = '';
				$ve['rules']['supervisor_addr3'] = '';
				$ve['rules']['supervisor_addr_kana'] = '';
		}
		if ($p['supervisor_fg'] == '1') {
				$ve['rules']['supervisor_name_sei'] = '';
				$ve['rules']['supervisor_name_mei'] = '';
		}

		return $ve;
	}

	/**
	 * 入力欄「その他」のradioにチェックが入ってる場合、rulesを変更してValidationする
	 **/
	public function changeValidationRules($p = null, $ve = null) {
		if (empty($p)) { return $ve; }

		//STEP 3
		if (!empty($p['expenses']) && in_array('9', $p['expenses'])) {
				$ve['rules']['expenses_other'] = 'required|max:100';
		}
		if (!empty($p['payment']) && in_array('9', $p['payment'])) {
				$ve['rules']['payment_other'] = 'required|max:100';
		}
		if (!empty($p['defective']) && $p['defective'] == '9') {
				$ve['rules']['defective_other'] = 'required|max:100';
		}
		if (!empty($p['sales_qty']) && $p['sales_qty'] == '9') {
				$ve['rules']['sales_qty_other'] = 'required|max:100';
		}
		if (!empty($p['about_returns']) && $p['about_returns'] == '9') {
				$ve['rules']['about_returns_other'] = 'required|max:100';
		}
		if (!empty($p['return_shipping']) && $p['return_shipping'] == '9') {
				$ve['rules']['return_shipping_other'] = 'required|max:100';
		}

		return $ve;
	}

	/**
	 * 商品画像①のvalidation追加
	 **/
	public function changeFileValidationRules($p = null, $ve = null) {
		if (empty($p)) { return $ve; }

		$cur_user = $this->getCurUser();

		if (current($cur_user->roles) != 'administrator') {
			$applicant = $this->getApplicantByEmail($cur_user->user_email);
		} else {
			return false;
		}

		//STEP 3
		$r_goods_image = $p['goods_image']['name'][0];
		if (empty($r_goods_image)) {
				// 前回file名の登録がなければ(=初回)必須にする
				if ($this->existsGoodsImageRequired($applicant) === false) {
					$ve['rules']['goods_image1'] = 'required';
				}
		}

		return $ve;
	}

	/**
	 * 商品画像①の登録チェック
	 **/
	public function existsGoodsImageRequired($applicant = null) {
		global $wpdb;

		if (!empty($applicant)) {
			$sql = "SELECT ap.goods_image1 FROM ". $wpdb->prefix. "applicant as ap WHERE ap.applicant = '". $applicant. "';";
			$ret = $wpdb->get_results($sql);
			$goods_image = $ret[0]->goods_image1;
		}
		return (!empty($goods_image)) ? true : false;
	}

	/**
	 * Userテーブルに登録がなく、申込者テーブルに登録があるユーザー一覧を取得（削除対象）
	 **/
	public function getUsersDeleted() {
		global $wpdb;
		$sql = "select ap.applicant, ap.mail, u.ID, u.user_nicename, u.user_email ". 
				"from wp_applicant as ap ". 
				"left join ". 
				"wp_users as u ". 
				"on ap.mail = u.user_email ". 
				"where u.ID is null;";

		$delUsers = $wpdb->get_results($sql);
		// 削除対象のApplicantコード、mailを返す。
		foreach ($delUsers as $i => $user) {
			if (!empty($user->applicant)) { 
				$ret[] = array(
					'applicant' => $user->applicant, 
					'mail' => $user->mail, 
				);
			}
		}

		if (!is_null($ret)) {
			return (count($ret) > 0) ? $ret : null;
		} else {
			return $ret;
		}
	}

	/**
	 * Userテーブルに登録がなく、申込者テーブルに登録があるユーザーの削除
	 **/
	public function initDelRecord($delUsers) {
		global $wpdb;

		$sql = "DELETE FROM wp_applicant WHERE applicant IN (";
		foreach ($delUsers as $i => $user_data) {
			$sql .= sprintf("'%s', ", $user_data['applicant']);
		}
		$sql = preg_replace('/, $/', ');', $sql);
		$ret[] = $wpdb->query($sql);

		return (count($ret) > 0) ? $ret[0] : 0;
	}

	/**
	 * Userテーブルに登録があり、申込者テーブルに未登録のユーザー一覧を取得
	 **/
	public function getUsersUnRegisterd() {
		global $wpdb;
		$sql = "select ap.applicant, ap.mail, u.id, u.user_email, u.user_status, u.user_nicename, u.role, u.first_name, u.last_name from wp_applicant as ap ". 
				"right join ". 
				"(select u1.*, u2.first_name, u3.last_name from ". 
					"(select u.id, u.user_email, u.user_status, u.user_nicename, um.meta_key, um.meta_value as role from wp_users as u ". 
						"right join wp_usermeta as um on u.ID = um.user_id where um.meta_key = 'wp_capabilities') as u1 ". 

					// wp_usermeta.first_nameの取得
					"right join ". 
					"(select u.ID, u.user_email, um.meta_key, um.meta_value as first_name from wp_users as u right join wp_usermeta as um ". 
						"on u.ID = um.user_id where um.meta_key = 'first_name') as u2 ". 
					"on u1.user_email = u2.user_email ". 

					// wp_usermeta.last_nameの取得
					"right join ". 
					"(select u.ID, u.user_email, um.meta_key, um.meta_value as last_name from wp_users as u right join wp_usermeta as um ". 
						"on u.ID = um.user_id where um.meta_key = 'last_name') as u3 ". 
					"on u1.user_email = u3.user_email ". 

				"where u1.role like '%subscriber%'". 
				") as u ". 
				"on ap.mail = u.user_email;";

		$unUsers = $wpdb->get_results($sql);
		// Applicantコード未生成のuser_emailを返す。追加で「nicename」(=ユーザーID)と、「first_name」(=会社名)、「last_name」(=NPコード) も返す。
		foreach ($unUsers as $i => $user) {
			if (is_null($user->applicant)) { 
				$ret[$user->user_email] = array(
					'nicename' => $user->user_nicename, 
					'first_name' => $user->first_name, 
					'last_name' => $user->last_name
				);
			}
		}

		if (!is_null($ret)) {  
			return (count($ret) > 0) ? $ret : null;
		} else {
			return $ret;
		}
	}

	/**
	 * Userテーブルに登録があり、申込者テーブルに未登録のユーザーの初期レコード登録
	 **/
	public function initRegRecord($unUsers) {
		global $wpdb;

		foreach ($unUsers as $mail => $user_data) {
			$rows = current($wpdb->get_results("select count(*) as cnt from ". $wpdb->prefix. "applicant"));

			$date = date('YmdHis', time());
			$cols = array(
				'applicant' => sprintf("AP-%s-%05d", $date, $rows->cnt+1), 
				'mail' => $mail, 
				'agree1' => '0', 
				'agree2' => '0', 
				'apply_service' => '2', 
				'apply_plan' => '1', 
				'biz_fg' => '0', 
				'biz_number' => '', 
				'company_name' => $user_data['first_name'], 
				'company_name_kana' => '', 
				'zip' => '', 
				'pref' => '', 
				'addr' => '', 
				'addr2' => '', 
				'addr3' => '', 
				'addr_kana' => '', 
				'tel' => '', 
				'fax' => '', 
				'est_dt' => '', 
				'num_employ' => '', 
				'capital' => '', 
				'annual_sales' => '', 
				'goods_class' => '', 
				'goods' => '', 
				'delivery_company' => '', 
				'url' => '', 
				'ceo_name_sei' => '', 
				'ceo_name_mei' => '', 
				'ceo_name_kana_sei' => '', 
				'ceo_name_kana_mei' => '', 
				'ceo_birth' => '', 
				'ceo_addr_fg' => '1', 
				'ceo_zip' => '', 
				'ceo_pref' => '', 
				'ceo_addr1' => '', 
				'ceo_addr2' => '', 
				'ceo_addr3' => '', 
				'ceo_addr_kana' => '', 
				'ceo_tel' => '', 
				'corp_fg' => '1', 
				'staff_company_name' => '', 
				'staff_company_name_kana' => '', 
				'staff_name_sei' => '', 
				'staff_name_mei' => '', 
				'staff_name_kana_sei' => '', 
				'staff_name_kana_mei' => '', 
				'staff_mail' => '', 
				'staff_section' => '', 
				'staff_post' => '', 
				'staff_tel' => '', 
				'staff_fax' => '', 
				'staff_addr_fg' => '1', 
				'staff_zip' => '', 
				'staff_pref' => '', 
				'staff_addr1' => '', 
				'staff_addr2' => '', 
				'staff_addr3' => '', 
				'staff_addr_kana' => '', 
				'invoice_fg' => '1', 
				'invoice_company_name_fg' => '1', 
				'invoice_company_name' => '', 
				'invoice_company_name_kana' => '', 
				'invoice_name_sei' => '', 
				'invoice_name_mei' => '', 
				'invoice_name_kana_sei' => '', 
				'invoice_name_kana_mei' => '', 
				'invoice_section' => '', 
				'invoice_post' => '', 
				'invoice_tel' => '', 
				'invoice_fax' => '', 
				'invoice_addr_fg' => '1', 
				'invoice_zip' => '', 
				'invoice_pref' => '', 
				'invoice_addr1' => '', 
				'invoice_addr2' => '', 
				'invoice_addr3' => '', 
				'invoice_addr_kana' => '', 
				'fin_name' => '', 
				'fin_branch_name' => '', 
				'fin_account_type' => '', 
				'fin_account_number' => '', 
				'fin_account_name' => '', 
				'fin_account_name_kana' => '', 
				'goods_name1' => '', 
				'goods_price1' => '', 
				'goods_image1' => '', 
				'goods_name2' => '', 
				'goods_price2' => '', 
				'goods_image2' => '', 
				'goods_name3' => '', 
				'goods_price3' => '', 
				'goods_image3' => '', 
				'price_range_min' => '', 
				'price_range_max' => '', 
				'other_site_url' => '', 
				'distributor' => '', 
				'corp_name' => '', 
				'corp_name_kana' => '', 
				'corp_name_en' => '', 
				'location_fg' => '1', 
				'supervisor_zip' => '', 
				'supervisor_pref' => '', 
				'supervisor_addr' => '', 
				'supervisor_addr2' => '', 
				'supervisor_addr3' => '', 
				'supervisor_addr_kana' => '', 
				'supervisor_fg' => '1', 
				'supervisor_name_sei' => '', 
				'supervisor_name_mei' => '', 
				'supervisor_mail' => '', 
				'supervisor_tel' => '', 
				'supervisor_fax' => '', 
				'contact_s_time' => '', 
				'contact_e_time' => '', 
				'expenses' => '[\"1\"\,\"2\"]', // 商品以外の必要代金
				'expenses_other' => '', // 商品以外の必要代金「その他」
				'defective' => '1', // 不良品の取扱
				'defective_other' => '', // 不良品の取扱「その他」
				'sales_qty' => '1', // 販売数量
				'sales_qty_other' => '', // 販売数量「その他」
				'delivery_time' => '', // 引渡し時期：在庫がある場合
				'delivery_time_none' => '', // 引渡し時期：在庫がない場合
				'payment' => '1', // 支払方法
				'payment_other' => '', // 支払方法「その他」
				'due_payment' => '', // 支払期限
				'about_returns' => '1', // 返品について
				'about_returns_other' => '', // 返品について「その他」
				'due_returns' => '', // 返品期限
				'return_shipping' => '1', // 返品送料
				'return_shipping_other' => '', // 返品送料「その他」
				'vt_ch1' => '2', 
				'vt_ch2' => '2', 
				'vt_ch3' => '2', 
				'vt_ch4' => '2', 
				'vt_ch5' => '2', 
				'vt_ch6' => '2', 
				'vt_ch7' => '1', 
				'vt_ch8' => '2', 
				'vt_ch9' => '2', 
				'vt_ch10' => '2', 
				'vt_ch11' => '2', 
				'status' => '00000', 
				'shop_category' => '', 
				'open_dt' => NULL, 
				'close_dt' => NULL, 
				'remark' => '', 
				'field1' => $user_data['last_name'], 
				'field2' => '', 
				'field3' => '', 
				'message' => '', 
				'rgdt' => sprintf("%s", date('Y-m-d H:i:s')), 
				'updt' => NULL, 
				'upuser' => 'test', 
			);

			$sql = "INSERT INTO wp_applicant VALUES (";
			foreach ($cols as $k => $v) {
				if ($v !== '' && $v === NULL) {
					$sql .= sprintf("NULL, ");
				} else {
					$sql .= sprintf("'%s', ", $v);
				}
			}
			$sql = preg_replace('/, $/', ');', $sql);

			$ret[] = $wpdb->query($sql);
		}
		return (count($ret) > 0) ? $ret[0] : 0;
	}

	/**
	 * 申込者コード一覧を取得
	 **/
	public function getApplicantCodes() {
		global $wpdb;
		$sql = "SELECT ap.applicant FROM ".$wpdb->prefix."applicant as ap ". 
				"right join ". 
				"(select u.id, u.user_email, u.user_status, um.meta_key, um.meta_value as role from wp_users as u ". 
					"right join wp_usermeta as um on u.ID = um.user_id where um.meta_key = 'wp_capabilities') as u ". 
					"on ap.mail = u.user_email where u.role like '%subscriber%' ". 
					"AND ap.applicant is not null AND ap.mail is not null;";
		$apps = $wpdb->get_results($sql);

		foreach ($apps as $i => $app) {
			$ret[] = $app->applicant;
		}
		return $ret;
	}

	/**
	 * アップロードファイルの削除
	 **/
	public function delFile($delete_file = null, $applicant = null) {
		$dir = dirname(__DIR__). '/uploads/'. $applicant;
		$del_file_path = $dir. '/'. $delete_file;

		if (file_exists($del_file_path)) {
			// 削除実行
			$ret = unlink($del_file_path);
			return $ret;
		}
	}


	/**
	 * DBのgoods_imageを削除
	 **/
	public function deleteGoodsImage($delete_file = null, $applicant = null) {
		global $wpdb;

		if (!is_null($delete_file)) {
			$tmp = preg_replace('/^(.+)_/', '', $delete_file);
			$num = preg_replace('/.jpg$/', '', $tmp);
			$data = array(
				'goods_image'. $num => ''
			);

			$where = array(
				'applicant' => $applicant
			);

			$ret = $wpdb->update(
				'wp_applicant', 
				$data, 
				$where
			);
		} else {
			$ret = false;
		}

		return $ret;
	}

	/**
	 * 
	 **/
	public function getInitForm() {
		return array(
			'select' => array(
				'pref' => $this->getPartsPref(), 
				'service' => $this->getPartsService(), 
				'plan' => $this->getPartsPlan(), 
				'goods_class' => $this->getPartsGoodsClass(), 
				'biz_fg' => $this->getPartsBizFg(), 
				'expenses' => $this->getPartsExpenses(), 
				'defective' => $this->getPartsDefective(), 
				'sales_qty' => $this->getPartsSalesQty(), 
				'payment' => $this->getPartsPayment(), 
				'about_returns' => $this->getPartsAboutReturns(), 
				'return_shipping' => $this->getPartsReturnShipping(), 
			)
		);
	}
}
?>
