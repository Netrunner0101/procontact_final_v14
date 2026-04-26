<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\RappelController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientManagementController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MockOAuthController;
use App\Http\Controllers\PortalController;

// Language switch route
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'fr'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome-production');
})->name('home');

// OAuth Test Route (for development)
Route::get('/oauth-test', function () {
    return view('oauth-test');
})->name('oauth.test');

// Social Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    // Google OAuth
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google');
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('google.callback');
    
    // Apple OAuth
    Route::get('/apple', [SocialAuthController::class, 'redirectToApple'])->name('apple');
    Route::get('/apple/callback', [SocialAuthController::class, 'handleAppleCallback'])->name('apple.callback');
    
    // Unlink social accounts
    Route::post('/unlink', [SocialAuthController::class, 'unlinkSocialAccount'])->name('unlink')->middleware('auth');
});

// Mock OAuth Routes (for testing without real Google credentials)
Route::prefix('mock')->name('mock.')->group(function () {
    Route::get('/oauth/google', [MockOAuthController::class, 'showMockAuth'])->name('oauth.google.show');
    Route::post('/oauth/google', [MockOAuthController::class, 'mockGoogleAuth'])->name('oauth.google');
});

// Profile routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin routes (protected for admin users only)
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard - now using Livewire
    Route::get('/dashboard', function () {
        return view('dashboard-livewire');
    })->name('dashboard');
    
    // Alternative traditional dashboard route
    Route::get('/dashboard-old', [DashboardController::class, 'index'])->name('dashboard.old');
    
    // Livewire Contact Manager
    Route::get('/contacts-manager', function () {
        return view('contacts-manager');
    })->name('contacts.manager');
    
    // Livewire Appointment Manager
    Route::get('/appointments-manager', function () {
        return view('appointments-manager');
    })->name('appointments.manager');
    
    // Livewire Notes Manager
    Route::get('/notes-manager', function () {
        return view('notes-manager');
    })->name('notes.manager');
    
    // Livewire Statistics Dashboard
    Route::get('/statistics-dashboard', function () {
        return view('statistics-dashboard');
    })->name('statistics.dashboard');
    
    Route::resource('contacts', ContactController::class);
    Route::resource('activites', ActiviteController::class);
    Route::resource('rendez-vous', RendezVousController::class)->parameters(['rendez-vous' => 'rendezVous']);
    
    // Contact-Activity relationship routes
    Route::post('activites/{activite}/contacts', [ActiviteController::class, 'attachContact'])->name('activites.contacts.attach');
    Route::delete('activites/{activite}/contacts/{contact}', [ActiviteController::class, 'detachContact'])->name('activites.contacts.detach');
    
    // Email appointment details
    Route::post('rendez-vous/{rendezVous}/email', [RendezVousController::class, 'email'])->name('rendez-vous.email');
    Route::post('/rendez-vous/{rendezVous}/send-email', [RendezVousController::class, 'sendEmail'])->name('rendez-vous.send-email');

    // Reminder routes
    Route::resource('rappels', RappelController::class);
    
    // Note routes
    Route::resource('notes', NoteController::class);
    
    // Statistics routes
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');
    Route::get('/statistiques/activite/{activite}', [StatistiqueController::class, 'activite'])->name('statistiques.activite');
    Route::get('/statistiques/export/global', [StatistiqueController::class, 'exportGlobal'])->name('statistiques.export.global');
    Route::get('/statistiques/export/activite/{activite}', [StatistiqueController::class, 'exportActivite'])->name('statistiques.export.activite');
    
    // Client Management routes (for admins to manage their clients)
    Route::prefix('admin/clients')->name('admin.clients.')->group(function () {
        Route::get('/', [ClientManagementController::class, 'index'])->name('index');
        Route::get('/create', [ClientManagementController::class, 'create'])->name('create');
        Route::post('/', [ClientManagementController::class, 'store'])->name('store');
        Route::get('/{client}', [ClientManagementController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [ClientManagementController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientManagementController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientManagementController::class, 'destroy'])->name('destroy');
    });
});

// Client routes (for client users)
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [ClientController::class, 'appointments'])->name('appointments');
    Route::get('/appointment/{rendezVous}', [ClientController::class, 'showAppointment'])->name('appointment');
});



// Client Portal — Magic-Link (public, no auth required)
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/{token}', [PortalController::class, 'index'])->name('index');
    Route::get('/{token}/appointment/{appointmentId}', [PortalController::class, 'showAppointment'])->name('appointment');
    Route::post('/{token}/appointment/{appointmentId}/note', [PortalController::class, 'storeNote'])->name('storeNote');
});

require __DIR__.'/auth.php';
