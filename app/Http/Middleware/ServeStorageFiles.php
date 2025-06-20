<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServeStorageFiles
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        
        // Check if the request is for a storage file
        if (str_starts_with($path, 'storage/')) {
            $storagePath = str_replace('storage/', '', $path);
            
            // Check if file exists
            if (Storage::disk('public')->exists($storagePath)) {
                $fullPath = Storage::disk('public')->path($storagePath);
                return new BinaryFileResponse($fullPath);
            }
        }

        return $next($request);
    }
} 