<?php

defined( 'ABSPATH' ) || exit;

const ZOHO_CLIENT_ID     = 'YOUR_CLIENT_ID';
const ZOHO_CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
const ZOHO_REFRESH_TOKEN = 'YOUR_REFRESH_TOKEN';
const ZOHO_API_DOMAIN    = 'https://www.zohoapis.com';

function zoho_get_access_token() {
	$access_token = get_option( 'zoho_access_token' );

	if ( $access_token ) {
		// اعتبارسنجی توکن با یک درخواست تست
		$test = wp_remote_get( ZOHO_API_DOMAIN . '/crm/v2/Users', [
			'headers' => [
				'Authorization' => 'Zoho-oauthtoken ' . $access_token
			]
		] );

		$test_body = json_decode( wp_remote_retrieve_body( $test ), true );
		if ( ! isset( $test_body['code'] ) || $test_body['code'] !== 'INVALID_TOKEN' ) {
			return $access_token;
		}
	}

	$response = wp_remote_post( 'https://accounts.zoho.com/oauth/v2/token', [
		'body' => [
			'refresh_token' => ZOHO_REFRESH_TOKEN,
			'client_id'     => ZOHO_CLIENT_ID,
			'client_secret' => ZOHO_CLIENT_SECRET,
			'grant_type'    => 'refresh_token',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $body['access_token'] ) ) {
		update_option( 'zoho_access_token', $body['access_token'] );

		return $body['access_token'];
	}

	return false;
}

// Contacts API Handler Methods.
function zoho_get_contacts() {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url      = ZOHO_API_DOMAIN . '/crm/v2/Contacts';
	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token
		]
	] );

	if ( is_wp_error( $response ) ) {
		return 'Connection error: ' . $response->get_error_message();
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( isset( $data['data'] ) ) {
		return $data['data'];
	}

	return 'No data received or error: ' . $body;
}

function zoho_get_contact_by_id( $contact_id ) {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}
	$url = ZOHO_API_DOMAIN . "/crm/v2/Contacts/{$contact_id}";

	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	return $data['data'][0] ?? false;
}

