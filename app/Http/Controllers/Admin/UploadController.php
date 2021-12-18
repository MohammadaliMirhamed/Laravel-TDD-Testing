<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UploadController extends Controller
{

    public function upload(Request $request)
    {
        $request->validate([
            "file" => "image|min:250|dimensions:max_width=100,max_height=200"
        ]);

        $file = $request->file('file');

        $file->move(\public_path('/upload/'), $file->hashName());

        return [ 'url' => '/upload/' . $file->hashName() ];
    }
}
