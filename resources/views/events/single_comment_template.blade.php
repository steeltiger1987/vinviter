<script type="text/javascript">
    var totalEventComments = {{ $totalComments }}
</script>
<div class="single-comment" style="display:none;">
    <a class="user-avatar" href="{{ route('user.profile', $comment->author->username) }}" title="{{ $comment->author->name }}"><img src="{{ url('images/small48/'.$comment->author->avatarFullPath) }}"></a>
    <div class="comment-body-container">
        <a class="username" href="{{ route('user.profile', $comment->author->username) }}" title="{{ $comment->author->name }}">{{ $comment->author->name }}</a>
        <p class="comment-body">{{ $comment->body }}</p>
        <span class="comment-likes">{{ $comment->numberOfLikes }}</span>
        <button type="button" class="like-button" data-comment-id="{{ $comment->id }}" data-is-liked="0">Like</button>
        <button type="button" class="reply-button" data-comment-id="{{ $comment->id }}">Reply {{ $comment->numberOfReplies }}</button>
    </div>
    <span class="posted-at"><i class="fa fa-clock-o"></i> {{ $comment->created_at->diffForHumans() }}</span>
    <div class="replies" id="comment-replies-{{ $comment->id }}">
        <div></div>
        <form id="event_{{ $event_id }}" name="eventPostComment" method="POST" data-post-url="{{ Request::url() }}">
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <textarea placeholder="Write your comment here" rows="3" name="body"></textarea>
            <button class="button">Send</button>
        </form>
    </div>
</div>