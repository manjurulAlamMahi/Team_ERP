<?php

use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ClientMessageController;
use App\Http\Controllers\Admin\ClientMessageTypeController;
use App\Http\Controllers\Admin\CommunityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('frontend.welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::prefix('dashboard')->middleware(['auth'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/inbox', 'inbox')->name('dashboard.inbox');

        Route::post('/addQuickAccess', 'addQuickAccess')->name('add.quick.access');
        Route::get('/removeQuickAccess/{route}', 'removeQuickAccess')->name('remove.quick.access');
    });

    Route::controller(ChatController::class)->group(function () {
        Route::post('/chat/send', 'sendMessage');
        Route::post('/chat/get', 'fetchMessages');
        Route::post('/chat/delete', 'destroyMessages');

        Route::get('/get/conversation/users', 'getUsers');
    });


    Route::controller(EventController::class)->group(function () {
        Route::get('/events/get', 'get')->name('events.get');
        Route::get('/events', 'index')->name('events.index');
        Route::post('/events/store', 'store')->name('events.store');
        Route::post('/events/update', 'update')->name('events.update');
        Route::post('/events/delete', 'destroy')->name('events.destroy');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::post('/mark-notification-read', 'markAsRead')->name('markAsRead');
        Route::get('/clear-all-notifications', 'clearAllNotifications')->name('clearAllNotifications');
        Route::get('/mark-all-notifications-read', 'markAllNotificationsRead')->name('markAllNotificationsRead');
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('/admin-setting', 'admin')->name('setting.admin');
        Route::post('/admin-setting-update', 'admin_update')->name('setting.admin.update');
        Route::get('/mail-setting', 'mail')->name('setting.mail');
        Route::post('/mail-setting-update', 'mail_update')->name('setting.mail.update');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/create-user', 'create')->name('user.create');
        Route::post('/store-user', 'store')->name('user.store');
        Route::post('/update-user', 'update')->name('user.update');
        Route::get('/user-list', 'list')->name('user.list');
        Route::get('/user-request', 'request')->name('user.request');
        Route::get('/user-status', 'status')->name('user.status');
        Route::get('/user-accept/{username}', 'accept')->name('user.accept');
        Route::get('/user-profile/{username}', 'profile')->name('user.profile');
        Route::get('/user-edit/{username}', 'edit')->name('user.edit');
        Route::post('/user-delete', 'destroy')->name('user.destroy');
        Route::post('/user-redirect_assignrole', 'redirect_assignrole')->name('user.redirect.assignrole');
        Route::get('/user-assignrole/{id}', 'assignrole')->name('user.assignrole');
        Route::post('/user-message-store', 'message_store')->name('user.message.store');

    });

    Route::controller(LeaderController::class)->group(function () {
        Route::get('/leader/team-stats', 'teamStats')->name('leader.team.stats');
        Route::get('/leader/my-team', 'myTeam')->name('leader.my.team');
        Route::get('/leader/add-member', 'createMember')->name('leader.add.member');
        Route::post('/leader/store-member', 'storeMember')->name('leader.store.member');
        Route::post('/leader/member-status', 'updateMemberStatus')->name('leader.member.status');
        Route::post('/leader/member-info', 'updateMemberInfo')->name('leader.member.info');
        Route::post('/leader/member-role', 'updateMemberRole')->name('leader.member.role');
        Route::post('/leader/member-password', 'updateMemberPassword')->name('leader.member.password');
    });

    Route::controller(ClientMessageTypeController::class)->group(function () {
        Route::get('/client-message-type-list', 'list')->name('client.message.type.list');
        Route::get('/create-client-message-type', 'create')->name('client.message.type.create');
        Route::post('/store-client-message-type', 'store')->name('client.message.type.store');
        Route::get('/client-message-type-edit/{id}', 'edit')->name('client.message.type.edit');
        Route::post('/update-client-message-type', 'update')->name('client.message.type.update');
        Route::get('/client-message-type-status', 'status')->name('client.message.type.status');
        Route::post('/client-message-type-delete', 'destroy')->name('client.message.type.destroy');
    });

    Route::controller(ClientMessageController::class)->group(function () {
        Route::get('/client-message/create', 'createForm')->name('client.message.create');
        Route::post('/client-message/store', 'store')->name('client.message.store');
        Route::get('/client-message/my-list', 'myList')->name('client.message.my.list');
        Route::get('/client-message/my/{id}', 'myShow')->name('client.message.my.show');
        Route::get('/client-message/review', 'reviewList')->name('client.message.review.list');
        Route::get('/client-message/review-history', 'reviewHistory')->name('client.message.review.history');
        Route::get('/client-message/review/{id}', 'reviewShow')->name('client.message.review.show');
        Route::get('/client-message/{id}/edit', 'edit')->name('client.message.edit');
        Route::post('/client-message/update', 'update')->name('client.message.update');
        Route::post('/client-message/destroy', 'destroy')->name('client.message.destroy');
        Route::post('/client-message/approve', 'approve')->name('client.message.approve');
        Route::post('/client-message/reject', 'reject')->name('client.message.reject');
    });

    Route::controller(CommunityController::class)->group(function () {
        Route::get('/community-list', 'list')->name('community.list');
        Route::get('/create-community', 'create')->name('community.create');
        Route::post('/store-community', 'store')->name('community.store');
        Route::get('/community-edit/{id}', 'edit')->name('community.edit');
        Route::post('/update-community', 'update')->name('community.update');
        Route::get('/community-status', 'status')->name('community.status');
        Route::post('/community-delete', 'destroy')->name('community.destroy');
    });

    Route::controller(TeamController::class)->group(function () {
        Route::get('/team-list', 'list')->name('team.list');
        Route::get('/create-team', 'create')->name('team.create');
        Route::post('/store-team', 'store')->name('team.store');
        Route::get('/team-edit/{id}', 'edit')->name('team.edit');
        Route::post('/update-team', 'update')->name('team.update');
        Route::get('/team-status', 'status')->name('team.status');
        Route::post('/team-delete', 'destroy')->name('team.destroy');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::post('/avatar_store', 'avatar_store')->name('avatar.store');
        Route::get('/avatar_destroy/{id}', 'avatar_destroy')->name('avatar.destroy');

        Route::get('/profile', 'index')->name('profile.index');
        Route::post('/profile/avatar', 'avatar')->name('profile.avatar');
        Route::post('/profile/cover', 'cover')->name('profile.cover');
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::post('/profile/username-check', 'checkUsername')->name('username.check');
        Route::get('/profile/password', 'password')->name('profile.password');
        Route::post('/profile/password/update', 'password_update')->name('profile.password.update');
        Route::post('/profile/delete', 'destroy')->name('profile.destroy');
    });

    Route::controller(RoleManagementController::class)->group(function () {
        Route::get('/rolemanagement', 'index')->name('role.index');

        Route::get('/get/roles', 'get')->name('role.get');
        Route::post('/create/roles', 'create')->name('role.create');
        Route::post('/update/roles', 'update')->name('role.update');
        Route::post('/destroy/roles', 'destroy')->name('role.destroy');

        Route::get('/get/permissions', 'getPermissions')->name('permissions.get');
        Route::post('/create/permission', 'createPermission')->name('permission.create');
        Route::get('/roles/{roleId}/permissions', 'getPermissionsForRole');
        Route::post('/roles/{roleId}/assign-permissions', 'assignPermissions');
        Route::post('/assign-role-to-user', 'assignToUser');
        Route::post('/roles/update-permissions', 'updatePermissions');

    });
});
