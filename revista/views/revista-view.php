<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title()); ?></h1>

    <span class="form-desc">
        <p>Selecione o arquivo CSV para importar os dados.</p>
    </span>
    <form action="<?php echo admin_url( 'admin.php?page=revista' ) ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" id="file">
        <input type="submit" value="Submit" name="submit">
    </form>
</div>