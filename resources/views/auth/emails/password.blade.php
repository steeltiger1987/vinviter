<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Reset Password</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/email.css') }}">
</head>
<body class="reset-password">
	<div class="row">
		<div class="small-3 columns small-centered">
			<div class="text-center logo"><img src="{{ asset('images/logo-large.png') }}" alt=""></div>
		</div>
	</div>
	<div class="row">
		<div class="small-8 columns small-centered">
			<div class="box">
				<h4>Reset Password</h4>
				<h6>Please click the link below to create a new password.</h6>
				<p class="action-button">
					<a class="button large" href="{{ $link = route('auth.resetForm', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}">Reset password</a>
				</p>
			</div>
			<small class="copyright">{{ date('Y') }} &copy; Vinviter</small>
		</div>
	</div>
</body>
</html>
