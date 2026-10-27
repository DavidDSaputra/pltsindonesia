<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS admin
|--------------------------------------------------------------------------
| Everything here requires an authenticated user with the "manage-content"
| ability (admin or editor role).
*/

Route::middleware(['auth', 'verified', 'can:manage-content'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('/', 'pages::admin.dashboard')->name('dashboard');

        // Articles
        Route::livewire('artikel', 'pages::admin.articles.index')->name('articles.index');
        Route::livewire('artikel/baru', 'pages::admin.articles.form')->name('articles.create');
        Route::livewire('artikel/{article}/ubah', 'pages::admin.articles.form')->name('articles.edit');

        // Taxonomy
        Route::livewire('kategori', 'pages::admin.categories.index')->name('categories.index');
        Route::livewire('tag', 'pages::admin.tags.index')->name('tags.index');

        // Media
        Route::livewire('media', 'pages::admin.media.index')->name('media.index');

        // Content blocks
        Route::livewire('layanan', 'pages::admin.services.index')->name('services.index');
        Route::livewire('proyek', 'pages::admin.projects.index')->name('projects.index');
        Route::livewire('testimoni', 'pages::admin.testimonials.index')->name('testimonials.index');
        Route::livewire('faq', 'pages::admin.faqs.index')->name('faqs.index');
        Route::livewire('galeri', 'pages::admin.galleries.index')->name('galleries.index');

        // Leads
        Route::livewire('pesan', 'pages::admin.messages.index')->name('messages.index');
        Route::livewire('pelanggan', 'pages::admin.subscribers.index')->name('subscribers.index');

        // System
        Route::livewire('pengaturan', 'pages::admin.settings.index')->name('settings.index');
        Route::livewire('pengguna', 'pages::admin.users.index')->name('users.index')->middleware('can:admin');
    });
