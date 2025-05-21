<div class="wrap">
    <h1>ویرایش مخاطب</h1>
    <form method="post">
		<?php wp_nonce_field( 'zoho_edit_contact_action' ); ?>
        <table class="form-table">
            <tr>
                <th><label for="first_name">نام:</label></th>
                <td><input type="text" id="first_name" name="first_name" value="<?php echo esc_attr( $contact['First_Name'] ?? '' ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="last_name">نام خانوادگی:</label></th>
                <td><input type="text" id="last_name" name="last_name" value="<?php echo esc_attr( $contact['Last_Name'] ?? '' ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="email">ایمیل:</label></th>
                <td><input type="email" id="email" name="email" value="<?php echo esc_attr( $contact['Email'] ?? '' ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="phone">شماره تماس:</label></th>
                <td><input type="text" id="phone" name="phone" value="<?php echo esc_attr( $contact['Phone'] ?? '' ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="department">واحد:</label></th>
                <td><input type="text" id="department" name="department" value="<?php echo esc_attr( $contact['Department'] ?? '' ); ?>" class="regular-text" /></td>
            </tr>
            <input type="hidden" name="contact_id" value="<?php echo esc_attr( $contact_id ); ?>">
        </table>
		<?php submit_button( 'ذخیره تغییرات' ); ?>
    </form>
</div>