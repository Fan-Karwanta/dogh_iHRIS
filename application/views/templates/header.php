<?php
// Use cached system settings to avoid repeated DB queries
if (!isset($GLOBALS['_sys_cache'])) {
    $query = $this->db->query("SELECT * FROM systems WHERE id=1");
    $GLOBALS['_sys_cache'] = $query->row();
}
$sys = $GLOBALS['_sys_cache'];
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8">
<title><?= $title ?> | <?= $sys->system_name ?></title>
<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

<!-- DNS Prefetch for faster external resource loading -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

<!-- Preconnect for critical resources -->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= site_url() ?>favicon_folder/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= site_url() ?>favicon_folder/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= site_url() ?>favicon_folder/favicon-16x16.png">
<link rel="manifest" href="<?= site_url() ?>favicon_folder/site.webmanifest">
<link rel="shortcut icon" href="<?= site_url() ?>favicon_folder/favicon.ico">

<!-- Critical CSS - Load first -->
<link rel="stylesheet" href="<?= site_url() ?>assets/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= site_url() ?>assets/css/atlantis.css">
<link rel="stylesheet" href="<?= site_url() ?>assets/css/custom.css">
<link rel="stylesheet" href="<?= site_url() ?>assets/css/fonts.min.css">

<!-- Fonts - Load async to prevent render blocking -->
<script>
(function() {
    // Check if fonts are already cached
    if (sessionStorage.fonts) {
        document.documentElement.classList.add('fonts-loaded');
        return;
    }
    
    // Load WebFont loader asynchronously
    var wf = document.createElement('script');
    wf.src = '<?= site_url() ?>assets/js/plugin/webfont/webfont.min.js';
    wf.async = true;
    wf.onload = function() {
        WebFont.load({
            google: { "families": ["Lato:300,400,700,900"] },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['<?= site_url() ?>assets/css/fonts.min.css']
            },
            active: function() {
                sessionStorage.fonts = true;
                document.documentElement.classList.add('fonts-loaded');
            }
        });
    };
    document.head.appendChild(wf);
})();
</script>

<!-- Performance optimization styles -->
<style>
    /* Prevent FOUT (Flash of Unstyled Text) */
    html:not(.fonts-loaded) body { opacity: 0.99; }
    html.fonts-loaded body { opacity: 1; transition: opacity 0.1s ease-in; }
    
    /* Smooth loading transition */
    .preloader { transition: opacity 0.3s ease-out; }
    .preloader.fade-out { opacity: 0; pointer-events: none; }
</style>