<?php
/**
 * List Table API: YC_Stock_List_Table class
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
class YC_Stock_List_Table extends WP_List_Table {

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
			$Stock = new Stock;
			$ret = $Stock->deleteStockDetail($req->no);
		}

//print_r($req);
$where = sprintf("WHERE st.stock is not null ");
if (!empty($req->s['no'])) { $where .= sprintf("AND st.stock = '%s'", $req->s['no']); }
if (!empty($req->s['goods_name'])) { $where .= "AND g.name LIKE '%". $req->s['goods_name']. "%'"; }
if (!empty($req->s['qty'])) { $where .= sprintf("AND g.qty = '%s'", $req->s['qty']); }
if (!empty($req->s['lot'])) { $where .= "AND std.lot LIKE '%". $req->s['lot']. "%'"; }
if (!empty($req->s['outgoing_warehouse'])) { $where .= sprintf("AND st.warehouse = '%s'", $req->s['outgoing_warehouse']); }
if (!empty($req->s['arrival_s_dt'])) { $where .= sprintf("AND st.arrival_dt >= '%s 00:00:00' ", $req->s['arrival_s_dt']); }
if (!empty($req->s['arrival_e_dt'])) { $where .= sprintf("AND st.arrival_dt <= '%s 23:59:59' ", $req->s['arrival_e_dt']); }
if (!empty($req->s['transfer_fg'])) { $where .= sprintf("AND st.transfer_fg = '%s'", $req->s['transfer_fg']); }
//print_r($where);

$limit = ($paged -1) * $users_per_page;
//print_r($limit);
//print_r($users_per_page);
$sql = sprintf("SELECT st.stock, st.arrival_dt, st.warehouse, st.transfer_fg, g.name AS goods_name, g.separately_fg, g.qty, std.lot, st.goods_total, st.stock AS stock FROM yc_stock AS st ");
$sql .= sprintf("LEFT JOIN yc_stock_detail AS std ON st.stock = std.stock ");
$sql .= sprintf("LEFT JOIN yc_goods AS g ON st.goods = g.goods ");
$sql .= sprintf("%s ", $where);
$sql .= sprintf("GROUP BY st.stock ");
//$sql .= sprintf("ORDER BY g.goods ASC ");
$sql .= sprintf("LIMIT %d, %d", (int) $limit, (int) $users_per_page);
//print_r($sql);

$this->items = $wpdb->get_results( $sql );

$total = current($wpdb->get_results( "SELECT count(*) AS count FROM yc_stock;" ));

		$this->set_pagination_args(
			array(
//				'total_items' => $wp_user_search->get_total(),
				'total_items' => $total->count,
				'per_page'    => $users_per_page,
			)
		);
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
			'cb'       => '<input type="checkbox" />',
			'goods' => __( '商品ID' ),
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
		foreach ( $this->items as $id => $object ) {
//			echo "\n\t" . $this->single_row( $user_object, '', '', isset( $post_counts ) ? $post_counts[ $userid ] : 0 );
			echo '<tr>';
			echo '<td><input type="checkbox" id="no_'. $id. '" name="no[]" value="'. $object->stock. '" /></td>';
			echo '<td><a href="/wp-admin/admin.php?page=stock-detail&stock='. $object->stock. '&action=edit">'. sprintf('STOCK-%07d', $object->stock). '</a></td>';
			echo '<td><a href="/wp-admin/admin.php?page=stock-bulk&arrival_dt='. $object->arrival_dt. '&warehouse='. $object->warehouse. '&action=edit">'. $object->arrival_dt. '</a></td>';
			$separately = ($object->separately_fg == true) ? mb_convert_encoding(" （バラ）", "UTF-8", "SJIS"): null;
			echo '<td>'. $object->goods_name. $separately. '</td>';
			echo '<td>'. $object->qty. '</td>';
			echo '<td><a href="/wp-admin/admin.php?page=stock-lot-regist&stock='. $object->stock. '&goods='. $object->goods. '&arrival_dt='. $object->arrival_dt. '&warehouse='. $object->warehouse. '"> [ '. $object->goods_total. ' ] </a></td>';
			echo '<td>'. $object->lot. '</td>';
			$transfer = ($object->transfer_fg == true) ? '<input type="button" onclick="cancel_transfer('. $object->stock. ');" value="'. mb_convert_encoding("転送取消", "UTF-8", "SJIS"). '">' : '';
			echo '<td>'. $transfer. '</td>';
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

/*
 *  ./includes/class-wp-list-table.php
                        $data = (array) $data;
                        // Descending initial sorting.
                        if ( ! isset( $data[1] ) ) {
                                $data[1] = false;
                        }
                        // Current sorting translatable string.
                        if ( ! isset( $data[2] ) ) {
                                $data[2] = '';
                        }
                        // Initial view sorted column and asc/desc order, default: false.
                        if ( ! isset( $data[3] ) ) {
                                $data[3] = false;
                        }
                        // Initial order for the initial sorted column, default: false.
                        if ( ! isset( $data[4] ) ) {
                                $data[4] = false;
                        }
*/
		return array(
			array(
				'cb' => '#', 
				'stock' => mb_convert_encoding('在庫番号', 'UTF-8', 'SJIS'), 
				'arrival_dt' => mb_convert_encoding('入庫日', 'UTF-8', 'SJIS'), 
				'goods_name' => mb_convert_encoding('商品名', 'UTF-8', 'SJIS'), 
				'amt' => mb_convert_encoding('荷姿・容量', 'UTF-8', 'SJIS'), 
				'stock_cnt' => mb_convert_encoding('個数', 'UTF-8', 'SJIS'), 
				'lot' => mb_convert_encoding('ロット番号', 'UTF-8', 'SJIS'), 
				'transfer_fg' => mb_convert_encoding('転送フラグ', 'UTF-8', 'SJIS'), 
			), 
			array(
			),
			array(
			),
			array(
				'goods'
			)
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
