<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'index method not implemented']);
    }

    public function markAsRead(Request $request, $id)
    {
        return response()->json(['message' => 'markAsRead method not implemented']);
    }

    public function markAllAsRead(Request $request)
    {
        return response()->json(['message' => 'markAllAsRead method not implemented']);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json(['message' => 'destroy method not implemented']);
    }

    public function getSettings(Request $request)
    {
        return response()->json(['message' => 'getSettings method not implemented']);
    }

    public function updateSettings(Request $request)
    {
        return response()->json(['message' => 'updateSettings method not implemented']);
    }

    public function getChannels(Request $request)
    {
        return response()->json(['message' => 'getChannels method not implemented']);
    }

    public function createChannel(Request $request)
    {
        return response()->json(['message' => 'createChannel method not implemented']);
    }

    public function deleteChannel(Request $request, $id)
    {
        return response()->json(['message' => 'deleteChannel method not implemented']);
    }
}
