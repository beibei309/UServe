<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\StudentServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\AdminFeedbackController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Pages\AdminPageController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminServicesController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminFaqsController;
use App\Http\Controllers\Admin\AdminCommunityController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentStatusController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;

// -- PUBLIC ROUTES --
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HelpController::class, 'index'])->name('help');

// Community profile
Route::get('/user/{user}', [ProfileController::class, 'showPublic'])->name('profile.public');

// Display the form to join as a part-timer
Route::get('/students/create', [ProfileController::class, 'create'])->middleware(['auth'])->name('students.create');
// Handle the profile form submission
Route::get('/students', [StudentsController::class, 'index'])->middleware(['auth'])->name('students.index');
Route::post('/students/create', [StudentsController::class, 'store'])->middleware(['auth'])->name('students.store');
Route::get('/students/edit-profile', [StudentsController::class, 'edit'])->middleware(['auth'])->name('students.edit');
Route::patch('/students/edit-profile', [StudentsController::class, 'update'])->middleware(['auth'])->name('students.update');
Route::delete('/students/profile/delete-file', [App\Http\Controllers\StudentsController::class, 'deleteWorkExperienceFile'])->middleware(['auth'])->name('students.delete-file');


// -- AUTHENTICATED ROUTES --
Route::middleware(['auth'])->group(function () {
    
    // Route untuk paparkan page verification
    Route::get('/onboarding/students', [VerificationController::class, 'index'])
        ->name('onboarding.students');

    // Route untuk Upload Profile Photo
    Route::post('/verification/upload-photo', [VerificationController::class, 'uploadPhoto'])
        ->name('students_verification.upload');

    // Route untuk Upload Live Selfie
    Route::post('/verification/upload-selfie', [VerificationController::class, 'uploadSelfie'])
        ->name('students_verification.upload_selfie');

    // Route untuk Save Location Data
    Route::post('/verification/save-location', [VerificationController::class, 'saveLocation'])
        ->name('verification.save_location');
    
    Route::get('/onboarding/community', [VerificationController::class, 'onboardingCommunity'])->name('onboarding.community.verify');

    Route::post('/onboarding/community/upload-photo', [VerificationController::class, 'uploadPhoto'])->name('onboarding.community.upload_photo');
    Route::post('/onboarding/community/upload-selfie', [VerificationController::class, 'uploadCommunitySelfie'])->name('onboarding.community.upload_selfie');
    Route::post('/onboarding/community/submit-doc', [VerificationController::class, 'submitDoc'])->name('onboarding.community.submit_doc');
        
        
        

});

// -- SERVICES ROUTES --
Route::get('/services', [StudentServiceController::class, 'index'])->name('services.index');
Route::get('/services/manage', [StudentServiceController::class, 'manage'])->middleware(['auth'])->name('services.manage');
Route::get('/services/create', [StudentServiceController::class, 'create'])->middleware(['auth'])->name('services.create');
Route::post('/services/create', [StudentServiceController::class, 'store'])->middleware(['auth'])->name('services.store');
Route::post('/student-services', [StudentServiceController::class, 'store']);

