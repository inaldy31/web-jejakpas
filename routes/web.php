<?php

use App\Http\Controllers\Admin\KnowledgeItemDownloadController;
use App\Http\Controllers\Admin\PeerEducatorProfilePdfController;
use App\Http\Controllers\Admin\PksDocumentDownloadController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ImpactController;
use App\Http\Controllers\Public\KnowledgeController;
use App\Http\Controllers\Public\PartnerController;
use App\Http\Controllers\Public\ProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/mitra', [PartnerController::class, 'index'])->name('partners.index');
Route::get('/mitra/{partner}', [PartnerController::class, 'show'])->name('partners.show');

Route::get('/program', ProgramController::class)->name('programs.index');

Route::get('/dampak', ImpactController::class)->name('impact');

Route::get('/pengetahuan', [KnowledgeController::class, 'index'])->name('knowledge.index');
Route::get('/pengetahuan/{knowledgeItem}/unduh', [KnowledgeController::class, 'download'])
    ->middleware('throttle:knowledge-download')
    ->name('knowledge.download');

Route::view('/kebijakan-privasi', 'public.pages.privacy')->name('privacy');
Route::view('/syarat-ketentuan', 'public.pages.terms')->name('terms');
Route::view('/kontak', 'public.pages.contact')->name('contact');

Route::middleware(['auth', 'auth.session', 'cache.headers:no_store;private;max_age=0'])->prefix('admin-berkas')->name('admin-files.')->group(function () {
    Route::get('pks/{pksDocument}', PksDocumentDownloadController::class)
        ->middleware('can:view,pksDocument')
        ->name('pks-document');

    Route::get('peer-educator/{peerCandidate}/profil.pdf', PeerEducatorProfilePdfController::class)
        ->middleware('can:view,peerCandidate')
        ->name('peer-educator-profile');

    Route::get('pengetahuan/{knowledgeItem}', KnowledgeItemDownloadController::class)
        ->middleware('can:view,knowledgeItem')
        ->name('knowledge-item');
});
