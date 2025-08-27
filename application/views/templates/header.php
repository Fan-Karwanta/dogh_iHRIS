<?php
$query = $this->db->query("SELECT * FROM systems WHERE id=1");
$sys = $query->row();
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?= $title ?> | <?= $sys->system_name ?></title>
<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= site_url() ?>favicon_folder/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= site_url() ?>favicon_folder/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= site_url() ?>favicon_folder/favicon-16x16.png">
<link rel="manifest" href="<?= site_url() ?>favicon_folder/site.webmanifest">
<link rel="shortcut icon" href="<?= site_url() ?>favicon_folder/favicon.ico">

<!-- Fonts and icons -->
<script src="<?= site_url() ?>assets/js/plugin/webfont/webfont.min.js"></script>
<script>
    WebFont.load({
        google: {
            "families": ["Lato:300,400,700,900"]
        },
        custom: {
            "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
            urls: ['<?= site_url() ?>assets/css/fonts.min.css']
        },
        active: function() {
            sessionStorage.fonts = true;
        }
    });
</script>

<!-- CSS Files -->
<link rel="stylesheet" href="<?= site_url() ?>assets/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= site_url() ?>assets/css/atlantis.css">
<link rel="stylesheet" href="<?= site_url() ?>assets/css/custom.css">