function zoho_add_contact( $data ): bool|string {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url = ZOHO_API_DOMAIN . '/crm/v2/Contacts';

	$payload = [
		'data' => [
			[
				'Last_Name'   => $data['Last_Name'],
				'First_Name'  => $data['First_Name'] ?? '',
				'Email'       => $data['Email'] ?? '',
				'Phone'       => $data['Phone'] ?? '',
				'Department'  => $data['Department'] ?? '',
				'Description' => $data['Description'] ?? '',
			]
		]
	];

	$response = wp_remote_post( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_delete_contact( $contact_id ): bool {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return false;
	}

	$url = ZOHO_API_DOMAIN . "/crm/v2/Contacts/{$contact_id}";

	$response = wp_remote_request( $url, [
		'method'  => 'DELETE',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_update_contact( $contact_id, $data ): bool {
	$access_token = zoho_get_access_token();
	$url          = ZOHO_API_DOMAIN . '/crm/v2/Contacts';

	$payload = [
		'data' => [
			[
				'id'         => $contact_id,
				'First_Name' => $data['First_Name'],
				'Last_Name'  => $data['Last_Name'],
				'Email'      => $data['Email'],
				'Phone'      => $data['Phone'],
				'Department' => $data['Department'],
			],
		]
	];

	$response = wp_remote_request( $url, [
		'method'  => 'PUT',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

// Leads API Handler Methods.
function zoho_get_leads() {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url      = ZOHO_API_DOMAIN . '/crm/v2/Leads';
	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token
		]
	] );

	if ( is_wp_error( $response ) ) {
		return 'Connection error: ' . $response->get_error_message();
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( isset( $data['data'] ) ) {
		return $data['data'];
	}

	return [];
}

function zoho_get_lead_by_id( $lead_id ) {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}
	$url = ZOHO_API_DOMAIN . "/crm/v2/Leads/{$lead_id}";

	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	return $data['data'][0] ?? false;
}

function zoho_add_lead( $data ): bool|string {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url = ZOHO_API_DOMAIN . '/crm/v2/Leads';

	$payload = [
		'data' => [
			[
				'Company'     => $data['Company'],
				'Last_Name'   => $data['Last_Name'],
				'First_Name'  => $data['First_Name'] ?? '',
				'Email'       => $data['Email'] ?? '',
				'Phone'       => $data['Phone'] ?? '',
				'Lead_Source' => $data['Lead_Source'] ?? '',
				'Salutation'  => $data['Salutation'] ?? '',
			]
		]
	];

	$response = wp_remote_post( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_update_lead( $lead_id, $data ): bool {
	$access_token = zoho_get_access_token();
	$url          = ZOHO_API_DOMAIN . '/crm/v2/Leads';

	$payload = [
		'data' => [
			[
				'id'          => $lead_id,
				'First_Name'  => $data['First_Name'],
				'Last_Name'   => $data['Last_Name'],
				'Company'     => $data['Company'],
				'Email'       => $data['Email'],
				'Phone'       => $data['Phone'],
				'Lead_Source' => $data['Lead_Source'],
				'Salutation'  => $data['Salutation'] ?? '',

			],
		]
	];

	$response = wp_remote_request( $url, [
		'method'  => 'PUT',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_delete_lead( $lead_id ): bool {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return false;
	}

	$url = ZOHO_API_DOMAIN . "/crm/v2/Leads/{$lead_id}";

	$response = wp_remote_request( $url, [
		'method'  => 'DELETE',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

// Calls API Handler Methods.
function zoho_get_calls() {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url = ZOHO_API_DOMAIN . '/crm/v2/Calls';

	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token
		]
	] );

	if ( is_wp_error( $response ) ) {
		return 'Connection error: ' . $response->get_error_message();
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( isset( $data['data'] ) ) {
		return $data['data'];
	}

	return 'No data received or error: ' . $body;
}

function zoho_get_call_by_id( $call_id ) {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}
	$url = ZOHO_API_DOMAIN . "/crm/v2/Calls/{$call_id}";

	$response = wp_remote_get( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	return $data['data'][0] ?? false;
}

function zoho_add_call( $data ): bool|string {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url = ZOHO_API_DOMAIN . '/crm/v2/Calls';

	$payload = [
		'data' => [
			[
				'se_module'       => $data['se_module'],
				'Call_Type'       => $data['Call_Type'],
				'Call_Start_Time' => $data['Call_Start_Time'],
				'Call_Duration'   => $data['Call_Duration'] ?? '00:00',
				'What_Id'         => $data['What_Id'] ?? '',
				'Subject'         => $data['Subject'] ?? '',
			]
		]
	];

	$response = wp_remote_post( $url, [
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_update_call( $call_id, $data ): bool {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return 'Failure to receive access token';
	}

	$url = ZOHO_API_DOMAIN . '/crm/v2/Calls';

	$payload = [
		'data' => [
			[
				'id'      => $call_id,
				'Subject' => $data['Subject'],
			],
		]
	];

	$response = wp_remote_request( $url, [
		'method'  => 'PUT',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
		'body'    => wp_json_encode( $payload ),
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoho_delete_call( $call_id ): bool {
	$access_token = zoho_get_access_token();
	if ( ! $access_token ) {
		return false;
	}

	$url = ZOHO_API_DOMAIN . "/crm/v2/Calls/{$call_id}";

	$response = wp_remote_request( $url, [
		'method'  => 'DELETE',
		'headers' => [
			'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			'Content-Type'  => 'application/json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return isset( $body['data'][0]['status'] ) && $body['data'][0]['status'] === 'success';
}

function zoh_get_salutations(): array {
	$leads = zoho_get_leads();

	return array_unique( array_column( $leads, 'Salutation' ) );
}

function zoh_get_contact_name_data(): array {
	$leads = zoho_get_leads();

	$result = [];

	foreach ( $leads as $lead ) {
		if ( ! empty( $lead['Full_Name'] ) && ! empty( $lead['id'] ) ) {
			$result[] = [
				'id'        => $lead['id'],
				'Full_Name' => $lead['Full_Name'],
			];
		}
	}

	return array_unique( $result, SORT_REGULAR );
}

function zoho_get_lead_sources(): array {
	$leads = zoho_get_leads();

	return array_unique( array_column( $leads, 'Lead_Source' ) );
}