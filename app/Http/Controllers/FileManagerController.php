<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function handleCallback(Request $request)
    {
        $url = $request->input('url'); // URL của ảnh đã chọn
        return response()->json(['url' => $url]);

    }
}
