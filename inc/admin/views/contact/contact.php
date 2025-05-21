<div class="wrap">
    <h1 class="wp-heading-inline">Contacts</h1>
    <a href="<?php echo esc_url( admin_url('admin.php?page=zoho_add_contact') ); ?>" class="page-title-action">Add Contact</a>
    <form method="POST">
	    <?php echo $contacts->display(); ?>
    </form>
</div>