Route::delete('/services/manage/{service}', [StudentServiceController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('services.destroy');
Route::put('/services/manage/{service}', [StudentServiceController::class, 'update'])
    ->middleware(['auth'])
    ->name('services.update');
Route::get('/services/{service}/edit', [StudentServiceController::class, 'edit'])
    ->middleware(['auth'])
    ->name('services.edit');


Route::get('/student-services/{service}', [StudentServiceController::class, 'show'])->name('student-services.show');

Route::get('/services/apply', [HomeController::class, 'serviceApply'])->middleware(['auth'])->name('services.apply');

 Route::get('/service-requests', [ServiceRequestController::class, 'index'])->middleware(['auth'])->name('service-requests.index');
Route::post('/service-request', [ServiceRequestController::class, 'store'])->middleware(['auth'])->name('service-request.store');

// Service Request routes
Route::middleware(['auth'])->group(function () {
    Route::get('/service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::post('/service-requests/{serviceRequest}/accept', [ServiceRequestController::class, 'accept'])->name('service-requests.accept');
    Route::post('/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])->name('service-requests.reject');
    // start work
    Route::post('/service-requests/{serviceRequest}/mark-in-progress', [ServiceRequestController::class, 'markInProgress'])->name('service-requests.mark-in-progress');
    // finished work
    Route::post('/service-requests/{serviceRequest}/mark-work-finished',[ServiceRequestController::class, 'markWorkFinished'])->name('service-requests.mark-work-finished');
    Route::post('/service-requests/{serviceRequest}/buyer-confirm-payment', [ServiceRequestController::class, 'buyerConfirmPayment'])->name('service-requests.buyer-confirm-payment');
    Route::post('/service-requests/{serviceRequest}/finalize', [App\Http\Controllers\ServiceRequestController::class, 'finalizeOrder'])->name('service-requests.finalize');    
    Route::post('/service-requests/{id}/mark-paid', [ServiceRequestController::class, 'markAsPaid'])->name('service-requests.mark-paid');
    Route::post('/service-requests/{serviceRequest}/report', [ServiceRequestController::class, 'report']) ->name('service-requests.report');
    Route::post('/service-requests/{id}/cancel-dispute', [ServiceRequestController::class, 'cancelDispute'])
    ->name('service-requests.cancel-dispute');
    Route::post('/service-requests/{id}/report-issue', [App\Http\Controllers\ServiceRequestController::class, 'reportIssue'])->name('service-requests.report-issue');
 
    Route::post('/service-requests/{serviceRequest}/mark-completed', [ServiceRequestController::class, 'markCompleted'])->name('service-requests.mark-completed');
    Route::post('/service-requests/{serviceRequest}/cancel', [ServiceRequestController::class, 'cancel'])->name('service-requests.cancel');
});
Route::get('/services/{id}', [StudentServiceController::class, 'details'])->name('services.details');

    // routes/web.php

Route::post('/switch-mode', [App\Http\Controllers\DashboardController::class, 'switchMode'])
    ->name('switch.mode')
    ->middleware('auth');
    
// after login
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Legal pages
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'post'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// UI pages
Route::get('/students/{user}/profile', [StudentsController::class, 'profile'])->name('students.profile');
// Routes moved to auth:admin group
// Routes moved to auth:admin group

// Authenticated JSON endpoints
Route::middleware(['auth'])->group(function () {
    Route::post('/availability/toggle', [AvailabilityController::class, 'toggle'])->name('availability.toggle');
    Route::post('/availability/update-settings', [AvailabilityController::class, 'updateSettings'])->name('availability.updateSettings');


    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::post('/reports', [ReportController::class, 'store']);

    // Favorites routes
    // Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    // Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    // Route::delete('/favorites/{user}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    // Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    // Route::get('/favorites/{user}/check', [FavoriteController::class, 'check'])->name('favorites.check');
    // // Service
    // Route::post('/favourites/service/toggle', [FavoriteController::class, 'toggleService'])
    // ->name('favorites.service.toggle')
    // ->middleware('auth');
    // Route::get('/favourites/service/check/{id}', [FavoriteController::class, 'checkService'])
    // ->name('favorites.service.check')
    // ->middleware('auth');

    Route::post('/favorites/services/toggle', [FavoriteController::class, 'toggleService'])
        ->name('favorites.services.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');
});



// Public JSON endpoints
Route::get('/students/{user}', [StudentServiceController::class, 'storefront']);
Route::get('/search/services', [SearchController::class, 'services']);

require __DIR__.'/auth.php';


/// Admin Login (public)
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    
// Protected Admin Routes - Requires admin authentication
Route::middleware(['auth:admin', 'prevent-back-history'])->prefix('admin')->group(function () {
    
    // ========================================
    // ADMIN DASHBOARD
    // ========================================
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // ========================================
    // VERIFICATION MANAGEMENT
    // ========================================
    // View pending community verifications (document + selfie uploads)
    Route::get('/verifications', [AdminPageController::class, 'verifications'])->name('admin.verifications.page');
    
    // Community Verification Actions
    Route::post('/verifications/{user}/approve', [AdminVerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/verifications/{user}/reject', [AdminVerificationController::class, 'reject'])->name('admin.verifications.reject');
    
    // View uploaded documents
    Route::get('/verifications/{user}/document', [AdminVerificationController::class, 'showDocument'])->name('admin.verifications.document');
    Route::get('/verifications/{user}/selfie', [AdminVerificationController::class, 'showSelfie'])->name('admin.verifications.selfie');
    
    // ========================================
    // STUDENT MANAGEMENT
    // ========================================
    Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/students/view/{id}', [AdminStudentController::class, 'view'])->name('admin.students.view');
    Route::get('/students/{id}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/students/{id}/update', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/students/{id}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');
    
    // Student Actions (Ban/Unban/Role)
    Route::post('/students/{id}/ban', [AdminStudentController::class, 'ban'])->name('admin.students.ban');
    Route::post('/students/{id}/unban', [AdminStudentController::class, 'unban'])->name('admin.students.unban');
    Route::post('/students/{id}/revoke-helper', [AdminStudentController::class, 'revokeHelper'])->name('admin.students.revoke_helper');
    Route::get('/students/{id}/selfie', [AdminStudentController::class, 'showSelfie'])->name('admin.students.selfie');
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('admin.students.export');

    // ========================================
    // SERVICE REQUESTS MANAGEMENT
    // ========================================
    Route::get('/requests', [AdminRequestController::class, 'index'])->name('admin.requests.index');
    Route::delete('/requests/{serviceRequest}', [AdminRequestController::class, 'destroy'])->name('admin.requests.destroy');
    Route::get('/requests/export', [AdminRequestController::class, 'export'])->name('admin.requests.export');
    Route::post('/requests/{id}/resolve', [AdminRequestController::class, 'resolveDispute'])->name('admin.requests.resolve');

    // ========================================
    // REPORTS & FEEDBACK
    // ========================================
    Route::get('/reports', [AdminPageController::class, 'reports'])->name('admin.reports.page');
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::post('/feedback/{user}/warning', [AdminFeedbackController::class, 'sendWarning'])->name('admin.feedback.warning');
    Route::post('/feedback/{user}/enforce', [AdminFeedbackController::class, 'enforceRoleAction'])->name('admin.feedback.enforce');
    Route::post('/feedback/{user}/block', [AdminFeedbackController::class, 'blockUser'])->name('admin.feedback.block');
    Route::post('/feedback/{user}/unblock', [AdminFeedbackController::class, 'unblockUser'])->name('admin.feedback.unblock');

    // ========================================
    // SERVICE MODERATION
    // ========================================
    Route::get('/services', [AdminServicesController::class, 'index'])->name('admin.services.index');
    Route::get('/services/{service}', [AdminServicesController::class, 'show'])->name('admin.services.show');
    Route::get('/services/{service}/reviews', [AdminServicesController::class, 'reviews'])->name('admin.services.reviews');
    Route::patch('/services/{service}/approve', [AdminServicesController::class, 'approve'])->name('admin.services.approve');
    Route::patch('/services/{service}/reject', [AdminServicesController::class, 'reject'])->name('admin.services.reject');
    Route::patch('/services/{service}/suspend', [AdminServicesController::class, 'suspend'])->name('admin.services.suspend');
    Route::patch('/services/{service}/unblock', [AdminServicesController::class, 'unblock'])->name('admin.services.unblock');
    Route::post('/services/{id}/warning', [AdminServicesController::class, 'storeWarning'])->name('admin.services.warn');

    // ========================================
    // CATEGORY MANAGEMENT
    // ========================================
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // ========================================
    // FAQ MANAGEMENT
    // ========================================
    Route::get('/faqs', [AdminFaqsController::class, 'index'])->name('admin.faqs.index');
    Route::get('/faqs/create', [AdminFaqsController::class, 'create'])->name('admin.faqs.create');
    Route::post('/faqs', [AdminFaqsController::class, 'store'])->name('admin.faqs.store');
    Route::get('/faqs/{faq}/edit', [AdminFaqsController::class, 'edit'])->name('admin.faqs.edit');
    Route::put('/faqs/{faq}', [AdminFaqsController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/faqs/{faq}', [AdminFaqsController::class, 'destroy'])->name('admin.faqs.destroy');
    Route::patch('/faqs/{faq}/toggle', [AdminFaqsController::class, 'toggle'])->name('admin.faqs.toggle');

    // ========================================
    // COMMUNITY USER MANAGEMENT
    // ========================================
    Route::prefix('community')->group(function () {
        Route::get('/', [AdminCommunityController::class, 'index'])->name('admin.community.index');
        Route::get('/view/{id}', [AdminCommunityController::class, 'view'])->name('admin.community.view');
        Route::get('/edit/{id}', [AdminCommunityController::class, 'edit'])->name('admin.community.edit');
        Route::put('/update/{id}', [AdminCommunityController::class, 'update'])->name('admin.community.update');
        Route::post('/blacklist/{id}', [AdminCommunityController::class, 'blacklist'])->name('admin.community.blacklist');
        Route::post('/unblacklist/{id}', [AdminCommunityController::class, 'unblacklist'])->name('admin.community.unblacklist');
        Route::delete('/delete/{id}', [AdminCommunityController::class, 'delete'])->name('admin.community.delete');
        Route::get('/export', [AdminCommunityController::class, 'export'])->name('admin.community.export');
    });

    // ========================================
    // STUDENT STATUS MANAGEMENT
    // ========================================
    Route::prefix('student-status')->name('admin.student_status.')->group(function () {
        Route::get('/', [AdminStudentStatusController::class, 'index'])->name('index');
        Route::get('/create', [AdminStudentStatusController::class, 'create'])->name('create');
        Route::post('/store', [AdminStudentStatusController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AdminStudentStatusController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AdminStudentStatusController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AdminStudentStatusController::class, 'destroy'])->name('delete');
        Route::post('/remind/{id}', [AdminStudentStatusController::class, 'sendReminder'])->name('send_reminder');
    });

    // ========================================
    // SUPERADMIN & SYSTEM
    // ========================================
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/admins', [SuperAdminController::class, 'adminsIndex'])->name('admin.super.admins.index');
        Route::get('/admins/create', [SuperAdminController::class, 'create'])->name('admin.super.admins.create');
        Route::post('/admins/store', [SuperAdminController::class, 'store'])->name('admin.super.admins.store');
        Route::get('/admins/{id}/edit', [SuperAdminController::class, 'edit'])->name('admin.super.admins.edit');
        Route::post('/admins/{id}/update', [SuperAdminController::class, 'update'])->name('admin.super.admins.update');
        Route::delete('/admins/{id}', [SuperAdminController::class, 'destroy'])->name('admin.super.admins.delete');

        Route::get('/link-storage', [SuperAdminController::class, 'createStorageLink'])->name('admin.super.link_storage');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// View Community Verification Document
Route::get('/view-doc/{id}', [AdminVerificationController::class, 'showDocumentById'])
    ->name('view.doc')
    ->middleware(['auth:admin']);
