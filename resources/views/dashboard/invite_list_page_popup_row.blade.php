<div class="row">
	<div class="small-6 small-offset-1 columns">
		<div class="input-group">
			<input class="input-group-field invite-list-name-field" type="text" value="{{ $list->name }}" readonly autocomplete="off">
			<span class="input-group-label round-to-k" data-total="{{ $list->totalMembers }}">{{ ($list->totalMembers) ? $list->totalMembers : 0 }}</span>
		</div>
	</div>
	<div class="small-4 columns float-right">
		@if($page->inviteLists->contains('id', $list->id))
		<button class="button yellow add-list-to-page-invitations" data-list-id="{{ $list->id }}" data-is-invited="1">Invited</button>
		@else
		<button class="button hollow add-list-to-page-invitations" data-list-id="{{ $list->id }}" data-is-invited="0">Invite</button>
		@endif
	</div>
</div>