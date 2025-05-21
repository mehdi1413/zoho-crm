<?php
defined( 'ABSPATH' ) || exit;

function zoho_add_menu_page(): void {

	$menu_suffix = add_menu_page(
		'zoho crm data',
		'zoho crm data',
		'manage_options',
		'zoho_crm',
		'zoho_crm_page_html',
		'dashicons-editor-table',
	);

	$contacts_suffix = add_submenu_page(
		'zoho_crm',
		'contacts data',
		'contacts',
		'manage_options',
		'zoho_contacts',
		'zoho_show_contacts_html',
	);

	$leads_suffix = add_submenu_page(
		'zoho_crm',
		'leads data',
		'leads',
		'manage_options',
		'zoho_leads',
		'zoho_leads_html',
	);

	$calls_suffix = add_submenu_page(
		'zoho_crm',
		'calls data',
		'calls',
		'manage_options',
		'zoho_calls',
		'zoho_calls_html',
	);

	$contact_edit_suffix = add_submenu_page(
		null,
		'edit contact',
		'edit contact',
		'manage_options',
		'zoho_edit_contact',
		'zoho_edit_contact_html',
	);

	$leads_edit_suffix = add_submenu_page(
		null,
		'edit lead',
		'edit lead',
		'manage_options',
		'zoho_edit_lead',
		'zoho_edit_lead_html',
	);

	$calls_edit_suffix = add_submenu_page(
		null,
		'edit call',
		'edit call',
		'manage_options',
		'zoho_edit_call',
		'zoho_edit_call_html',
	);

	$contact_add_suffix = add_submenu_page(
		null,
		'add new contact',
		'add new contact',
		'manage_options',
		'zoho_add_contact',
		'zoho_add_contact_html',
	);

	$lead_add_suffix = add_submenu_page(
		null,
		'add new lead',
		'add new lead',
		'manage_options',
		'zoho_add_lead',
		'zoho_add_lead_html',
	);

	$call_add_suffix = add_submenu_page(
		null,
		'add new call',
		'add new call',
		'manage_options',
		'zoho_add_call',
		'zoho_add_call_html',
	);
}

add_action( 'admin_menu', 'zoho_add_menu_page' );

function zoho_crm_page_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}
	include ZOHO_VIEW_PATH . 'main.php';
}

// CONTACTS CRUD METHODS
function zoho_show_contacts_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}
	require ZOHO_TABLE_PATH . 'ContactsListTable.php';
	$contacts = new ContactsListTable();
	$contacts->prepare_items();

	if ( isset( $_GET['deleted'] ) ) {
		if ( $_GET['deleted'] == '1' ) {
			echo '<div class="notice notice-success is-dismissible"><p>The contact was successfully deleted.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>An error occurred while deleting the contact.</p></div>';
		}
	}

	include ZOHO_VIEW_PATH . 'contact/contact.php';
}

function zoho_handle_delete_contact(): void {
	if (
		isset( $_GET['page'], $_GET['action'], $_GET['id'], $_GET['_wpnonce'] )
		&& $_GET['page'] === 'zoho_contacts'
		&& $_GET['action'] === 'delete'
	) {
		$contact_id = sanitize_text_field( $_GET['id'] );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to perform this operation.' );
		}
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'zoho_delete_contact_' . $contact_id ) ) {
			wp_die( 'Security error: The token is invalid.' );
		}

		$deleted = zoho_delete_contact( $contact_id );

		$redirect_url = admin_url( 'admin.php?page=zoho_contacts' );

		if ( $deleted ) {
			$redirect_url = add_query_arg( 'deleted', '1', $redirect_url );
		} else {
			$redirect_url = add_query_arg( 'deleted', '0', $redirect_url );
		}

		wp_redirect( $redirect_url );
		exit;
	}
}

add_action( 'admin_init', 'zoho_handle_delete_contact' );

function zoho_add_contact_html(): void {
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_add_contact_action' ) ) {
		$data = [
			'First_Name'  => sanitize_text_field( $_POST['first_name'] ),
			'Last_Name'   => sanitize_text_field( $_POST['last_name'] ),
			'Email'       => sanitize_email( $_POST['email'] ),
			'Phone'       => sanitize_text_field( $_POST['phone'] ),
			'Department'  => sanitize_text_field( $_POST['department'] ),
			'Description' => sanitize_textarea_field( $_POST['description'] ),
		];

		$new_contact = zoho_add_contact( $data );

		if ( $new_contact ) {
			echo '<div class="wrap"><div class="notice notice-success is-dismissible"><p>Contact was successfully added.</p></div></div>';
		} else {
			echo '<div class="wrap"><div class="notice notice-error is-dismissible"><p>An error occurred during the process of adding a new contact.</p></div></div>';
		}
	}

	include ZOHO_VIEW_PATH . 'contact/add-contact.php';
}

