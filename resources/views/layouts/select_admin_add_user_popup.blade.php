@if((isset($addedMode) && $addedMode) || (isset($oldAdmins) && $oldAdmins->contains('id', $followable->id)))
<div class="column user" data-id="{{ $followable->id }}">
	<a target="_blank" href="{{ route('user.profile', $followable->username) }}"><img src="{{ url('images/small59/'.$followable->avatarFullPath) }}"></a>
	<div class="float-left text-left">
		<span class="name">{{ $followable->name }}</span>
		<button class="add-button added" type="button" data-id="{{ $followable->id }}" data-name="{{ $followable->name }}" data-username="{{ $followable->username }}"><span class="fa fa-check-circle"></span> Added</button>
	</div>
</div>
@else
<div class="column user" data-id="{{ $followable->id }}">
	<a target="_blank" href="{{ route('user.profile', $followable->username) }}"><img src="{{ url('images/small59/'.$followable->avatarFullPath) }}"></a>
	<div class="float-left text-left">
		<span class="name">{{ $followable->name }}</span>
		<button class="add-button" type="button" data-id="{{ $followable->id }}" data-name="{{ $followable->name }}" data-username="{{ $followable->username }}"><span class="fa fa-user-plus"></span> Add</button>
	</div>
</div>
@endif