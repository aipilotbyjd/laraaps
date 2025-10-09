<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'index method not implemented']);
    }

    public function getCategories(Request $request)
    {
        return response()->json(['message' => 'getCategories method not implemented']);
    }

    public function getTags(Request $request)
    {
        return response()->json(['message' => 'getTags method not implemented']);
    }

    public function getCustomNodes(Request $request)
    {
        return response()->json(['message' => 'getCustomNodes method not implemented']);
    }

    public function createCustomNode(Request $request)
    {
        return response()->json(['message' => 'createCustomNode method not implemented']);
    }

    public function updateCustomNode(Request $request, $id)
    {
        return response()->json(['message' => 'updateCustomNode method not implemented']);
    }

    public function deleteCustomNode(Request $request, $id)
    {
        return response()->json(['message' => 'deleteCustomNode method not implemented']);
    }

    public function publishCustomNode(Request $request, $id)
    {
        return response()->json(['message' => 'publishCustomNode method not implemented']);
    }

    public function getUsageStats(Request $request)
    {
        return response()->json(['message' => 'getUsageStats method not implemented']);
    }

    public function show(Request $request, $type)
    {
        return response()->json(['message' => 'show method not implemented']);
    }

    public function getSchema(Request $request, $type)
    {
        return response()->json(['message' => 'getSchema method not implemented']);
    }

    public function testNode(Request $request, $type)
    {
        return response()->json(['message' => 'testNode method not implemented']);
    }

    public function validateConfig(Request $request, $type)
    {
        return response()->json(['message' => 'validateConfig method not implemented']);
    }

    public function getDynamicParameters(Request $request, $type)
    {
        return response()->json(['message' => 'getDynamicParameters method not implemented']);
    }

    public function resolveParameters(Request $request, $type)
    {
        return response()->json(['message' => 'resolveParameters method not implemented']);
    }

    public function getNodeUsage(Request $request, $type)
    {
        return response()->json(['message' => 'getNodeUsage method not implemented']);
    }
}
