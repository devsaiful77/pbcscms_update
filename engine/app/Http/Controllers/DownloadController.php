<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download($filename)
    {
        //$filePath = 'path/to/your/files/' . $filename;
        $basePath = str_replace('engine', '', base_path());
        $imageURL = $basePath . 'public/attachments/' . $filename;
        if (file_exists($imageURL)){
            return response()->download($imageURL);
        }
        return redirect()->back();
    }
}
