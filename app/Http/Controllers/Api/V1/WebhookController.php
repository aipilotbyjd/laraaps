<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleIncomingWebhook(Request $request, $workflowId, $path)
    {
        return response()->json(['message' => 'handleIncomingWebhook method not implemented']);
    }

    public function index(Request $request)
    {
        return response()->json(['message' => 'index method not implemented']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'store method not implemented']);
    }

    public function show(Request $request, $id)
    {
        return response()->json(['message' => 'show method not implemented']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'update method not implemented']);
    }

    public function destroy(Request $request, $id)
    {
        return response()->json(['message' => 'destroy method not implemented']);
    }

    public function test(Request $request, $id)
    {
        return response()->json(['message' => 'test method not implemented']);
    }

    public function getTestUrl(Request $request, $id)
    {
        return response()->json(['message' => 'getTestUrl method not implemented']);
    }

    public function getLogs(Request $request, $id)
    {
        return response()->json(['message' => 'getLogs method not implemented']);
    }

    public function getStats(Request $request, $id)
    {
        return response()->json(['message' => 'getStats method not implemented']);
    }

    public function regenerateToken(Request $request, $id)
    {
        return response()->json(['message' => 'regenerateToken method not implemented']);
    }

    public function updateIpWhitelist(Request $request, $id)
    {
        return response()->json(['message' => 'updateIpWhitelist method not implemented']);
    }
}
