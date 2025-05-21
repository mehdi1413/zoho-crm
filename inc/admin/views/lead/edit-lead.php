<div class="wrap">
    <h1>edit lead</h1>
    <form method="post">
		<?php wp_nonce_field( 'zoho_edit_lead_action' ); ?>
        <table class="form-table">
            <tr>
                <th><label for="first_name">نام:</label></th>
                <td><input type="text" id="first_name" name="first_name"
                           value="<?php echo esc_attr( $lead['First_Name'] ?? '' ); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="last_name">نام خانوادگی:</label></th>
                <td><input type="text" id="last_name" name="last_name"
                           value="<?php echo esc_attr( $lead['Last_Name'] ?? '' ); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="salutation">جنسیت:</label></th>
                <td>
                    <select name="salutation" id="salutation">
						<?php foreach ( $salutations as $index => $value ) : ?>
                            <option value="<?php echo esc_html( $value ) ?>" <?php selected( $lead['Salutation'] ?? '', $value ) ?>><?php echo esc_html( $value ) ?></option>
						<?php endforeach; ?>
                    </select>
            </tr>
            <tr>
                <th><label for="company">کمپانی:</label></th>
                <td><input type="text" id="company" name="company"
                           value="<?php echo esc_attr( $lead['Company'] ?? '' ); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="email">ایمیل:</label></th>
                <td><input type="email" id="email" name="email" value="<?php echo esc_attr( $lead['Email'] ?? '' ); ?>"
                           class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="phone">شماره تماس:</label></th>
                <td><input type="text" id="phone" name="phone" value="<?php echo esc_attr( $lead['Phone'] ?? '' ); ?>"
                           class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="lead_source">نوع لید:</label></th>
                <td>
                    <select name="lead_source" id="lead_source">
						<?php foreach ( $lead_sources as $index => $value ) : ?>
                            <option value="<?php echo esc_html( $value ) ?>" <?php selected( $lead['Lead_Source'] ?? '', $value ) ?>><?php echo esc_html( $value ) ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
		<?php submit_button( 'ذخیره تغییرات' ); ?>
    </form>
</div>