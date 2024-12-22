<?php

use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NeighborhoodController;
use App\Http\Controllers\Api\TermsAndConditionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\NotificationController;
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

Route::POST('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/p-password/reset',  [AuthController::class, 'PasswordReset']);

Route::post('p-forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('pt-forgot-password', [PasswordResetLinkController::class, 'store']);

Route::post('forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::post('p-send-otp', [AuthController::class, 'sendOTP']);
Route::post('p-reset-password', [AuthController::class, 'resetPassword']);

Route::get('/regions', [RegionController::class, 'index']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/neighborhoods', [NeighborhoodController::class, 'index']);
Route::get('/regions/{region}/cities', [CityController::class, 'getCitiesByRegion']);
Route::get('/cities/{city}/neighborhoods', [NeighborhoodController::class, 'getNeighborhoodsByCity']);
Route::get('/number-support', [UserController::class, 'numberSupport']);

Route::prefix('public')->group(function () {
    Route::get('/jobs', [JobController::class, 'getJobs']);
    Route::get('/courses', [CourseController::class, 'getCourses']);
    Route::get('/meetings', [MeetingController::class, 'getMeetings']);
    Route::get('/projects', [ProjectController::class, 'getProjects']);
    Route::get('/discounts', [DiscountController::class, 'getDiscounts']);
    Route::get('/services', [ServiceController::class, 'getServices']);
});
Route::middleware(['blocked', 'auth:sanctum'])->group(function () {
    // User related routes
    Route::prefix('user')->group(function () {
        Route::get('/meetings', [UserController::class, 'getMeetings']);
        Route::get('/courses', [UserController::class, 'getCourses']);
        Route::get('/data', [UserController::class, 'getUser']);
        Route::post('/toggle-visibility', [UserController::class, 'toggleVisibility']);
        Route::get('/getDataUser/{user}', [UserController::class, 'getDataUser']);
        Route::put('/update/{user}', [UserController::class, 'updateProfile']);
        Route::get('/search', [UserController::class, 'searchUser']);
        Route::get('/notifications', [UserController::class, 'getNotificationsForUser']);
        Route::put('/changePassword', [UserController::class, 'changePassword']);
    });
    Route::put('/notifications/AsRead/{notification}', [NotificationController::class, 'markNotificationAsRead']);
    Route::put('/markAllNotificationsAsRead', [NotificationController::class, 'markAllNotificationsAsRead']);
    Route::get('/getNewNotifications', [NotificationController::class, 'getNewNotifications']);
    Route::put('/ChangeStatus', [JobController::class, 'ChangeStatus']);

    Route::apiResources([
        'p-jobs' => JobController::class, // jobs Resource
        'p-courses' => CourseController::class, // courses Resource
        'p-meetings' => MeetingController::class, // meetings Resource
        'p-bank-accounts' => BankAccountController::class, // bank-accounts Resource
        'p-projects' => ProjectController::class,
        'p-discounts' => DiscountController::class,
        'p-services' => ServiceController::class,
        'p-job-application' => JobApplicationController::class,
    ]);

    Route::post('/toggle-block', [BlockedUserController::class, 'toggleBlock']);
    Route::get('/blocked-user', [BlockedUserController::class, 'getBlockedUsers']);
    Route::post('/toggle-show-complainted', [AuthController::class, 'toggleShowNoComplaintedPosts']);

    Route::post('/p-add-favorite/{type}/{id}', [FavoriteController::class, 'toggleFavorite']);
    Route::post('/p-add-like/{type}/{id}', [LikeController::class, 'toggleLike']);
    Route::post('/p-add-complaint/{type}/{id}', [ComplaintController::class, 'toggleComplaint']);
    Route::get('/p-show-complaint/{type}/{id}', [ComplaintController::class, 'showComplaints']);

    Route::get('/user/favorites', [FavoriteController::class, 'getUserFavorites']);
    Route::get('/getDataDashboard', [DashboardController::class, 'getDataDashboard']);
    Route::post('/terms-and-conditions', [TermsAndConditionController::class, 'createOrUpdate']);
    Route::get('/terms-and-conditions/last', [TermsAndConditionController::class, 'getLastTermsAndCondition']);
    Route::post('/complaints/{complaintId}/edit', [ComplaintController::class, 'editComplaint']);
    Route::delete('/complaints/{complaintId}', [ComplaintController::class, 'deleteComplaint']);
    

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


    Route::get('/job-applications/count/{jobId}', [JobApplicationController::class, 'getJobApplicationCount']);
    Route::get('/job-applications/for-user/{jobId}', [JobApplicationController::class, 'getJobApplicationsForUserAndJob']);
    Route::get('/job-applications/by-user', [JobApplicationController::class, 'getJobApplicationsByUserId']);
});
