<div class="row">
    <div class="small-8 columns">
        <div id="comments-container" data-event-id="{{ $event->id }}">
            @foreach($event->comments as $comment)
            <div class="single-comment">
                <a class="user-avatar" href="{{ route('user.profile', $comment->author->username) }}" title="{{ $comment->author->name }}"><img src="{{ url('images/small48/'.$comment->author->avatarFullPath) }}"></a>
                <div class="comment-body-container">
                    <a class="username" href="{{ route('user.profile', $comment->author->username) }}" title="{{ $comment->author->name }}">{{ $comment->author->name }}</a>
                    <p class="comment-body">{{ $comment->body }}</p>
                    <span class="comment-likes">{{ $comment->numberOfLikes }}</span>
                    @if(Auth::check())
                    @if(Auth::user()->doesLikeTheComment($comment))
                    <button type="button" class="like-button active" data-comment-id="{{ $comment->id }}" data-is-liked="1">Liked</button>
                    @else
                    <button type="button" class="like-button" data-comment-id="{{ $comment->id }}" data-is-liked="0">Like</button>
                    @endif
                    <button type="button" class="reply-button" data-comment-id="{{ $comment->id }}">Reply {{ $comment->numberOfReplies }}</button>
                    @endif
                </div>
                <span class="posted-at"><i class="fa fa-clock-o"></i> {{ $comment->created_at->diffForHumans() }}</span>
                @if(Auth::check())
                <div class="replies" id="comment-replies-{{ $comment->id }}">
                    <div>
                        @foreach($comment->replies as $reply)
                        @include('events.single_comment_reply_template')
                        @endforeach
                    </div>
                    <form name="eventPostComment" method="POST" data-post-url="{{ Request::url().'/comments' }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea placeholder="Write your reply here" rows="3" name="body"></textarea>
                        <button class="button">Send</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if(Auth::check())
        <form id="event_{{ $event->id }}" name="eventPostComment" method="POST" class="main-comment-form" data-post-url="{{ Request::url().'/comments' }}">
            <input type="hidden" name="parent_id">
            <textarea placeholder="Write your comment here" rows="3" name="body"></textarea>
            <button class="button">Send</button>
        </form>
        @endif
    </div>
</div>