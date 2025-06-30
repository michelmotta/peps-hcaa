<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoubtController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GuidebookController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonUserController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WebController;
use App\Models\LessonUser;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;

// Website Routes
Route::name('web.')->group(function () {
    // Public pages (everyone can access)
    Route::get('/', [WebController::class, 'index'])->name('index');
    Route::get('/aulas', [WebController::class, 'classes'])->name('classes');
    Route::get('/aula/{lesson}', [WebController::class, 'class'])->name('class');
    Route::get('/professores', [WebController::class, 'teachers'])->name('teachers');
    Route::get('/professor/{user}', [WebController::class, 'teacher'])->name('teacher');
    Route::get('/informacoes', [WebController::class, 'informations'])->name('informations');
    Route::get('/login', [WebController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login-post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout-post');
    Route::get('/perfil', [WebController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [AuthController::class, 'perfilCreate'])->name('perfil-create');
    Route::get('/sugerir-temas', [WebController::class, 'suggestions'])->name('suggestions');
    Route::get('/biblioteca', [WebController::class, 'library'])->name('library');

    // Protected routes (only logged-in users)
    Route::middleware('auth')->group(function () {
        Route::post('/sugerir-temas', [WebController::class, 'suggestionCreate'])->name('suggestion-create');
        Route::patch('/sugerir-temas/{suggestion}', [WebController::class, 'suggestionUpdate'])->name('suggestion-update');
        Route::patch('/perfil/{user}', [AuthController::class, 'perfilUpdate'])->name('perfil-update');
        Route::get('/minhas-aulas', [WebController::class, 'myClasses'])->name('myClasses');
        Route::post('/perguntar/{lesson}', [DoubtController::class, 'doubtCreate'])->name('doubt-create');
        Route::post('/aula/{lesson}/subscribe', [LessonUserController::class, 'subscribe'])->name('subscribe');
        Route::post('/aula/{lesson}/history', [HistoryController::class, 'saveHistory'])->name('save-history');

        //Quiz Routes
        Route::get('/aula/{lesson}/quiz/next-question', [QuizController::class, 'getNextQuestion'])->name('quiz.nextQuestion');
        Route::post('/aula/{lesson}/quiz/submit-answer', [QuizController::class, 'submitAnswer'])->name('quiz.submitAnswer');
        Route::post('/aula/{lesson}/quiz/clear-session', [QuizController::class, 'clearSession'])->name('quiz.clearSession');
        Route::get('/certificates/{lesson}', [QuizController::class, 'generateCertificate'])->name('certificates.generate');

        //Feedback Routes
        Route::post('/aula/{lesson}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    });
});



// Dashboard Routes (Coordenador or Professor)
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'can:isCoordenadorOrProfessor'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/users/ajax', [UserController::class, 'searchUser'])->name('search-user');
    Route::resource('lessons', LessonController::class);
    Route::post('lessons/{lesson}/change-status', [LessonController::class, 'changeStatus'])->name('lessons.change-status');
    Route::post('lessons/attachments/upload', [TopicController::class, 'attachmentsUpload'])->name('attachments.upload');
    Route::post('lessons/attachments/delete', [TopicController::class, 'attachmentsDelete'])->name('attachments.delete');
    Route::resource('lessons.topics', TopicController::class);
    Route::resource('lessons.students', LessonUserController::class);
    Route::resource('lessons.doubts', DoubtController::class);
    Route::resource('suggestions', SuggestionController::class);
    Route::resource('guidebooks', GuidebookController::class);    

    // Dashboard Routes (Coordenador only)
    Route::middleware('can:isCoordenador')->group(function () {
        Route::post('users/{user}/active', [UserController::class, 'toggleActiveUser'])->name('users.active');
        Route::resource('information', InformationController::class);
        Route::resource('specialties', SpecialtyController::class);
        Route::resource('users', UserController::class);
        Route::resource('quizzes', QuizController::class);
        Route::resource('histories', HistoryController::class);
        Route::resource('libraries', LibraryController::class);
    });
});
