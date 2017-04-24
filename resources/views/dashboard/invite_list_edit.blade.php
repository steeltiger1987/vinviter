@extends('layouts.master')

@section('title', $list->name.' - Edit- Dashboard')

@section('content')
<nav>
	<div class="middle-gray-panel">
		@include('layouts.feedback_modal')
		<div class="row">
			<div class="small-8 small-centered columns">
				<div class="option-box">
					<div class="row small-up-3 dib100">
						<div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.upcomingEvents') }}">My Events</a></div>
						<div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.publishedPages') }}">My Pages</a></div>
						<div class="column"><a class="button dashboard-nav-button dashboard-page-active" href="{{ route('dashboard.inviteLists.show') }}">Invite Lists</a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</nav>
<section class="content dashboard no-padding">
	<div class="row invite-lists">
		<div class="small-12 columns list-container">
			<div class="row">
				<h4><strong>Edit List - {{ $list->name }}</strong></h4>
			</div>
			<div class="row">
				<form action="{{ route('dashboard.inviteLists.update', $list) }}" name="saveInviteList" method="POST">
					{!! csrf_field() !!}
					{{ method_field('PUT') }}
					<div class="small-12 columns">
						<div class="row">
							<div class="small-11 small-centered columns select-invlist-members-block" id="select-invlist-members-wrap">
								<div class="selected-label"><span>{{ (count($list->members) > 0) ? count($list->members) : '0' }}</span> selected</div>
								<div class="row small-up-3 select-invlist-members-user-list" id="selected-list-members">
									@if($list->members)
									<?php $addedMode = true; ?>
									@foreach($list->members as $followable)
									@include('dashboard.select_invite_list_members_user_row')
									<input type="hidden" name="members[]" value="{{ $followable->id }}">
									@endforeach
									<?php $addedMode = false; ?>
									@endif
								</div>

								<div class="row">
									<div class="small-7 columns">
										<input type="text" placeholder="Search users" id="select-list-members-search">
										{!! $errors->first('members', '<span class="form-error is-visible">:message</span>') !!}
									</div>
									<div class="small-2 float-left columns">
										<a class="view-all-users" data-open="invite-list-members-select-following-followers"><span class="fa fa-plus"></span> View all users</a>

										<div class="reveal-following-followers reveal" id="invite-list-members-select-following-followers" data-reveal>
											<div class="reveal-gray-ribbon no-padding">
												<div class="title">
													<ul class="tabs invite-list-members-following-followers-tab" data-tabs id="followers-following-tabs">
														<li class="tabs-title is-active">
															<a href="#following">Following <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowing }}">{{ $user->numberOfFollowing }}</span></a>
														</li>
														<li class="tabs-title">
															<a href="#followers" aria-selected="true">Followers <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowers }}">{{ $user->numberOfFollowers }}</span></a>
														</li>
													</ul>
												</div>
												<button class="close-button invite-list-members-following-followers-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
											</div>

											<div class="tabs-content no-border select-invlist-members-user-list" data-tabs-content="followers-following-tabs">
												<div class="tabs-panel is-active" id="following">
													<div class="row small-up-4" id="invite-list-members-following-container">
														@foreach($user->following as $followable)
														@include('dashboard.select_invite_list_members_user_row')
														@endforeach
													</div>

													@if($user->following->hasMorePages())
													<a class="float-right" id="invite-list-members-following-view-more" data-request-url="{{ route('user.following', $user) }}" data-type="following" data-next-page="{{ $user->following->currentPage()+1 }}" data-last-page="{{ $user->following->lastPage() }}">View more</a>
													@endif
												</div>

												<div class="tabs-panel" id="followers">
													<div class="row small-up-4" id="invite-list-members-followers-container">
														@foreach($user->followers as $followable)
														@include('dashboard.select_invite_list_members_user_row')
														@endforeach
													</div>

													@if($user->followers->hasMorePages())
													<a class="float-right" id="invite-list-members-followers-view-more" data-request-url="{{ route('user.followers', $user) }}" data-type="followers" data-next-page="{{ $user->followers->currentPage()+1 }}" data-last-page="{{ $user->followers->lastPage() }}">View more</a>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row small-up-3 select-invlist-members-user-list search-results">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="small-11 columns small-centered text-right">
								<input type="submit" name="save_invite_list_members" class="button yellow" value="Save List">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection