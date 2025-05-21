<div class="wrap">
    <h1 class="wp-heading-inline">Leads List</h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=zoho_add_lead' ) ); ?>" class="page-title-action">Add Lead</a>
    <form method="POST">
		<?php echo $leads->display(); ?>
    </form>
</div>