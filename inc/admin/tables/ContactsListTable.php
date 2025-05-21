<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once 'ABSPATH' . 'wp-admin/includes/class-wp-list-table.php';
}

class ContactsListTable extends WP_List_Table {

	public function prepare_items(): void {

		$this->process_bulk_action();

		$contacts = zoho_get_contacts();
		$data     = [];

		foreach ( $contacts as $index => $contact ) {
			$data[] = [
				'ID'         => $contact['id'],
				'full_name'  => $contact['Full_Name'],
				'name'       => $contact['First_Name'],
				'family'     => $contact['Last_Name'],
				'email'      => $contact['Email'],
				'phone'      => $contact['Phone'],
				'department' => $contact['Department'],
				'edit'       => $contact['$editable']
			];
		}

		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = [
			'full_name' => [ 'full_name', true ],
		];

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$order_by = $_GET['orderby'] ?? '';
		$order    = $_GET['order'] ?? 'asc';

		if ( $order_by && isset( $data[0][ $order_by ] ) ) {
			usort( $data, function ( $a, $b ) use ( $order_by, $order ) {
				if ( $order === 'asc' ) {
					return strcmp( $a[ $order_by ], $b[ $order_by ] );
				} else {
					return strcmp( $b[ $order_by ], $a[ $order_by ] );
				}
			} );
		}

		$this->items = $data;
	}

	public function get_columns(): array {
		return [
			'cb'         => '<input type="checkbox"/>',
			'full_name'  => 'Full Name',
			'email'      => 'Email',
			'phone'      => 'Phone',
			'department' => 'Department',
		];
	}

	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		}

		return '-';
	}

	public function get_bulk_actions(): array {
		return [
			'delete' => 'Delete',
		];
	}

	public function process_bulk_action(): void {
		if ( $this->current_action() === 'delete' ) {
			$ids           = $_POST['contact_ids'];
			$deleted_items = 0;
			foreach ( $ids as $id ) {
				$deleted = zoho_delete_contact( $id );
				if ( $deleted ) {
					$deleted_items ++;
				}
			}
			if ( $deleted_items > 0 ) {
				echo '<div class="wrap"><div class="notice notice-success is-dismissible"><p>' . $deleted_items . ' conatct deleted.</p></div></div>';
			} else {
				echo '<div class="wrap"><div class="notice notice-error is-dismissible"><p>خطا در ذخیره‌سازی اطلاعات.</p></div></div>';
			}
		}
	}

	public function column_full_name( $item ): string {
		if ( $item['edit'] == 1 ) {
			$edit_url   = admin_url( 'admin.php?page=zoho_edit_contact&id=' . $item['ID'] );
			$delete_url = wp_nonce_url(
				admin_url( 'admin.php?page=zoho_contacts&action=delete&id=' . $item['ID'] ),
				'zoho_delete_contact_' . $item['ID']
			);
			$actions    = [
				'edit'   => '<a href="' . esc_url( $edit_url ) . '">Edit</a>',
				'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'Are You Sure?\')">Delete</a>',
			];

			$main_column = '<strong><a class="row-title" href="' . esc_url( $edit_url ) . '">' . $item['full_name'] . '</a></strong>';

			return $main_column . $this->row_actions( $actions );
		}

		return $item['full_name'];
	}

	public function column_cb( $item ): string {
		return '<input type="checkbox" name="contact_ids[]" value="' . $item['ID'] . '"/>';
	}
}