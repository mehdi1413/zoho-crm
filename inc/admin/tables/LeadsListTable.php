<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once 'ABSPATH' . 'wp-admin/includes/class-wp-list-table.php';
}

class LeadsListTable extends WP_List_Table {

	public function prepare_items(): void {
		$this->process_bulk_action();

		$leads = zoho_get_leads();
		$data  = [];

		foreach ( $leads as $index => $lead ) {
			$data[] = [
				'ID'      => $lead['id'],
				'name'    => $lead['Full_Name'],
				'company' => $lead['Company'],
				'email'   => $lead['Email'],
				'phone'   => $lead['Phone'],
				'source'  => $lead['Lead_Source'],
				'edit'    => $lead['$editable']
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
			'cb'      => '<input type="checkbox"/>',
			'name'    => 'Lead Name',
			'company' => 'Company',
			'email'   => 'Email',
			'phone'   => 'Phone',
			'source'  => 'Call Type',
		];
	}

	public function get_bulk_actions(): array {
		return [ 'delete' => 'Delete' ];
	}

	public function process_bulk_action(): void {
		if ( $this->current_action() === 'delete' ) {
			$lead_ids      = $_POST['leads'];
			$deleted_items = 0;
			foreach ($lead_ids as $lead_id){
				$deleted = zoho_delete_lead( $lead_id );
				if ( $deleted ) {
					$deleted_items ++;
				}
			}
			if ( $deleted_items > 0 ) {
				echo '<div class="wrap"><div class="notice notice-success is-dismissible"><p>' . $deleted_items . ' lead deleted.</p></div></div>';
			} else {
				echo '<div class="wrap"><div class="notice notice-error is-dismissible"><p>Error storing information.</p></div></div>';
			}
		}
	}

	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		}

		return '-';
	}

	public function column_name( $item ): string {
		if ( $item['edit'] == 1 ) {
			$edit_url   = admin_url( 'admin.php?page=zoho_edit_lead&id=' . $item['ID'] );
			$delete_url = wp_nonce_url(
				admin_url( 'admin.php?page=zoho_leads&action=delete&id=' . $item['ID'] ),
				'zoho_delete_list_' . $item['ID']
			);

			$actions = [
				'edit'   => '<a href="' . esc_url( $edit_url ) . '">Edit</a>',
				'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'Are You Sure?\')">Delete</a>',
			];

			$main_column = '<strong><a class="row-title" href="' . esc_url( $edit_url ) . '">' . $item['name'] . '</a></strong>';

			return $main_column . $this->row_actions( $actions );
		}

		return $item['name'];
	}

	public function column_cb( $item ): string {
		return '<input type="checkbox" name="leads[]" value="' . $item['ID'] . '" />';
	}

}