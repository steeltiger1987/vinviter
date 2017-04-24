			<div class="row collapse list-block" data-id="{{ $list->id }}">
				<div class="small-5 columns">
					<div class="input-group">
						<input class="input-group-field invite-list-name-field" type="text" value="{{ $list->name }}" readonly autocomplete="off">
						<span class="input-group-label round-to-k" data-total="{{ $list->totalMembers }}">{{ ($list->totalMembers) ? $list->totalMembers : 0 }}</span>
					</div>
				</div>
				<div class="small-1 small-offset-1 columns">
					<button class="button success save-invite-list-name text-uppercase hide">Save</button>
				</div>
				<div class="small-3 columns actions float-right">
					<div class="row small-up-2">
						<div class="column"><a href="{{ route('dashboard.inviteLists.edit', $list) }}" class="button hollow" target="_blank">Add</a></div>
						<div class="column">
							<ul class="dropdown menu" data-dropdown-menu data-alignment="right" data-disable-hover="true" data-click-open="true">
								<li class="db">
									<button class="button hollow" type="button">Edit</button>
									<ul class="menu">
										<li><a class="change-invite-list-name">Change name</a></li>
										<li><a data-open="{{ 'delete-list-'.$list->id }}">Delete</a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
					<div class="reveal" id="{{ 'delete-list-'.$list->id }}" data-reveal>
						<h4>Delete</h4>
						<p>Are you sure you want to delete this Invite List?</p>
						<p class="float-right delete-invite-list-buttons">
							<button type="button" class="button secondary" data-close>Cancel</button>
							<button class="delete-invite-list-confirm button alert" type="button" data-id="{{ $list->id }}">Delete</button>
						</p>
						<button class="close-button" data-close aria-label="Close modal" type="button">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				</div>
			</div>