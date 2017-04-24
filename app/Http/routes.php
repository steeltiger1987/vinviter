<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    // Authentication Routes...
    Route::get('users/login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@showLoginForm']);
    Route::post('users/login', ['as' => 'auth.login', 'uses' => 'Auth\AuthController@login']);
    Route::get('users/logout', ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@logout']);

    // Registration Routes...
    Route::get('users/signup', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@showRegistrationForm']);
    Route::post('users/signup', ['as' => 'auth.register', 'uses' => 'Auth\AuthController@register']);

    // Password Reset Routes...
    Route::get('users/password/reset/{token?}', ['as' => 'auth.resetForm', 'uses' => 'Auth\PasswordController@showResetForm']);
    Route::post('users/password/email', ['as' => 'auth.resetEmail', 'uses' => 'Auth\PasswordController@sendResetLinkEmail']);
    Route::post('users/password/reset', ['as' => 'auth.reset', 'uses' => 'Auth\PasswordController@reset']);

    // Profile Routes..
    Route::get('users/{user}', ['as' => 'user.profile', 'uses' => 'UserController@showProfile']);
    Route::get('/users/{user}/events/attending', ['as' => 'user.events.attending', 'uses' => 'UserController@getAttendingEvents']);
    Route::get('/users/{user}/events/attended', ['as' => 'user.events.attended', 'uses' => 'UserController@getAttendedEvents']);
    Route::get('/users/{user}/following', ['as' => 'user.following', 'uses' => 'UserController@getFollowing']);
    Route::get('/users/{user}/followers', ['as' => 'user.followers', 'uses' => 'UserController@getFollowers']);
    Route::get('/users/{user}/followingPages', ['as' => 'user.followingPages', 'uses' => 'UserController@getFollowingPages']);

    Route::post('users/{user}/follow', ['as' => 'user.profile.follow', 'uses' => 'UserController@follow']);
    Route::post('users/{user}/unfollow', ['as' => 'user.profile.unfollow', 'uses' => 'UserController@unfollow']);
    Route::post('users/{user}/status', ['as' => 'user.profile.status', 'uses' => 'UserController@updateStatus']);
    Route::post('users/{user}/profileImage', ['as' => 'user.profile.profileImage', 'uses' => 'UserController@ajaxProfileImage']);
    Route::post('users/{user}/backgroundImage', ['as' => 'user.profile.backgroundImage', 'uses' => 'UserController@ajaxBackgroundImage']);
    // Email verification
    Route::get('users/signup/verify/{token}', ['as' => 'auth.verify', 'uses' => 'Auth\AuthController@confirmEmail']);

    Route::post('users/{user}/notifications/pages/{page}/', ['as' => 'user.postPageNotifications', 'uses' => 'UserController@postPageNotifications']);
    // Route::post('users/{user}/block/pages/{page}/', ['as' => 'user.postBlockPage', 'uses' => 'UserController@postBlockPage']);
    Route::get('/users/{user}/favorites/', ['as' => 'user.favorites', 'uses' => 'UserController@getFavorites']);
    Route::post('/users/{user}/favorites/{favorite}/likes', ['as' => 'user.favoriteLike', 'uses' => 'UserController@postFavoriteLike']);


    // User settings
    Route::get('settings/account', ['as' => 'settings.account', 'uses' => 'UserController@settingsView']);
    Route::get('settings/profile', ['as' => 'settings.profile', 'uses' => 'UserController@settingsView']);
    // Route::get('settings/preferences', ['as' => 'settings.preferences', 'uses' => 'UserController@settingsView']);
    Route::get('settings/deactivate', ['as' => 'settings.deleteAccount', 'uses' => 'UserController@settingsView']);
    Route::get('settings', function(){
        return redirect()->route('settings.account');
    });

    
    // Report
    Route::group(['prefix' => 'report', 'as' => 'report.'], function(){
        Route::post('/pages/{page}', ['as' => 'page', 'uses' => 'HomeController@postReportPage']);
        Route::post('/users/{user}', ['as' => 'user', 'uses' => 'HomeController@postReportUser']);
    });

    // Notifications
    Route::get('notifications', ['as' => 'user.notifications', 'uses' => 'UserController@getNotifications']);
    Route::post('notifications/seen', ['as' => 'user.notifications.seen', 'uses' => 'UserController@notificationsSeen']);
    Route::post('notifications/clear', ['as' => 'user.notifications.clear', 'uses' => 'UserController@clearNotifications']);

    Route::post('settings/account', ['as' => 'settings.postAccount', 'uses' => 'UserController@updateAccount']);
    Route::post('settings/profile', ['as' => 'settings.postProfile', 'uses' => 'UserController@updateProfile']);
    Route::post('settings/preferences', ['as' => 'settings.postPreferences', 'uses' => 'UserController@updatePreferences']);
    Route::delete('settings/deactivate', ['as' => 'settings.postDeleteAccount', 'uses' => 'UserController@delete']);

    

    // Dynamic images on the go
    Route::get('images/{size}/{entity}/{id}/{subgroup}/{filename}', ['as' => 'dynamicImages', 'uses' => 'HomeController@serveImages']);

    

    // Static pages
    Route::get('static/privacy', ['as' => 'app.pages.privacy', 'uses' => 'HomeController@getPrivacyPage']);
    Route::get('static/terms', ['as' => 'app.pages.terms', 'uses' => 'HomeController@getTermsPage']);
    Route::get('static/contact', ['as' => 'app.pages.contact', 'uses' => 'HomeController@getContactPage']);
    Route::post('static/contact', ['as' => 'app.pages.postContact', 'uses' => 'HomeController@postContactPage']);

    

    // Initial create page
    Route::get('create', ['as' => 'app.create', 'uses' => 'HomeController@create']);

    

    // RESTful Route for Events
    Route::get('/events/{id}/preview', ['as' => 'events.preview', 'uses' => 'EventController@preview']);
    Route::post('/events/{id}/preview', ['as' => 'events.publish', 'uses' => 'EventController@publish']);
    Route::post('/events/create/ajax-upload-image', ['as' => 'events.create.ajaxUploadImage', 'uses' => 'EventController@ajaxUploadImage']);
    Route::delete('/events/create/ajax-delete-image', ['as' => 'events.create.ajaxDeleteImage', 'uses' => 'EventController@ajaxDeleteImage']);
    Route::post('/events/{id}/attendees', ['as' => 'events.attendees', 'uses' => 'EventController@storeAttendee']);
    Route::get('/events/{id}/attendees', ['as' => 'events.attendees', 'uses' => 'EventController@getAttendees']);
    Route::delete('/events/{id}/attendees', ['as' => 'events.attendees', 'uses' => 'EventController@deleteAttendee']);

    Route::post('/events/{id}/ajax-upload-photo', ['as' => 'events.uploadHistoryPhoto', 'uses' => 'EventController@ajaxUploadHistoryPhoto']);
    Route::delete('/events/{id}/ajax-delete-photo', ['as' => 'events.deleteHistoryPhoto', 'uses' => 'EventController@ajaxDeleteHistoryPhoto']);
    Route::post('/events/{id}/publish-photos', ['as' => 'events.publishHistoryPhotos', 'uses' => 'EventController@publishHistoryPhotos']);
    Route::post('/events/{id}/history-photos/{historyPhoto}/likes', ['as' => 'events.historyPhotoLike', 'uses' => 'EventController@likeHistoryPhoto']);
    Route::get('/events/{event}/history-photos/', ['as' => 'events.historyPhotos', 'uses' => 'EventController@getHistoryPhotos']);
    
    Route::delete('/events/{event}/admins/{id}', ['as' => 'events.deleteAdmin', 'uses' => 'EventController@deleteAdmin']);

    // Comments
    Route::post('/events/{event}/comments', ['as' => 'events.postComment', 'uses' => 'EventController@storeComment']);
    Route::post('/events/{event}/comments/{comment}/likes', ['as' => 'events.commentLike', 'uses' => 'EventController@likeComment']);

    Route::post('/events/{event}/invite-a-list', ['as' => 'events.inviteAList', 'uses' => 'EventController@inviteAList']);
    Route::post('/events/{event}/invite-a-user', ['as' => 'events.inviteAUser', 'uses' => 'EventController@inviteAUser']);
    Route::get('/events/{event}/invite-lists', ['as' => 'events.inviteLists', 'uses' => 'EventController@getInviteListsAjax']);
    Route::get('/events/{event}/invite-users-search/{search}', ['as' => 'events.inviteUsersSearch', 'uses' => 'EventController@searchInviteUsersAjax']);

    Route::resource('events', 'EventController');

    

    // RESTful Route for Pages
    Route::get('/pages/{slug}/preview', ['as' => 'pages.preview', 'uses' => 'PageController@preview']);
    Route::post('/pages/{slug}/preview', ['as' => 'pages.publish', 'uses' => 'PageController@publish']);
    Route::post('/pages/{page}/follow', ['as' => 'pages.follow', 'uses' => 'PageController@follow']);
    Route::post('/pages/{page}/unfollow', ['as' => 'pages.unfollow', 'uses' => 'PageController@unfollow']);
    Route::get('/pages/{page}/followers', ['as' => 'pages.getFollowers', 'uses' => 'PageController@getFollowers']);
    Route::post('/pages/create/ajax-upload-image', ['as' => 'pages.create.ajaxUploadImage', 'uses' => 'PageController@ajaxUploadImage']);
    Route::delete('/pages/create/ajax-delete-image', ['as' => 'pages.create.ajaxDeleteImage', 'uses' => 'PageController@ajaxDeleteImage']);

    Route::get('/pages/{page}/events/upcoming', ['as' => 'pages.events.upcoming', 'uses' => 'PageController@getUpcomingEvents']);
    Route::get('/pages/{page}/events/history', ['as' => 'pages.events.history', 'uses' => 'PageController@getHistoryEvents']);

    Route::delete('/pages/{page}/admins/{id}', ['as' => 'pages.deleteAdmin', 'uses' => 'PageController@deleteAdmin']);

    Route::post('/pages/{page}/invite-a-list', ['as' => 'pages.inviteAList', 'uses' => 'PageController@inviteAList']);
    Route::post('/pages/{page}/invite-a-user', ['as' => 'pages.inviteAUser', 'uses' => 'PageController@inviteAUser']);
    Route::get('/pages/{page}/invite-lists', ['as' => 'pages.inviteLists', 'uses' => 'PageController@getInviteListsAjax']);
    Route::get('/pages/{page}/invite-users-search/{search}', ['as' => 'pages.inviteUsersSearch', 'uses' => 'PageController@searchInviteUsersAjax']);
    Route::resource('pages', 'PageController');

    

    Route::get('/', ['as' => 'landingPage', 'uses' => 'HomeController@getLandingPage']);

    Route::get('/upcoming', ['as' => 'upcoming', 'uses' => 'EventController@index']);
    Route::post('/filter', ['as' => 'upcomingFilter', 'uses' => 'EventController@buildEventsURL']);
    
    Route::get('/history', ['as' => 'history', 'uses' => 'EventController@index']);
    Route::post('/history/filter', ['as' => 'historyFilter', 'uses' => 'EventController@buildEventsURL']);
    
    Route::get('/search/{term}', ['as' => 'mainSearch', 'uses' => 'HomeController@appSearch']);

    

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function(){
        Route::get('/published-pages', ['as' => 'publishedPages', 'uses' => 'DashboardController@getPublishedPages']);
        Route::get('/published-pages-admin', ['as' => 'publishedPages.typeAdmin', 'uses' => 'DashboardController@getPublishedPagesTypeAdmin']);
        Route::get('/saved-pages', ['as' => 'savedPages', 'uses' => 'DashboardController@getSavedPages']);
        Route::get('/', ['as' => 'upcomingEvents', 'uses' => 'DashboardController@getUpcomingEvents']);
        Route::get('/upcoming-admin', ['as' => 'upcomingEvents.typeAdmin', 'uses' => 'DashboardController@getUpcomingEventsTypeAdmin']);
        Route::get('/history-events', ['as' => 'historyEvents', 'uses' => 'DashboardController@getHistoryEvents']);
        Route::get('/history-admin-events', ['as' => 'historyEvents.typeAdmin', 'uses' => 'DashboardController@getHistoryEventsTypeAdmin']);
        Route::get('/saved-events', ['as' => 'savedEvents', 'uses' => 'DashboardController@getSavedEvents']);

        Route::group(['prefix' => 'invite-lists', 'as' => 'inviteLists.'], function(){
            Route::get('/', ['as' => 'show', 'uses' => 'InviteListController@index']);
            Route::get('/{list}/edit', ['as' => 'edit', 'uses' => 'InviteListController@edit']);
            Route::post('/', ['as' => 'store', 'uses' => 'InviteListController@store']);
            Route::delete('{id}/delete', ['as' => 'delete', 'uses' => 'InviteListController@delete']);
            Route::put('{id}', ['as' => 'update', 'uses' => 'InviteListController@update']);
        });
    });

Route::post('/feedback', ['as' => 'siteFeedback', 'uses' => 'HomeController@postFeedback']);

Route::get('/confirm',function(){View::make("emails.confirm");});
Route::group(['middleware' => 'api', 'prefix' => 'api'], function(){
    Route::get('/users/search/{name}', ['as' => 'api.users.search', 'uses' => 'api\v1\UserController@search']);
    Route::get('/pages/search/{name}', ['as' => 'api.pages.search', 'uses' => 'api\v1\PageController@search']);

    Route::get('/countryRegions/{id}', ['as' => 'api.getCountryRegions', 'uses' => 'api\v1\CommonController@getCountryRegions']);
    Route::get('/regionCities/{id}', ['as' => 'api.getRegionCities', 'uses' => 'api\v1\CommonController@getRegionCities']);
});

});