function zoho_edit_contact_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}
	$contact_id = $_GET['id'] ?? '';
	if ( empty( $contact_id ) ) {
		echo '<div class="wrap"><div class="notice notice-error"><p>The contact ID is not specified.</p></div></div>';

		return;
	}
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_edit_contact_action' ) ) {
		$data    = [
			'First_Name' => sanitize_text_field( $_POST['first_name'] ),
			'Last_Name'  => sanitize_text_field( $_POST['last_name'] ),
			'Email'      => sanitize_email( $_POST['email'] ),
			'Phone'      => sanitize_text_field( $_POST['phone'] ),
			'Department' => sanitize_text_field( $_POST['department'] ),
		];
		$updated = zoho_update_contact( $contact_id, $data );

		if ( $updated ) {
			echo '<div class="wrap"><div class="notice notice-success"><p>The contact was successfully updated.</p></div></div>';
		} else {
			echo '<div class="wrap"><div class="notice notice-error"><p>Error storing information.</p></div></div>';
		}
	}

	$contact = zoho_get_contact_by_id( $contact_id );
	if ( ! $contact ) {
		echo '<div class="wrap"><div class="notice notice-error"><p>Contact not found.</p></div></div>';

		return;
	}

	include ZOHO_VIEW_PATH . 'contact/edit-contact.php';
}


// LEADS CRUD METHODS
function zoho_leads_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}

	require ZOHO_TABLE_PATH . 'LeadsListTable.php';
	$leads = new LeadsListTable();
	$leads->prepare_items();

	if ( isset( $_GET['deleted'] ) ) {
		if ( $_GET['deleted'] == '1' ) {
			echo '<div class="notice notice-success is-dismissible"><p>The lead was successfully removed.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>An error occurred while deleting the lead.</p></div>';
		}
	}

	include ZOHO_VIEW_PATH . 'lead/leads.php';
}

function zoho_add_lead_html(): void {
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_add_lead_action' ) ) {
		$data = [
			'Company'     => sanitize_text_field( $_POST['company'] ),
			'First_Name'  => sanitize_text_field( $_POST['first_name'] ),
			'Last_Name'   => sanitize_text_field( $_POST['last_name'] ),
			'Email'       => sanitize_email( $_POST['email'] ),
			'Phone'       => sanitize_text_field( $_POST['phone'] ),
			'Lead_Source' => sanitize_text_field( $_POST['lead_source'] ),
			'Salutation'  => sanitize_text_field( $_POST['salutation'] ),
		];

		$new_lead = zoho_add_lead( $data );

		if ( $new_lead ) {
			echo '<div class="notice notice-success is-dismissible"><p>The lead was successfully added.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>Error storing information.</p></div>';
		}
	}

	$salutations  = zoh_get_salutations();
	$lead_sources = zoho_get_lead_sources();
	include ZOHO_VIEW_PATH . 'lead/add-lead.php';
}

function zoho_edit_lead_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}

	$lead_id = $_GET['id'] ?? '';
	if ( empty( $lead_id ) ) {
		echo '<div class="wrap"><div class="notice notice-error"><p>The lead ID is not specified.</p></div></div>';

		return;
	}

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_edit_lead_action' ) ) {
		$data = [
			'First_Name'  => sanitize_text_field( $_POST['first_name'] ),
			'Last_Name'   => sanitize_text_field( $_POST['last_name'] ),
			'Company'     => sanitize_text_field( $_POST['company'] ),
			'Email'       => sanitize_email( $_POST['email'] ),
			'Phone'       => sanitize_text_field( $_POST['phone'] ),
			'Lead_Source' => sanitize_text_field( $_POST['lead_source'] ),
			'Salutation'  => sanitize_text_field( $_POST['salutation'] ),
		];

		$updated = zoho_update_lead( $lead_id, $data );

		if ( $updated ) {
			echo '<div class="notice notice-success is-dismissible"><p>The contact was successfully updated.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>Error storing information.</p></div>';
		}
	}

	$lead = zoho_get_lead_by_id( $lead_id );

	if ( ! $lead ) {
		echo '<div class="notice notice-error is-dismissible"><p>Lead Not Found.</p></div>';

		return;
	}

	$salutations  = zoh_get_salutations();
	$lead_sources = zoho_get_lead_sources();
	include ZOHO_VIEW_PATH . 'lead/edit-lead.php';
}

