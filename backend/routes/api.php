<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('v1.')->prefix('v1')->group(function () {

    Route::post('signup', \App\Http\Controllers\Api\RegistrationController::class)
        ->name('signup')
        ->middleware('guest:api');
    Route::get('/verify-email/{user:email}/{code}', App\Http\Controllers\Api\VerifyEmailController::class)
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');
    Route::post('/login', \App\Http\Controllers\Api\AuthenticationController::class)
        ->middleware('guest')
        ->name('login');
    Route::post('/forgot-password', \App\Http\Controllers\Api\ResetPasswordCodeController::class)
        ->middleware('guest')
        ->name('forgot.password');
    Route::post('/reset-password', \App\Http\Controllers\Api\ResetPasswordController::class)
        ->middleware('guest')
        ->name('reset.password');
    Route::post('/email/verification-notification', \App\Http\Controllers\Api\EmailVerificationNotificationCodeController::class)
        ->middleware(['guest:api', 'throttle:6,1'])
        ->name('verification.send');
    Route::post('users/profile-photo', \App\Http\Controllers\Api\ProfilePhotoController::class)
        ->name('users.profile-photo')
        ->middleware('auth:api');
    Route::get('users/profile', \App\Http\Controllers\Api\ProfileController::class)
        ->name('users.profile')
        ->middleware('auth:api');
    Route::post('/users/refresh', \App\Http\Controllers\Api\RefreshTokenController::class)
        ->middleware('auth:api')
        ->name('users.refresh');
    Route::post('/users/logout', \App\Http\Controllers\Api\LogoutController::class)
        ->middleware('auth:api')
        ->name('users.logout');
    Route::delete('users/destroy', \App\Http\Controllers\Api\DestroyAccountController::class)
        ->name('users.destroy')
        ->middleware('auth:api');
    Route::patch('users/profile/password', \App\Http\Controllers\Api\ChangePasswordController::class)
        ->name('profile.password')
        ->middleware('auth:api');
    Route::patch('users/profile', \App\Http\Controllers\Api\UpdateProfileController::class)
        ->name('profile.update')
        ->middleware('auth:api');
    Route::post('users/phone/verification', [\App\Http\Controllers\Api\PhoneVerificationController::class, 'store'])
        ->name('users.store');
    Route::post('users/phone/verify', [\App\Http\Controllers\Api\PhoneVerificationController::class, 'verify'])
        ->name('users.verify');

    Route::post('users/resumes', [\App\Http\Controllers\Api\ProfileDocumentController::class, 'store'])
        ->name('resumes.store')
        ->middleware(['permissions:userprofile_store', 'auth:api']);

    Route::get('users/resumes', [\App\Http\Controllers\Api\ProfileDocumentController::class, 'show'])
        ->name('resumes.show')
        ->middleware(['permissions:userprofile_show', 'auth:api']);

    Route::get('users/profile', \App\Http\Controllers\Api\ProfileController::class)
        ->middleware('auth:api')
        ->name('users.profile');

    Route::middleware('roles:manager')->name('admin.')->prefix('admin')->group(function () {
        Route::post('employee', [\App\Http\Controllers\Admin\EmployeeController::class, 'store'])
            ->name('employee.store');
        Route::patch('employee/{user:id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])
            ->name('employee.update');
        Route::get('employee', [\App\Http\Controllers\Admin\EmployeeController::class, 'index'])
            ->name('employee.index');
        Route::get('employee/{user:id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'show'])
            ->name('employee.show');

        Route::delete('employee/{user:id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroy'])
            ->name('employee.destroy');

        //Roles
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])
            ->name('roles.index')
            ->middleware('permission.authorize:role_index');

        Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])
            ->name('roles.store')
            ->middleware('permission.authorize:role_store');

        Route::patch('roles/{role:id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])
            ->name('roles.update')
            ->middleware(['permission.authorize:role_update', 'role.update']);

        Route::get('roles/{role:id}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])
            ->name('roles.show')
            ->middleware('permission.authorize:role_show');

        Route::delete('roles/{role:id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])
            ->name('roles.delete')
            ->middleware(['permission.authorize:role_delete', 'role.update']);

        Route::post('users/{user:id}/roles/attach', \App\Http\Controllers\Admin\AttachRoleToUserController::class)
            ->name('users.roles.attach')
            ->middleware(['permission.authorize:user_update', 'permission.authorize:user_store']);

        Route::patch('users/{user:id}/roles/attach', \App\Http\Controllers\Admin\DetachRoleToUserController::class)
            ->name('users.roles.deattach')
            ->middleware(['permission.authorize:user_update']);
    });
    Route::get('places', \App\Http\Controllers\Google\GooglePlaceController::class)
        ->name('places.index');
    Route::get('users/{user}/messages', [\App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::get('users/{user}/messages/{message}', [\App\Http\Controllers\Api\MessageController::class, 'show']);
    Route::patch('users/{user}/messages/{message}', [\App\Http\Controllers\Api\MessageController::class, 'update']);

    Route::middleware(['auth:api', 'roles:manager,staff'])->name('trilio.')->prefix('trilio')->group(function () {
        Route::get('projects', [\App\Http\Controllers\Trilio\Api\ProjectController::class, 'index'])
            ->name('projects.index');
        Route::get('projects/{project:uuid}', [\App\Http\Controllers\Trilio\Api\ProjectController::class, 'show'])
            ->name('projects.show');
        Route::post('projects', [\App\Http\Controllers\Trilio\Api\ProjectController::class, 'store'])
            ->name('projects.store');
        Route::patch('projects/{project:uuid}', [\App\Http\Controllers\Trilio\Api\ProjectController::class, 'update'])
            ->name('projects.update');
        Route::delete('projects/{project:uuid}', [\App\Http\Controllers\Trilio\Api\ProjectController::class, 'destroy'])
            ->name('projects.destroy');

        Route::get('projects/{project:uuid}/activities', [\App\Http\Controllers\Trilio\Api\ActivityController::class, 'index'])
            ->name('activities.index');
        Route::get('projects/activities/{activity:uuid}', [\App\Http\Controllers\Trilio\Api\ActivityController::class, 'show'])
            ->name('activities.show');
        Route::post('projects/{project:uuid}/activities', [\App\Http\Controllers\Trilio\Api\ActivityController::class, 'store'])
            ->name('activities.store');
        Route::patch('projects/activities/{activity:uuid}', [\App\Http\Controllers\Trilio\Api\ActivityController::class, 'update'])
            ->name('activities.update');
        Route::delete('projects/activities/{activity:uuid}', [\App\Http\Controllers\Trilio\Api\ActivityController::class, 'destroy'])
            ->name('activities.destroy');

        Route::get('activities/{activity:uuid}/tasks', [\App\Http\Controllers\Trilio\Api\TaskController::class, 'index'])
            ->name('tasks.index');
        Route::get('activities/activity/{task:uuid}', [\App\Http\Controllers\Trilio\Api\TaskController::class, 'show'])
            ->name('tasks.show');
        Route::post('activities/{activity:uuid}/tasks', [\App\Http\Controllers\Trilio\Api\TaskController::class, 'store'])
            ->name('tasks.store');
        Route::patch('activities/activity/{task:uuid}', [\App\Http\Controllers\Trilio\Api\TaskController::class, 'update'])
            ->name('tasks.update');
        Route::delete('activities/activity/{task:uuid}', [\App\Http\Controllers\Trilio\Api\TaskController::class, 'destroy'])
            ->name('tasks.destroy');
    });

    /**
     * Media management endpoints
     */
    Route::post('media', [\App\Http\Controllers\Api\MediaController::class, 'post'])
        ->name('media.post');
    Route::get('media/{media:uuid}', [\App\Http\Controllers\Api\MediaController::class, 'show'])
        ->name('media.show');
});
