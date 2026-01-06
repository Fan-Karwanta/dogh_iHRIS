<?php
$current_page = $this->uri->segment(1);
$current_page1 = $this->uri->segment(2);
// Cache user data to avoid repeated queries
if (!isset($GLOBALS['_user_cache'])) {
    $GLOBALS['_user_cache'] = $this->ion_auth->user()->row();
}
$user = $GLOBALS['_user_cache'];
// Use cached system settings to avoid repeated DB queries
if (!isset($GLOBALS['_sys_cache'])) {
    $query = $this->db->query("SELECT * FROM systems WHERE id=1");
    $GLOBALS['_sys_cache'] = $query->row();
}
$sys = $GLOBALS['_sys_cache'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view('templates/header'); ?>
</head>

<body data-background-color="bg3">
	<div id="loading-container" class="preloader">
		<div id="loading-screen">
			<div class="loader loader-lg"></div>
			<span style="margin-left:-20px">Please wait...</span>
		</div>
	</div>
	<div class="wrapper">
		<?php $this->load->view('templates/topbar'); ?>

		<?php $this->load->view('templates/sidebar'); ?>

		<div class="main-panel">

			<div class="container">
				<div class="page-inner">
					<?php if (isset($message) || $this->session->flashdata('message')) : ?>
						<div class="alert alert-<?= $this->session->flashdata('success'); ?>" role="alert">
							<?= isset($message) ? $message : $this->session->flashdata('message') ?>
						</div>
					<?php endif ?>


					<?= $content ?>

				</div>
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<nav class="pull-left">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link text-muted" href="javascript:void(0)">
									2025 &copy; Copyright <strong><?= $sys->system_name ?></strong>. All Rights Reserved
								</a>
							</li>
						</ul>
					</nav>
					<div class="copyright ml-auto">
						2025, by <a href="https://web.facebook.com/p/Davao-Occidental-General-Hospital-100089814696152/?_rdc=1&_rdr#" target="_blank">DOGH</a>
					</div>
				</div>
			</footer>
		</div>
	</div>
	<?php $this->load->view('modal'); ?>
	<?php $this->load->view('templates/footer'); ?>
</body>

</html>