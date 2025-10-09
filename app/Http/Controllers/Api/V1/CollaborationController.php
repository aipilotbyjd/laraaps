<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollaborationController extends Controller
{
    public function getPresence(Request $request, $id)
    {
        return response()->json(['message' => 'getPresence method not implemented']);
    }

    public function joinPresence(Request $request, $id)
    {
        return response()->json(['message' => 'joinPresence method not implemented']);
    }

    public function leavePresence(Request $request, $id)
    {
        return response()->json(['message' => 'leavePresence method not implemented']);
    }

    public function submitOperation(Request $request, $id)
    {
        return response()->json(['message' => 'submitOperation method not implemented']);
    }

    public function getOperations(Request $request, $id, $cursor)
    {
        return response()->json(['message' => 'getOperations method not implemented']);
    }

    public function lock(Request $request, $id)
    {
        return response()->json(['message' => 'lock method not implemented']);
    }

    public function unlock(Request $request, $id)
    {
        return response()->json(['message' => 'unlock method not implemented']);
    }
}
