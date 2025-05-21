<div class="wrap">
	<h1 class="wp-heading-inline">edit call data</h1>
    <form method="post">
		<?php wp_nonce_field( 'zoho_edit_call_action' ); ?>
        <table class="form-table">
            <tr>
                <th><label for="subject">موضوع:</label></th>
                <td><input type="text" id="subject" name="subject" value="<?php echo esc_attr( $call['Subject'] ?? '' ); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="name">نام مخاطب:</label></th>
                <td><input type="text" id="name" name="name" value="<?php echo esc_attr( $call['Who_Id']['name'] ?? '' ); ?>" class="regular-text" disabled/></td>
            </tr>
            <tr>
                <th><label for="related">مرتبط با:</label></th>
                <td><input type="text" id="related" name="related" value="<?php echo esc_attr( $call['What_Id']['name'] ?? '' ); ?>" class="regular-text" disabled/></td>
            </tr>
            <tr>
                <th><label for="type">نوع تماس:</label></th>
                <td><input type="text" id="type" name="type" value="<?php echo esc_attr( $call['Call_Type'] ?? '' ); ?>" class="regular-text" disabled/></td>
            </tr>
            <tr>
                <th><label for="duration">زمان تماس:</label></th>
                <td><input type="text" id="duration" name="duration" value="<?php echo esc_attr( $call['Call_Duration'] ?? '' ); ?>" class="regular-text" disabled/></td>
            </tr>
        </table>
		<?php submit_button( 'ذخیره تغییرات' ); ?>
    </form>
</div>