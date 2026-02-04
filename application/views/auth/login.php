<form action="<?= site_url('auth/login') ?>" method="POST">

	<div class="container container-login animated fadeIn">

		<h3 class="text-center">Sign In Here</h3>
		<?php if ($message !== null) : ?>
			<div class="alert alert-danger" role="alert">
				<?= $message ?>
			</div>
		<?php endif ?>
		<div class="login-form">
			<div class="form-group form-floating-label">
				<input id="username" name="identity" type="text" class="form-control input-border-bottom" required>
				<label for="username" class="placeholder">Username</label>
			</div>
			<div class="form-group form-floating-label">
				<input id="password" name="password" type="password" class="form-control input-border-bottom" required>
				<label for="password" class="placeholder">Password</label>
				<div class="show-password">
					<i class="icon-eye"></i>
				</div>
			</div>
			<div class="row form-sub m-0">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" name="remember" id="rememberme" value="1">
					<label class="custom-control-label" for="rememberme">Remember Me</label>
				</div>

				<!-- <a href="forgot_password" class="link float-right">Forgot Password?</a> -->
			</div>
			<div class="form-action mb-3">
				<button type="submit" class="btn btn-primary btn-rounded btn-login">Sign In</button>
			</div>
			<div class="login-account">
				<a href="<?= site_url('/userauth') ?>" class="btn btn-secondary btn-rounded mb-3">
					<i class="fas fa-arrow-left mr-2"></i>Back to User Selection
				</a>
			</div>
			<div class="login-account">
				<span class="msg">Powered by: </span>
				<a href="https://web.facebook.com/p/Davao-Occidental-General-Hospital-100089814696152/?_rdc=1&_rdr#" class="link" target="_blank">DOGH</a>
			</div>
		</div>
	</div>
</form>