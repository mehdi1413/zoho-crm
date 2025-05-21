<div class="wrap">
    <h1>Add New Call</h1>
    <form method="post">
		<?php wp_nonce_field( 'zoho_add_call_action' ); ?>
        <table class="form-table">
            <tr>
                <th><label for="leads_id">Leads:</label></th>
                <td>
                    <select name="leads_id" id="leads_id">
						<?php foreach ( $leads as $lead ) : ?>
                            <option value="<?php echo $lead['id'] ?>"><?php echo $lead['Full_Name'] ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <input type="hidden" name="se_module" value="Leads">

            <tr>
                <th><label for="call_type">Call Type:</label></th>
                <td>
                    <select name="call_type" id="call_type">
                        <option value="Outbound">Outbound</option>
                        <option value="Inbound">Inbound</option>
                        <option value="Missed">Missed</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="call_start_time">Call Start Time:</label></th>
                <td>
                    <input type="date" id="call_date" name="call_date" class="regular-text"/>
                    <input type="time" id="call_time" name="call_time" class="regular-text"/>
                </td>
                <input type="hidden" name="call_start_time" id="call_start_time">
            </tr>
            <tr>
                <th><label for="call_duration">Call Duration:</label></th>
                <td><input type="time" id="call_duration" name="call_duration" class="regular-text"/></td>
            </tr>
            <tr>
                <th><label for="subject">Subject:</label></th>
                <td><input type="text" id="subject" name="subject" class="regular-text"/></td>
            </tr>
        </table>
		<?php submit_button( 'ذخیره تغییرات' ); ?>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('call_date');
        const timeInput = document.getElementById('call_time');
        const hiddenInput = document.getElementById('call_start_time');
        function updateCallStartTime() {
            const date = dateInput.value;
            const time = timeInput.value;

            if (date && time) {
                const [hours, minutes] = time.split(':');
                const fullTime = `${hours}:${minutes}:00`;
                const localDateTime = new Date(`${date}T${fullTime}`);
                const tzOffsetMinutes = localDateTime.getTimezoneOffset();
                const offsetHours = String(Math.floor(Math.abs(tzOffsetMinutes) / 60)).padStart(2, '0');
                const offsetMinutes = String(Math.abs(tzOffsetMinutes) % 60).padStart(2, '0');
                const offsetSign = tzOffsetMinutes <= 0 ? '+' : '-';
                const timezone = `${offsetSign}${offsetHours}:${offsetMinutes}`;
                const isoDate = `${date}T${fullTime}${timezone}`;
                hiddenInput.value = isoDate;
            }
        }
        dateInput.addEventListener('change', updateCallStartTime);
        timeInput.addEventListener('change', updateCallStartTime);
    });
</script>