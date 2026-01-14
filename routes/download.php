<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/hak-paten/download-template', function () {

    $path = public_path('templates/template-draft-paten.docx');

    dd([
        "path_yang_dicari" => $path,
        "file_ada?" => File::exists($path)
    ]);

})->name('download.template.draftpaten');