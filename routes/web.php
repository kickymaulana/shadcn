<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Event/Show', [
        'event' => [
            'id' => 1,
            'title' => 'judul',
        ],
    ]);
});
