<div class="wrap">
	<h1 class="wp-heading-inline">calls table</h1>
    <a href="<?php echo esc_url( admin_url('admin.php?page=zoho_add_call') ); ?>" class="page-title-action">Add Call</a>
    <form method="POST">
	    <?php echo $calls->display(); ?>
    </form>
</div>