<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function upload(Request $request)
    {
        return response()->json(['message' => 'upload method not implemented']);
    }

    public function initMultipartUpload(Request $request)
    {
        return response()->json(['message' => 'initMultipartUpload method not implemented']);
    }

    public function uploadPart(Request $request, $id)
    {
        return response()->json(['message' => 'uploadPart method not implemented']);
    }

    public function completeMultipartUpload(Request $request, $id)
    {
        return response()->json(['message' => 'completeMultipartUpload method not implemented']);
    }

    public function getFiles(Request $request)
    {
        return response()->json(['message' => 'getFiles method not implemented']);
    }

    public function getFile(Request $request, $id)
    {
        return response()->json(['message' => 'getFile method not implemented']);
    }

    public function deleteFile(Request $request, $id)
    {
        return response()->json(['message' => 'deleteFile method not implemented']);
    }

    public function downloadFile(Request $request, $id)
    {
        return response()->json(['message' => 'downloadFile method not implemented']);
    }

    public function shareFile(Request $request, $id)
    {
        return response()->json(['message' => 'shareFile method not implemented']);
    }
}
