<footer {!! (in_array(Request::url(), [route('auth.login'), route('auth.register')])) ? 'class="no-bd-top"' : '' !!}>
	<div class="row">
		<div class="small-12 columns">
			<ul class="menu float-left">
				<li><a href="{{ route('app.pages.privacy') }}" class="no-pd-lft">Privacy Policy</a></li>
				<li><a href="{{ route('app.pages.terms') }}">Terms of Use</a></li>
				<li><a href="{{ route('app.pages.contact') }}">Contact us</a></li>
			</ul>
			<span class="float-right">&copy; {{ date('Y') }} Vinviter. All rights reserved</span>
		</div>
	</div>
</footer>
