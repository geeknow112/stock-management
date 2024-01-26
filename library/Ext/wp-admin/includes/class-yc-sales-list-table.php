<?php
/**
 * List Table API: YC_Sales_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying users in a list table.
 *
 * @since 3.1.0
 * @access private
 *
 * @see WP_List_Table
 */
class YC_Sales_List_Table extends WP_List_Table {

	/**
	 * Site ID to generate the Users list table for.
	 *
	 * @since 3.1.0
	 * @var int
	 */
	public $site_id;

	/**
	 * Whether or not the current Users list table is for Multisite.
	 *
	 * @since 3.1.0
	 * @var bool
	 */
	public $is_site_users;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'singular' => 'user',
				'plural'   => 'users',
				'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);

		$this->is_site_users = 'site-users-network' === $this->screen->id;

		if ( $this->is_site_users ) {
			$this->site_id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;
		}
	}

	/**
	 * Check the current user's permissions.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		if ( $this->is_site_users ) {
			return current_user_can( 'manage_sites' );
		} else {
			return current_user_can( 'list_users' );
		}
	}

	/**
	 * Prepare the users list for display.
	 *
	 * @since 3.1.0
	 *
	 * @global string $role
	 * @global string $usersearch
	 */
	public function prepare_items() {
		global $role, $usersearch;

if (!is_array($_REQUEST['s'])) {
		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
} else {
	if (isset( $_REQUEST['s'] )) {
		foreach ($_REQUEST['s'] as $k => $v) {
			$tmp[$k] = wp_unslash( trim( $v ) );
		}
		$usersearch = $tmp;
	} else {
		$usersearch = '';
	}
}

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$per_page       = ( $this->is_site_users ) ? 'site_users_network_per_page' : 'users_per_page';
		$users_per_page = $this->get_items_per_page( $per_page );

		$paged = $this->get_pagenum();

		if ( 'none' === $role ) {
			$args = array(
				'number'  => $users_per_page,
				'offset'  => ( $paged - 1 ) * $users_per_page,
				'include' => wp_get_users_with_no_role( $this->site_id ),
				'search'  => $usersearch,
				'fields'  => 'all_with_meta',
			);
		} else {
			$args = array(
				'number' => $users_per_page,
				'offset' => ( $paged - 1 ) * $users_per_page,
				'role'   => $role,
				'search' => $usersearch,
				'fields' => 'all_with_meta',
			);
		}

		if ( '' !== $args['search'] ) {
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( $this->is_site_users ) {
			$args['blog_id'] = $this->site_id;
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		/**
		 * Filters the query arguments used to retrieve users for the current users list table.
		 *
		 * @since 4.4.0
		 *
		 * @param array $args Arguments passed to WP_User_Query to retrieve items for the current
		 *                    users list table.
		 */
		$args = apply_filters( 'users_list_table_query_args', $args );

		// Query the user IDs for this page.
		$wp_user_search = new WP_User_Query( $args );

//		$this->items = $wp_user_search->get_results();
global $wpdb;
$req = (object) $_REQUEST;

		// 一括操作：ステータス変更処理
		if ($req->cmd == 'edit' && isset($req->change_status)) {
			$get = (object) $_GET;
			$post = (object) $_POST;
			$Sales = new Sales;
			$ret = $Sales->changeStatus($req->change_status, $req->no);
			$Sales->makeLotSpace($get, $post);
		}

//$this->vd($req);
$where = sprintf("WHERE s.sales is not null AND s.status <> 9 ");
if (!empty($req->s['no'])) { $where .= sprintf("AND s.sales = '%s'", $req->s['no']); }
if (!empty($req->s['goods_name'])) { $where .= "AND g.name LIKE '%". $req->s['goods_name']. "%'"; }
if (isset($req->s['car_model']) && $req->s['car_model'] != '0') { $where .= sprintf("AND s.class = '%s'", $req->s['car_model']); }
if (isset($req->s['status']) && $req->s['status'] != '') { $where .= sprintf("AND s.status = '%s'", $req->s['status']); }
if (!empty($req->s['outgoing_warehouse']) && $req->s['outgoing_warehouse'] != '') { $where .= sprintf("AND s.outgoing_warehouse = '%s' ", $req->s['outgoing_warehouse']); }
if (!empty($req->s['lot'])) { $where .= sprintf("AND gd.lot = '%s'", $req->s['lot']); }
if (!empty($req->s['order_s_dt'])) { $where .= sprintf("AND s.rgdt >= '%s 00:00:00' ", $req->s['order_s_dt']); }
if (!empty($req->s['order_e_dt'])) { $where .= sprintf("AND s.rgdt <= '%s 23:59:59' ", $req->s['order_e_dt']); }
if (!empty($req->s['delivery_s_dt'])) { $where .= sprintf("AND s.delivery_dt >= '%s 00:00:00' ", $req->s['delivery_s_dt']); }
if (!empty($req->s['delivery_e_dt'])) { $where .= sprintf("AND s.delivery_dt <= '%s 23:59:59' ", $req->s['delivery_e_dt']); }
if (!empty($req->s['arrival_s_dt'])) { $where .= sprintf("AND s.arrival_dt >= '%s 00:00:00' ", $req->s['arrival_s_dt']); }
if (!empty($req->s['arrival_e_dt'])) { $where .= sprintf("AND s.arrival_dt <= '%s 23:59:59' ", $req->s['arrival_e_dt']); }
//$this->vd($where);

		// TEST repeat
		/**
		 *  1. 検索条件をもとに、salesを抽出する。
		 *  2. 同条件でJOINして、repeatを抽出する。
		 *  3. mergeする。
		 *  4. pagerのために、mergeした配列を調整する。
		 **/

//				$sql_r = sprintf("select * from yc_sales as s LEFT JOIN yc_schedule_repeat AS sr ON s.sales = sr.sales WHERE s.repeat_fg = 1;");
				//print_r($sql_r);
//				$repeat_items = $wpdb->get_results( $sql_r );
		//		$r = $repeat_items[0];

				// repeat itemsの生成
//				$ret_repeat_items = $this->makeRepeatItems($repeat_items);

		//$this->vd($ret_repeat_items);
		//$this->vd(count($ret_repeat_items));

//		$count_repeat_item = count($ret_repeat_items);
//		$users_per_page = $users_per_page - $count_repeat_item; //repeat対象注文カウント数分減ずる


		/**
		 * 受注情報をLIMITで取得して、後でpager用にrepert分を追加する方法
		 **/
		$limit = ($paged -1) * $users_per_page;
		$sql = sprintf("SELECT s.*, g.name AS goods_name, g.separately_fg, c.name AS customer_name FROM yc_sales AS s ");
		$sql .= sprintf("LEFT JOIN yc_goods AS g ON s.goods = g.goods ");
		if (!empty($req->s['lot'])) { $sql .= sprintf("LEFT JOIN yc_goods_detail AS gd ON s.sales = gd.sales "); }
		$sql .= sprintf("LEFT JOIN yc_customer AS c ON s.customer = c.customer ");
		$sql .= sprintf("%s ", $where);
		if (!empty($req->s['lot'])) { $sql .= "GROUP BY gd.sales "; }
		$sql .= sprintf("LIMIT %d, %d", (int) $limit, (int) $users_per_page);
//print_r($sql);
		$this->items = $wpdb->get_results( $sql );


		/**
		 * 受注情報を全取得して、repeat分をpushして、delivery_dtでsortした上で、phpでpager用limitを設定する方法
		 **/
		/*
		$limit = ($paged -1) * $users_per_page;
		$sql = sprintf("SELECT s.*, g.name AS goods_name, c.name AS customer_name FROM yc_sales AS s LEFT JOIN yc_goods AS g ON s.goods = g.goods LEFT JOIN yc_customer AS c ON s.customer = c.customer %s", $where);
		$this->items = $wpdb->get_results( $sql );
		*/

		// repeat分を追加
//		foreach ($ret_repeat_items as $i => $d) {
//			array_push($this->items, $d);
//		}

		/*
		foreach ($this->items as $i => $d) {
			$sort[$i] = (array) $d;
		}
		*/

		/*
		echo '<pre>';
		//	print_r(array_column($sort, 'sales'));
		//	print_r(array_multisort(array_column($sort, 'sales'), SORT_ASC, $sort));
		echo '</pre>';
		*/

//		echo '<pre>';
//		print_r($this->items);
//		echo '</pre>';
$total = current($wpdb->get_results( "SELECT count(*) AS count FROM yc_sales;" ));

		$this->set_pagination_args(
			array(
//				'total_items' => $wp_user_search->get_total(),
				'total_items' => $total->count,
				'per_page'    => $users_per_page,
			)
		);
	}

	/**
	 * Make Repeat Items.
	 *
	 *
	 */
	public function makeRepeatItems($repeat_items = null) {
		foreach ($repeat_items as $i => $r) {
			if (!isset($r->sales)) { continue; }

			// copy不要部分を初期化
			$r->base_sales = $r->sales;
			$r->sales = null;
			$r->lot_fg = $r->status = 0;
			$r->rgdt = $r->updt = $r->upuser = null;

			switch ($r->period) { 
				default: 
				case 0: // 毎日
					$period = '+1 day';
					break;

				case 1: // 毎週
					$period = '+1 week';
					break;

				case 2: // 毎月
					$period = '+1 month';
					break;

				case 3: // 毎年
					$period = '+1 year';
					break;
			}
			$delivery_dt = new DateTime($r->delivery_dt);
			$delivery_dt->modify($period);
			$r->delivery_dt = $delivery_dt->format('Y-m-d');

			$arrival_dt = new DateTime($r->arrival_dt);
			$arrival_dt->modify($period);
			$r->arrival_dt = $arrival_dt->format('Y-m-d');

			$ret_repeat_items[] = $r;
		}
		return $ret_repeat_items;
	}

	/**
	 *
	 **/
	function vd($d) {
//return false;
		global $wpdb;
		$cur_user = wp_get_current_user();
		if (current($cur_user->roles) == 'administrator') {
			echo '<div class="border border-success mb-3">';
			echo '<pre>';
//			var_dump($d);
			print_r($d);
			echo '</pre>';
			echo '</div>';
		}
	}

	/**
	 * Output 'no users' message.
	 *
	 * @since 3.1.0
	 */
	public function no_items() {
		_e( 'No users found.' );
	}

	/**
	 * Return an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * Filtersing of the user table.
	 *
	 * @since 3.1.0
	 *
	 * @global string $role
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		global $role;

		$wp_roles = wp_roles();

		if ( $this->is_site_users ) {
			$url = 'site-users.php?id=' . $this->site_id;
			switch_to_blog( $this->site_id );
			$users_of_blog = count_users( 'time', $this->site_id );
			restore_current_blog();
		} else {
			$url           = 'users.php';
			$users_of_blog = count_users();
		}

		$total_users = $users_of_blog['total_users'];
		$avail_roles =& $users_of_blog['avail_roles'];
		unset( $users_of_blog );

		$current_link_attributes = empty( $role ) ? ' class="current" aria-current="page"' : '';

		$role_links        = array();
		$role_links['all'] = sprintf(
			'<a href="%s"%s>%s</a>',
			$url,
			$current_link_attributes,
			sprintf(
				/* translators: %s: Number of users. */
				_nx(
					'All <span class="count">(%s)</span>',
					'All <span class="count">(%s)</span>',
					$total_users,
					'users'
				),
				number_format_i18n( $total_users )
			)
		);

		foreach ( $wp_roles->get_names() as $this_role => $name ) {
			if ( ! isset( $avail_roles[ $this_role ] ) ) {
				continue;
			}

			$current_link_attributes = '';

			if ( $this_role === $role ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$name = translate_user_role( $name );
			$name = sprintf(
				/* translators: 1: User role name, 2: Number of users. */
				__( '%1$s <span class="count">(%2$s)</span>' ),
				$name,
				number_format_i18n( $avail_roles[ $this_role ] )
			);

			$role_links[ $this_role ] = "<a href='" . esc_url( add_query_arg( 'role', $this_role, $url ) ) . "'$current_link_attributes>$name</a>";
		}

		if ( ! empty( $avail_roles['none'] ) ) {

			$current_link_attributes = '';

			if ( 'none' === $role ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$name = __( 'No role' );
			$name = sprintf(
				/* translators: 1: User role name, 2: Number of users. */
				__( '%1$s <span class="count">(%2$s)</span>' ),
				$name,
				number_format_i18n( $avail_roles['none'] )
			);

			$role_links['none'] = "<a href='" . esc_url( add_query_arg( 'role', 'none', $url ) ) . "'$current_link_attributes>$name</a>";
		}

		return $role_links;
	}

	/**
	 * Retrieve an associative array of bulk actions available on this table.
	 *
	 * @since 3.1.0
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		if ( is_multisite() ) {
			if ( current_user_can( 'remove_users' ) ) {
				$actions['remove'] = __( 'Remove' );
			}
		} else {
			if ( current_user_can( 'delete_users' ) ) {
				$actions['delete'] = __( 'Delete' );
			}
		}

		// Add a password reset link to the bulk actions dropdown.
		if ( current_user_can( 'edit_users' ) ) {
			$actions['resetpassword'] = __( 'Send password reset' );
		}

//		return $actions;
		return false;
	}

	/**
	 * Output the controls to allow user roles to be changed in bulk.
	 *
	 * @since 3.1.0
	 *
	 * @param string $which Whether this is being invoked above ("top")
	 *                      or below the table ("bottom").
	 */
	protected function extra_tablenav( $which ) {
		return false;
		$id        = 'bottom' === $which ? 'new_role2' : 'new_role';
		$button_id = 'bottom' === $which ? 'changeit2' : 'changeit';
		?>
	<div class="alignleft actions">
		<?php if ( current_user_can( 'promote_users' ) && $this->has_items() ) : ?>
		<label class="screen-reader-text" for="<?php echo $id; ?>"><?php _e( 'Change role to&hellip;' ); ?></label>
		<select name="<?php echo $id; ?>" id="<?php echo $id; ?>">
			<option value=""><?php _e( 'Change role to&hellip;' ); ?></option>
			<?php wp_dropdown_roles(); ?>
			<option value="none"><?php _e( '&mdash; No role for this site &mdash;' ); ?></option>
		</select>
			<?php
			submit_button( __( 'Change' ), '', $button_id, false );
		endif;

		/**
		 * Fires just before the closing div containing the bulk role-change controls
		 * in the Users list table.
		 *
		 * @since 3.5.0
		 * @since 4.6.0 The `$which` parameter was added.
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'restrict_manage_users', $which );
		?>
		</div>
		<?php
		/**
		 * Fires immediately following the closing "actions" div in the tablenav for the users
		 * list table.
		 *
		 * @since 4.9.0
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'manage_users_extra_tablenav', $which );
	}

	/**
	 * Capture the bulk action required, and return it.
	 *
	 * Overridden from the base class implementation to capture
	 * the role change drop-down.
	 *
	 * @since 3.1.0
	 *
	 * @return string The bulk action required.
	 */
	public function current_action() {
		if ( isset( $_REQUEST['changeit'] ) && ! empty( $_REQUEST['new_role'] ) ) {
			return 'promote';
		}

		return parent::current_action();
	}

	/**
	 * Get a list of columns for the list table.
	 *
	 * @since 3.1.0
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		$c = array(
			'cb'       => '',
			'sales' => __( '注文ID' ),
			'name'     => __( 'Name' ),
			'qty'    => __( 'Email' ),
			'role'     => __( 'Role' ),
			'posts'    => __( 'Posts' ),
		);

		if ( $this->is_site_users ) {
			unset( $c['posts'] );
		}

		return $c;
	}

	/**
	 * Get a list of sortable columns for the list table.
	 *
	 * @since 3.1.0
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		$c = array(
			'username' => 'login',
			'email'    => 'email',
		);

		return $c;
	}

	/**
	 * Generate the list table rows.
	 *
	 * @since 3.1.0
	 */
	public function display_rows() {
		// Query the post counts for this page.
		if ( ! $this->is_site_users ) {
			$post_counts = count_many_users_posts( array_keys( $this->items ) );
		}

/*
		foreach ( $this->items as $userid => $user_object ) {
			echo "\n\t" . $this->single_row( $user_object, '', '', isset( $post_counts ) ? $post_counts[ $userid ] : 0 );
		}
*/

$s = new Sales();
$initForm = $s->getInitForm();

		foreach ( $this->items as $id => $object ) {
//			echo "\n\t" . $this->single_row( $user_object, '', '', isset( $post_counts ) ? $post_counts[ $userid ] : 0 );
			if ($object->repeat_fg == 1) {
				if (!$object->base_sales) {
					echo '<tr style="background: plum;">';
				} else {
					echo '<tr style="background: pink;">';
				}
			} else {
				echo '<tr>';
			}

			$qty = sprintf('%.1f', $object->qty);

			echo '<td><input type="checkbox" id="no_'. $id. '" name="no[]" value="'. $object->sales. '" /></td>';
			echo '<input type="hidden" id="arr_goods" name="arr_goods['. $object->sales. ']" value="'. $object->goods. '" />';
			echo '<input type="hidden" id="arr_qty" name="arr_qty['. $object->sales. ']" value="'. $qty. '" />';
//			echo '<input type="hidden" id="arr_repeat" name="arr_repeat[]" value="{{$list->repeat}}" />';
//			echo '<input type="hidden" id="arr_delivery_dt" name="arr_delivery_dt[]" value="{{$list->delivery_dt}}" />';
			echo '<td><a href="/wp-admin/admin.php?page=sales-detail&sales='. $object->sales. '&action=edit">'. sprintf('%07d', $object->sales). '</a></td>';
			echo '<td><a href="/wp-admin/admin.php?page=customer-detail&customer='. $object->customer. '&action=edit">'. $object->customer_name. '</a></td>';
			$separately = ($object->separately_fg == true) ? mb_convert_encoding(" （バラ）", "UTF-8", "SJIS"): null;
			echo '<td><a href="/wp-admin/admin.php?page=goods-detail&goods='. $object->goods. '&action=edit">'. $object->goods_name. $separately. '</a></td>';
//			echo '<td><a href="/wp-admin/admin.php?page=lot-regist&sales='. $object->sales. '&goods='. $object->goods. '&action=save">'. $object->qty. '</a></td>';
			echo '<td>';
			if ($object->status == '0') {
				echo ': '. $qty. ' :';
			} else {
				echo '<a href="/wp-admin/admin.php?page=lot-regist&sales='. $object->sales. '&goods='. $object->goods. '&customer='. $object->customer. '&action=save"> [ '. $qty. ' ] </a>';
			}
			echo '</td>';
			echo '<td>'. $object->delivery_dt. '</td>';
			echo '<td>'. $object->arrival_dt. '</td>';
			if ($object->status == '0') {
				echo '<td><span class="text-danger">'. mb_convert_encoding("未確定", "UTF-8", "SJIS"). '</span></td>';
//				echo '<td><span class="text-danger">{{ $initForm["select"]["status"][$object->status] }}</span></td>';
			} else {
				echo '<td><span class="text-success">'. mb_convert_encoding("確定", "UTF-8", "SJIS"). '</span></td>';
//				echo '<td><span class="text-success">{{ $initForm['select']['status'][$list->status] }}</span></td>';
			}
			echo '</tr>';
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 * @since 3.1.0
	 * @since 4.2.0 The `$style` parameter was deprecated.
	 * @since 4.4.0 The `$role` parameter was deprecated.
	 *
	 * @param WP_User $user_object The current user object.
	 * @param string  $style       Deprecated. Not used.
	 * @param string  $role        Deprecated. Not used.
	 * @param int     $numposts    Optional. Post count to display for this user. Defaults
	 *                             to zero, as in, a new user has made zero posts.
	 * @return string Output for a single row.
	 */
	public function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		if ( ! ( $user_object instanceof WP_User ) ) {
			$user_object = get_userdata( (int) $user_object );
		}
		$user_object->filter = 'display';
		$email               = $user_object->user_email;

		if ( $this->is_site_users ) {
			$url = "site-users.php?id={$this->site_id}&amp;";
		} else {
			$url = 'users.php?';
		}

		$user_roles = $this->get_role_list( $user_object );

		// Set up the hover actions for this user.
		$actions     = array();
		$checkbox    = '';
		$super_admin = '';

		if ( is_multisite() && current_user_can( 'manage_network_users' ) ) {
			if ( in_array( $user_object->user_login, get_super_admins(), true ) ) {
				$super_admin = ' &mdash; ' . __( 'Super Admin' );
			}
		}

		// Check if the user for this row is editable.
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link.
			$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );

			if ( current_user_can( 'edit_user', $user_object->ID ) ) {
				$edit            = "<strong><a href=\"{$edit_link}\">{$user_object->user_login}</a>{$super_admin}</strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			} else {
				$edit = "<strong>{$user_object->user_login}{$super_admin}</strong><br />";
			}

			if ( ! is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) ) {
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Delete' ) . '</a>';
			}
			if ( is_multisite() && current_user_can( 'remove_user', $user_object->ID ) ) {
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url( $url . "action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Remove' ) . '</a>';
			}

			// Add a link to the user's author archive, if not empty.
			$author_posts_url = get_author_posts_url( $user_object->ID );
			if ( $author_posts_url ) {
				$actions['view'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( $author_posts_url ),
					/* translators: %s: Author's display name. */
					esc_attr( sprintf( __( 'View posts by %s' ), $user_object->display_name ) ),
					__( 'View' )
				);
			}

			// Add a link to send the user a reset password link by email.
			if ( get_current_user_id() !== $user_object->ID && current_user_can( 'edit_user', $user_object->ID ) ) {
				$actions['resetpassword'] = "<a class='resetpassword' href='" . wp_nonce_url( "users.php?action=resetpassword&amp;users=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Send password reset' ) . '</a>';
			}

			/**
			 * Filters the action links displayed under each user in the Users list table.
			 *
			 * @since 2.8.0
			 *
			 * @param string[] $actions     An array of action links to be displayed.
			 *                              Default 'Edit', 'Delete' for single site, and
			 *                              'Edit', 'Remove' for Multisite.
			 * @param WP_User  $user_object WP_User object for the currently listed user.
			 */
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );

			// Role classes.
			$role_classes = esc_attr( implode( ' ', array_keys( $user_roles ) ) );

			// Set up the checkbox (because the user is editable, otherwise it's empty).
			$checkbox = sprintf(
				'<label class="screen-reader-text" for="user_%1$s">%2$s</label>' .
				'<input type="checkbox" name="users[]" id="user_%1$s" class="%3$s" value="%1$s" />',
				$user_object->ID,
				/* translators: %s: User login. */
				sprintf( __( 'Select %s' ), $user_object->user_login ),
				$role_classes
			);

		} else {
			$edit = "<strong>{$user_object->user_login}{$super_admin}</strong>";
		}

		$avatar = get_avatar( $user_object->ID, 32 );

		// Comma-separated list of user roles.
		$roles_list = implode( ', ', $user_roles );

		$r = "<tr id='user-$user_object->ID'>";

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}
			if ( 'posts' === $column_name ) {
				$classes .= ' num'; // Special case for that column.
			}

			if ( in_array( $column_name, $hidden, true ) ) {
				$classes .= ' hidden';
			}

			$data = 'data-colname="' . esc_attr( wp_strip_all_tags( $column_display_name ) ) . '"';

			$attributes = "class='$classes' $data";

			if ( 'cb' === $column_name ) {
				$r .= "<th scope='row' class='check-column'>$checkbox</th>";
			} else {
				$r .= "<td $attributes>";
				switch ( $column_name ) {
					case 'username':
						$r .= "$avatar $edit";
						break;
					case 'name':
						if ( $user_object->first_name && $user_object->last_name ) {
							$r .= "$user_object->first_name $user_object->last_name";
						} elseif ( $user_object->first_name ) {
							$r .= $user_object->first_name;
						} elseif ( $user_object->last_name ) {
							$r .= $user_object->last_name;
						} else {
							$r .= sprintf(
								'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
								_x( 'Unknown', 'name' )
							);
						}
						break;
					case 'email':
						$r .= "<a href='" . esc_url( "mailto:$email" ) . "'>$email</a>";
						break;
					case 'role':
						$r .= esc_html( $roles_list );
						break;
					case 'posts':
						if ( $numposts > 0 ) {
							$r .= sprintf(
								'<a href="%s" class="edit"><span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
								"edit.php?author={$user_object->ID}",
								$numposts,
								sprintf(
									/* translators: %s: Number of posts. */
									_n( '%s post by this author', '%s posts by this author', $numposts ),
									number_format_i18n( $numposts )
								)
							);
						} else {
							$r .= 0;
						}
						break;
					default:
						/**
						 * Filters the display output of custom columns in the Users list table.
						 *
						 * @since 2.8.0
						 *
						 * @param string $output      Custom column output. Default empty.
						 * @param string $column_name Column name.
						 * @param int    $user_id     ID of the currently-listed user.
						 */
						$r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
				}

				if ( $primary === $column_name ) {
					$r .= $this->row_actions( $actions );
				}
				$r .= '</td>';
			}
		}
		$r .= '</tr>';

		return $r;
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 *
	 * @return string Name of the default primary column, in this case, 'username'.
	 */
	protected function get_default_primary_column_name() {
		return 'username';
	}

	/**
	 * Returns an array of user roles for a given user object.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_User $user_object The WP_User object.
	 * @return string[] An array of user roles.
	 */
	protected function get_role_list( $user_object ) {
		$wp_roles = wp_roles();

		$role_list = array();

		foreach ( $user_object->roles as $role ) {
			if ( isset( $wp_roles->role_names[ $role ] ) ) {
				$role_list[ $role ] = translate_user_role( $wp_roles->role_names[ $role ] );
			}
		}

		if ( empty( $role_list ) ) {
			$role_list['none'] = _x( 'None', 'no user roles' );
		}

		/**
		 * Filters the returned array of roles for a user.
		 *
		 * @since 4.4.0
		 *
		 * @param string[] $role_list   An array of user roles.
		 * @param WP_User  $user_object A WP_User object.
		 */
		return apply_filters( 'get_role_list', $role_list, $user_object );
	}

	public function get_column_info() {
		return array(
			array(
				'cb' => '#', 
				'sales' => mb_convert_encoding('注文番号', 'UTF-8', 'SJIS'), 
				'name' => mb_convert_encoding('注文者名', 'UTF-8', 'SJIS'), 
				'goods' => mb_convert_encoding('商品', 'UTF-8', 'SJIS'), 
				'qty' => mb_convert_encoding('個数', 'UTF-8', 'SJIS'), 
				'' => mb_convert_encoding('出庫倉庫', 'UTF-8', 'SJIS'), 
				'arrival_dt' => mb_convert_encoding('引取(入庫)予定日', 'UTF-8', 'SJIS'), 
				'' => mb_convert_encoding('配送予定日', 'UTF-8', 'SJIS'), 
				'status' => mb_convert_encoding('状態(確定｜未確定｜削除)', 'UTF-8', 'SJIS')
			),
			array(
			),
			array(
			),
			array(
			),
		);
	}

	/**
	 * Displays the search box.
	 *
	 * @since 3.1.0
	 *
	 * @param string $text	 The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {
			if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
					return;
			}

			$input_id = $input_id . '-search-input';

			if ( ! empty( $_REQUEST['orderby'] ) ) {
					echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['order'] ) ) {
					echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
					echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['detached'] ) ) {
					echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
			}
			?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
	<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
</p>
			<?php
	}
}
