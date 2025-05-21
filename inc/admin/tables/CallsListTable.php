<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once 'ABSPATH' . 'wp-admin/includes/class-wp-list-table.php';
}

class CallsListTable extends WP_List_Table {

	public function prepare_items(): void {
		$this->process_bulk_action();

		$calls = zoho_get_calls();

		$data = [];

		foreach ( $calls as $call ) {
			$data[] = [
				'ID'         => $call['id'],
				'subject'    => $call['Subject'],
				'type'       => $call['Call_Type'],
				'related'    => $call['What_Id']['name'],
				'contact'    => $call['Who_Id']['name'],
				'contact_id' => $call['Who_Id']['id'],
				'time'       => $call['Call_Start_Time'],
				'duration'   => $call['Call_Duration'],
				'edit'       => $call['$editable']
			];
		}

		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = [];

		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$this->items           = $data;
	}

	public function get_columns(): array {
		return [
			'cb'       => '<input type="checkbox" />',
			'subject'  => 'Subject',
			'type'     => 'Call Type',
			'related'  => 'Account',
			'contact'  => 'Contact',
			'time'     => 'Call Start Time',
			'duration' => 'Call Duration',
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
			'delete' => 'Delete'
		];
	}

	public function process_bulk_action(): void {
		if ( $this->current_action() === 'delete' ) {
			$ids           = $_POST['call_ids'];
			$deleted_items = 0;
			foreach ( $ids as $id ) {
				$deleted = zoho_delete_call( $id );
				if ( $deleted ) {
					$deleted_items ++;
				}
			}
			if ( $deleted_items > 0 ) {
				echo '<div class="wrap"><div class="notice notice-success is-dismissible"><p>' . $deleted_items . ' call deleted.</p></div></div>';
			} else {
				echo '<div class="wrap"><div class="notice notice-error is-dismissible"><p>خطا در ذخیره‌سازی اطلاعات.</p></div></div>';
			}
		}
	}

	public function column_cb( $item ): string {
		return '<input type="checkbox" name="call_ids[]" value="' . $item['ID'] . '" />';
	}

	public function column_subject( $item ) {
		if ( $item['edit'] == 1 ) {
			$edit_url   = admin_url( 'admin.php?page=zoho_edit_call&id=' . $item['ID'] );
			$delete_url = wp_nonce_url(
				admin_url( 'admin.php?page=zoho_calls&action=delete&id=' . $item['ID'] ),
				'zoho_delete_call_' . $item['ID']
			);

			$actions = [
				'edit'   => '<a href="' . esc_url( $edit_url ) . '">Edit</a>',
				'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'آیا مطمئن هستید؟\')">Delete</a>',
			];

			$main_column = '<strong><a class="row-title" href="' . esc_url( $edit_url ) . '">' . $item['subject'] . '</a></strong>';

			return $main_column . $this->row_actions( $actions );
		}

		return $item['subject'];
	}

	public function column_contact( $item ): string {
		if ( $item['contact'] && $item['contact_id'] ) {
			$url = admin_url( 'admin.php?page=zoho_edit_contact&id=' . $item['contact_id'] );

			return '<a href="' . esc_url( $url ) . '">' . esc_html( $item['contact'] ) . '</a>';
		}

		return '-';
	}
}