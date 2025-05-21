<div class="wrap">
	<h1 class="wp-heading-inline">add new contact</h1>
    <form method="post">
		<?php wp_nonce_field( 'zoho_add_contact_action' ); ?>
        <table class="form-table">
            <tr>
                <th><label for="first_name">نام:</label></th>
                <td><input type="text" id="first_name" name="first_name" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="last_name">نام خانوادگی:</label></th>
                <td><input type="text" id="last_name" name="last_name" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="email">ایمیل:</label></th>
                <td><input type="email" id="email" name="email" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="phone">شماره تماس:</label></th>
                <td><input type="text" id="phone" name="phone" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="department">واحد:</label></th>
                <td><input type="text" id="department" name="department" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="description">توضیحات:</label></th>
                <td><input type="text" id="description" name="description" class="regular-text" /></td>
            </tr>
        </table>
		<?php submit_button( 'ذخیره تغییرات' ); ?>
    </form>
</div>