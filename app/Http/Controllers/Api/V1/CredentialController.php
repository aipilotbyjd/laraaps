<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
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

    public function getTypes(Request $request)
    {
        return response()->json(['message' => 'getTypes method not implemented']);
    }

    public function getTypeSchema(Request $request, $type)
    {
        return response()->json(['message' => 'getTypeSchema method not implemented']);
    }

    public function test(Request $request, $id)
    {
        return response()->json(['message' => 'test method not implemented']);
    }

    public function getTestStatus(Request $request, $id)
    {
        return response()->json(['message' => 'getTestStatus method not implemented']);
    }

    public function oauthAuthorize(Request $request, $id)
    {
        return response()->json(['message' => 'oauthAuthorize method not implemented']);
    }

    public function oauthCallback(Request $request, $id)
    {
        return response()->json(['message' => 'oauthCallback method not implemented']);
    }

    public function oauthRefresh(Request $request, $id)
    {
        return response()->json(['message' => 'oauthRefresh method not implemented']);
    }

    public function getShares(Request $request, $id)
    {
        return response()->json(['message' => 'getShares method not implemented']);
    }

    public function createShare(Request $request, $id)
    {
        return response()->json(['message' => 'createShare method not implemented']);
    }

    public function deleteShare(Request $request, $id, $userId)
    {
        return response()->json(['message' => 'deleteShare method not implemented']);
    }

    public function getUsage(Request $request, $id)
    {
        return response()->json(['message' => 'getUsage method not implemented']);
    }

    public function getWorkflows(Request $request, $id)
    {
        return response()->json(['message' => 'getWorkflows method not implemented']);
    }
}
