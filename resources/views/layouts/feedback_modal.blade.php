<button class="feedback-button" data-open="feedback-form"><span>Feedback</span></button>
<div class="feedback-modal reveal" id="feedback-form" data-reveal>
	<h4>We would love to hear from you!</h4>
	<p>Since we are still on the Beta Version, you might  encounter some bugs or feel like Vinviter is missing something. We are open to any and all suggestions!</p>
	<form action="{{ route('siteFeedback') }}" method="POST" name="siteFeedback">
		{{ csrf_field() }}
		<div class="row">
			<div class="small-8 columns">
				<label>
					Your name:
					@if(Auth::check())
					<input type="text" id="name" name="name" value="{{ Auth::user()->name }}" disabled>
					@else
					<input type="text" id="name" name="name">
					@endif
				</label>
			</div>
		</div>
		<div class="row">
			<div class="small-8 columns">
				<label>
					Your email address:
					@if(Auth::check())
					<input type="email" id="email" name="email" value="{{ Auth::user()->email }}" disabled>
					@else
					<input type="email" id="email" name="email">
					@endif
				</label>
			</div>
		</div>
		<div class="row">
			<div class="small-8 columns">
				<label>
					Comments:
					<textarea id="comments" name="comments" rows="4"></textarea>
				</label>
			</div>
		</div>
		<div class="row">
			<div class="small-8 columns">
				<input id="submit-site-feedback" type="submit" class="button" value="Submit feedback">
			</div>
		</div>
	</form>
	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>