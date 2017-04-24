<div class="single-comment-reply" id="reply-{{ $reply->id }}" {!! $displayNone or '' !!}>
<a class="user-avatar" href="{{ route('user.profile', $reply->author->username) }}" title="{{ $reply->author->name }}"><img src="{{ url('images/small40/'.$reply->author->avatarFullPath) }}"></a>
    <div class="comment-reply-body-container">
        <a class="username" href="{{ route('user.profile', $reply->author->username) }}" title="{{ $reply->author->name }}">{{ $reply->author->name }}</a>
        <p class="comment-reply-body">{{ $reply->body }}</p>
    </div>
    <span class="posted-at"><i class="fa fa-clock-o"></i> {{ $reply->created_at->diffForHumans() }}</span>
</div>