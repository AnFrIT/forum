<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageController extends Controller
{
    public function serve($path)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            throw new NotFoundHttpException('File not found');
        }

        $fullPath = Storage::disk('public')->path($path);
        
        return new BinaryFileResponse($fullPath);
    }
} 