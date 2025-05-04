<?php

use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NeighborhoodController;
use App\Http\Controllers\Api\SearchUserController;
use App\Http\Controllers\Api\TermsAndConditionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserSettingController;

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

Route::post('/p-send-otp', [OtpController::class, 'sendOtp']);
Route::post('/p-reset-password', [OtpController::class, 'verifyOtp']);
Route::post('/verify-otp-register', [OtpController::class, 'verifyOtpRegister']);
Route::post('/send-message', [OtpController::class, 'sendMessage']);

Route::POST('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/getDataDashboard', [DashboardController::class, 'getDataDashboard']);
Route::get('/terms-and-conditions/last', [TermsAndConditionController::class, 'getLastTermsAndCondition']);

Route::get('/regions', [RegionController::class, 'index']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/neighborhoods', [NeighborhoodController::class, 'index']);
Route::get('/regions/{region}/cities', [CityController::class, 'getCitiesByRegion']);
Route::get('/cities/{city}/neighborhoods', [NeighborhoodController::class, 'getNeighborhoodsByCity']);
Route::get('/number-support', [SupportController::class, 'numberSupport']);

Route::middleware(['blocked'])->group(function () {
    // User related routes
    Route::prefix('user')->group(function () {
        Route::get('/meetings', [UserController::class, 'getMeetings']);
        Route::get('/courses', [UserController::class, 'getCourses']);
        Route::get('/data', [UserController::class, 'getUser']);

        Route::get('/getDataUser/{user}', [UserController::class, 'getDataUser']);
        Route::put('/update/{user}', [UserController::class, 'updateProfile']);
        Route::put('/changePassword', [UserController::class, 'changePassword']);
        Route::get('/notifications', [UserController::class, 'getNotificationsForUser']);
        Route::post('/toggle-visibility', [UserSettingController::class, 'toggleVisibility']);
        Route::get('/search', [SearchUserController::class, 'searchUser']);
    });
    Route::post('/toggle-show-complainted', [UserSettingController::class, 'toggleShowNoComplaintedPosts']);
    Route::put('/notifications/AsRead/{notification}', [NotificationController::class, 'markNotificationAsRead']);
    Route::put('/markAllNotificationsAsRead', [NotificationController::class, 'markAllNotificationsAsRead']);
    Route::get('/getNewNotifications', [NotificationController::class, 'getNewNotifications']);
    Route::put('/ChangeStatus', [JobController::class, 'ChangeStatus']);

    Route::apiResource('p-jobs', JobController::class);
    Route::apiResource('p-courses', CourseController::class);
    Route::apiResource('p-meetings', MeetingController::class);
    Route::apiResource('p-projects', ProjectController::class);
    Route::apiResource('p-discounts', DiscountController::class);
    Route::apiResource('p-services', ServiceController::class);
    Route::apiResource('p-job-application', JobApplicationController::class);
    Route::apiResource('p-bank-accounts', BankAccountController::class);


    Route::get('/getDataDashboard/authenticated', [DashboardController::class, 'getAuthenticatedDataDashboard'])->name('dashboard.authenticated');

    Route::post('/toggle-block', [BlockedUserController::class, 'toggleBlock']);
    Route::get('/blocked-user', [BlockedUserController::class, 'getBlockedUsers']);

    Route::get('/user/favorites', [FavoriteController::class, 'getUserFavorites']);
    Route::post('/p-add-favorite/{type}/{id}', [FavoriteController::class, 'toggleFavorite']);
    
    Route::post('/p-add-like/{type}/{id}', [LikeController::class, 'toggleLike']);

  
    Route::post('/terms-and-conditions', [TermsAndConditionController::class, 'createOrUpdate']);

    Route::post('/complaints/{complaintId}/edit', [ComplaintController::class, 'editComplaint']);
    Route::delete('/complaints/{complaintId}', [ComplaintController::class, 'deleteComplaint']);
    Route::post('/p-add-complaint/{type}/{id}', [ComplaintController::class, 'toggleComplaint']);
    Route::get('/p-show-complaint/{type}/{id}', [ComplaintController::class, 'showComplaints']);

    // Search
    Route::get('/p-projectsSearch', [ProjectController::class, 'search']);
    Route::get('/p-coursesSearch', [CourseController::class, 'search']);
    Route::get('/p-meetingsSearch', [MeetingController::class, 'search']);
    Route::get('/p-jobsSearch', [JobController::class, 'search']);
    Route::get('/p-discountsSearch', [DiscountController::class, 'search']);
    Route::get('/p-servicesSearch', [ServiceController::class, 'search']);

    // Bank related routes
    Route::prefix('bank')->group(function () {
        Route::get('/getSavings', [BankAccountController::class, 'getSavings']);
        Route::get('/getCharities', [BankAccountController::class, 'getCharities']);
    });
    Route::get('/getCharityAndSavingBankAccounts', [BankAccountController::class, 'getCharityAndSavingBankAccounts']);


    Route::prefix('job-applications')->group(function () {
        Route::get('/count/{jobId}', [JobApplicationController::class, 'getJobApplicationCount']);
        Route::get('/for-user/{jobId}', [JobApplicationController::class, 'getJobApplicationsForUserAndJob']);
        Route::get('/by-user', [JobApplicationController::class, 'getJobApplicationsByUserId']);
    });

    Route::prefix('chat')->group(function () {
        Route::post('/conversation', [ChatController::class, 'createConversation']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::get('/messages/{id}', [ChatController::class, 'getMessages']);
        Route::get('/Conversations', [ChatController::class, 'getConversations']);
        Route::get('/user/Conversations', [ChatController::class, 'getUserConversations']);
        Route::get('/conversations/{conversationId}/participants', [ChatController::class, 'getConversationParticipants']);
        Route::post('conversations/{conversation}/participants', [ChatController::class, 'addParticipantsToConversation']);
        Route::post('/conversations/{conversation}/photo', [ChatController::class, 'updatePhoto']);
    });
});
