<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoubtController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GuidebookCategoryController;
use App\Http\Controllers\GuidebookController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonUserController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes for the public-facing website and authenticated user actions.
|
*/


// Authentication
Route::get('/login', [WebController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login-post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout-post')->middleware('auth');

// Password Reset
Route::match(['get', 'post'], '/recuperar-senha', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::match(['get', 'post'], '/redefinir-senha/{token?}', [AuthController::class, 'resetPassword'])->name('password.reset');

// Group all website routes under a single name prefix.
Route::name('web.')->group(function () {
    // --- Public & Authentication Routes ---
    Route::get('/', [WebController::class, 'index'])->name('index');
    Route::get('/aulas', [WebController::class, 'classes'])->name('classes');
    Route::get('/aula/{lesson}', [WebController::class, 'class'])->name('class');
    Route::get('/professores', [WebController::class, 'teachers'])->name('teachers');
    Route::get('/professor/{user}', [WebController::class, 'teacher'])->name('teacher');
    Route::get('/informacoes', [WebController::class, 'informations'])->name('informations');
    Route::get('/sugerir-temas', [WebController::class, 'suggestions'])->name('suggestions');
    Route::get('/termos-de-uso', [WebController::class, 'userTerms'])->name('user.terms');

    // Certificates
    Route::match(['get', 'post'], '/validar-certificado', [WebController::class, 'validateCertificate'])->name('validate.certificate');

    // Profile
    Route::get('/perfil', [WebController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [AuthController::class, 'perfilCreate'])->name('perfil-create');

    // --- Authenticated User Routes ---
    Route::middleware('auth')->group(function () {

        // Library
        Route::get('/biblioteca', [WebController::class, 'library'])->name('library');

        Route::patch('/perfil/{user}', [AuthController::class, 'perfilUpdate'])->name('perfil-update');

        // Suggestions
        Route::post('/sugerir-temas', [WebController::class, 'suggestionCreate'])->name('suggestion-create');
        Route::patch('/sugerir-temas/{suggestion}', [WebController::class, 'suggestionUpdate'])->name('suggestion-update');

        // My Classes & Lessons
        Route::get('/minhas-aulas', [WebController::class, 'myClasses'])->name('myClasses');
        Route::post('/aula/{lesson}/subscribe', [LessonUserController::class, 'subscribe'])->name('subscribe');
        Route::post('/aula/{lesson}/history', [HistoryController::class, 'saveHistory'])->name('save-history');
        Route::post('/perguntar/{lesson}', [DoubtController::class, 'doubtCreate'])->name('doubt-create');

        // Certificate Generation
        Route::get('/certificates/{lesson}', [WebController::class, 'generateStudentCertificate'])->name('certificates.generate');

        // Lesson-specific actions (Quiz and Feedback)
        Route::prefix('/aula/{lesson}')->group(function () {
            // Quiz Routes
            Route::prefix('/quiz')->name('quiz.')->group(function () {
                Route::get('/next-question', [QuizController::class, 'getNextQuestion'])->name('nextQuestion');
                Route::post('/submit-answer', [QuizController::class, 'submitAnswer'])->name('submitAnswer');
                Route::post('/clear-session', [QuizController::class, 'clearSession'])->name('clearSession');
            });

            // Feedback Routes
            Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Routes for the admin panel, accessible by Coordinators and Professors.
|
*/

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'can:isCoordenadorOrProfessor'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // --- AJAX Search Routes ---
    Route::get('/users/ajax', [UserController::class, 'searchUser'])->name('search-user');
    Route::get('/professors/ajax', [UserController::class, 'searchProfessor'])->name('search-professor');
    Route::get('/lessons/ajax', [LessonController::class, 'searchLesson'])->name('search-lesson');

    // --- Lesson Management ---
    Route::post('lessons/attachments/upload', [TopicController::class, 'attachmentsUpload'])->name('attachments.upload');
    Route::post('lessons/attachments/delete', [TopicController::class, 'attachmentsDelete'])->name('attachments.delete');
    Route::post('lessons/{lesson}/change-status', [LessonController::class, 'changeStatus'])->name('lessons.change-status');
    Route::get('lessons/{lesson}/certificates/{user}', [LessonController::class, 'generateTeacherCertificate'])->name('lessons.certificates');

    // --- Resource Routes (Professor & Coordinator) ---
    Route::resource('videos', VideoController::class);
    Route::resource('lessons', LessonController::class);
    Route::resource('lessons.topics', TopicController::class);
    Route::resource('lessons.subscriptions', LessonUserController::class);
    Route::resource('lessons.doubts', DoubtController::class);
    Route::resource('lessons.feedbacks', FeedbackController::class);
    Route::resource('lessons.messages', MessageController::class);
    Route::resource('suggestions', SuggestionController::class);
    Route::resource('guidebooks', GuidebookController::class);
    Route::resource('guidebook-categories', GuidebookCategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | Coordinator-Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:isCoordenador')->group(function () {
        // User Management
        Route::post('users/{user}/active', [UserController::class, 'toggleActiveUser'])->name('users.active');
        Route::resource('users', UserController::class);

        // Other Resources
        Route::resource('specialties', SpecialtyController::class);
        Route::resource('quizzes', QuizController::class);
        Route::resource('histories', HistoryController::class);
        Route::resource('libraries', LibraryController::class);
        Route::resource('sectors', SectorController::class);

        // Reports
        Route::name('reports.')->prefix('reports')->group(function () {
            Route::get('periods', [ReportController::class, 'reportByPeriod'])->name('periods');
            Route::get('periods/export', [ReportController::class, 'exportPeriodsPdf'])->name('periods.export');
            Route::get('students', [ReportController::class, 'reportByStudent'])->name('students');
            Route::get('students/export', [ReportController::class, 'exportStudentsPdf'])->name('students.export');
            Route::get('teachers', [ReportController::class, 'reportByTeacher'])->name('teachers');
            Route::get('teachers/export', [ReportController::class, 'exportTeachersPdf'])->name('teachers.export');
            Route::get('lessons', [ReportController::class, 'reportByLesson'])->name('lessons');
            Route::get('lessons/export', [ReportController::class, 'exportLessonsPdf'])->name('lessons.export');
        });
    });
});
