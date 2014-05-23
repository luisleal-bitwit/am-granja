<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
         <base href="<?php echo base_url() ?>" class="sitebase">
        <title>Importar Animal Listado</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/bootstrap-ui/jquery.ui.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css">
        <link rel="stylesheet" href="assets/css/bootstrap.admin.blue.css">
        <link rel="stylesheet" href="assets/css/bootstrap.custom.css">
        <link rel="stylesheet" href="assets/css/bootstrap.multiselect.css">
        <link rel="stylesheet" href="assets/css/loading.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/jquery.jRating.css">
        <link rel="stylesheet" href="assets/css/icons.css">
        <link rel="stylesheet" href="assets/css/style.admin.css">
        <link rel="stylesheet" href="assets/css/helpers.css">
    </head>
    <body>
        <div id="high-cont">
            <div id="contenido">
                <div class="items">
                    <div class="clearfix"></div>
                    <div class="row-fluid">
                        <h2>Importar Listado de animales</h2>
                        <div class="container" style="margin-top:50px">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-error"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('success') == TRUE): ?>
                                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" action="<?php echo base_url() ?>admin/animales/csv/importcsv" enctype="multipart/form-data">
                                <input type="file" name="userfile" ><br><br>
                                <input type="submit" name="submit" value="Subir CSV" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>