function zoho_handle_delete_lead(): void {
	if (
		isset( $_GET['page'], $_GET['action'], $_GET['id'], $_GET['_wpnonce'] )
		&& $_GET['page'] === 'zoho_leads'
		&& $_GET['action'] === 'delete'
	) {
		$lead_id = sanitize_text_field( $_GET['id'] );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to perform this operation.' );
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'zoho_delete_list_' . $lead_id ) ) {
			wp_die( 'Security error: The token is invalid.' );
		}

		$deleted = zoho_delete_lead( $lead_id );

		$redirect_url = admin_url( 'admin.php?page=zoho_leads' );

		if ( $deleted ) {
			$redirect_url = add_query_arg( 'deleted', '1', $redirect_url );
		} else {
			$redirect_url = add_query_arg( 'deleted', '0', $redirect_url );
		}

		wp_redirect( $redirect_url );
		exit;
	}
}

add_action( 'admin_init', 'zoho_handle_delete_lead' );


// CALLS CRUD METHODS
function zoho_calls_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}

	require_once ZOHO_TABLE_PATH . 'CallsListTable.php';
	$calls = new CallsListTable();
	$calls->prepare_items();

	if ( isset( $_GET['deleted'] ) ) {
		if ( $_GET['deleted'] == '1' ) {
			echo '<div class="notice notice-success is-dismissible"><p>Contact successfully deleted.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>An error occurred while deleting the contact.</p></div>';
		}
	}
	include ZOHO_VIEW_PATH . 'call/call.php';
}

function zoho_add_call_html(): void {
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_add_call_action' ) ) {
		$data = [
			'se_module'       => sanitize_text_field( $_POST['se_module'] ),
			'Call_Type'       => sanitize_text_field( $_POST['call_type'] ),
			'Call_Start_Time' => sanitize_text_field( $_POST['call_start_time'] ),
			'Call_Duration'   => sanitize_text_field( $_POST['call_duration'] ),
			'What_Id'         => [
				'id'   => sanitize_text_field( $_POST['leads_id'] ),
			],
			'Subject'         => sanitize_text_field( $_POST['subject'] ),
		];

		$new_call = zoho_add_call( $data );

		if ( $new_call ) {
			echo '<div class="notice notice-success is-dismissible"><p>Contact successfully added.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>Error storing information.</p></div>';
		}
	}
	$leads = zoh_get_contact_name_data();
	include ZOHO_VIEW_PATH . 'call/add-call.php';
}

function zoho_edit_call_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized access' );
	}

	$call_id = $_GET['id'] ?? '';
	if ( empty( $call_id ) ) {
		echo '<div class="wrap"><div class="notice notice-error"><p>Caller ID not specified.</p></div></div>';

		return;
	}

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'zoho_edit_call_action' ) ) {
		$data = [
			'Subject' => sanitize_text_field( $_POST['subject'] ),
		];

		$updated = zoho_update_call( $call_id, $data );

		if ( $updated ) {
			echo '<div class="notice notice-success is-dismissible"><p>The Call was successfully updated.</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>Error storing information.</p></div>';
		}
	}

	$call = zoho_get_call_by_id( $call_id );

	if ( ! $call ) {
		echo '<div class="notice notice-error is-dismissible"><p>Call Not Found.</p></div>';

		return;
	}

	include ZOHO_VIEW_PATH . 'call/edit-call.php';
}

function zoho_handle_delete_call(): void {
	if (
		isset( $_GET['page'], $_GET['action'], $_GET['id'], $_GET['_wpnonce'] )
		&& $_GET['page'] === 'zoho_calls'
		&& $_GET['action'] === 'delete'
	) {
		$call_id = sanitize_text_field( $_GET['id'] );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to perform this operation.' );
		}

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'zoho_delete_call_' . $call_id ) ) {
			wp_die( 'Security error: The token is invalid.' );
		}

		$deleted = zoho_delete_call( $call_id );

		$redirect_url = admin_url( 'admin.php?page=zoho_calls' );

		if ( $deleted ) {
			$redirect_url = add_query_arg( 'deleted', '1', $redirect_url );
		} else {
			$redirect_url = add_query_arg( 'deleted', '0', $redirect_url );
		}

		wp_redirect( $redirect_url );
		exit;
	}
}
add_action( 'admin_init', 'zoho_handle_delete_call' );