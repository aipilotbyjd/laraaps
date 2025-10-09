<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VariableController extends Controller
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

    public function getEnvironments(Request $request)
    {
        return response()->json(['message' => 'getEnvironments method not implemented']);
    }

    public function createEnvironment(Request $request)
    {
        return response()->json(['message' => 'createEnvironment method not implemented']);
    }

    public function updateEnvironment(Request $request, $id)
    {
        return response()->json(['message' => 'updateEnvironment method not implemented']);
    }

    public function deleteEnvironment(Request $request, $id)
    {
        return response()->json(['message' => 'deleteEnvironment method not implemented']);
    }

    public function activateEnvironment(Request $request, $id)
    {
        return response()->json(['message' => 'activateEnvironment method not implemented']);
    }

    public function getSecrets(Request $request)
    {
        return response()->json(['message' => 'getSecrets method not implemented']);
    }

    public function createSecret(Request $request)
    {
        return response()->json(['message' => 'createSecret method not implemented']);
    }

    public function getSecret(Request $request, $id)
    {
        return response()->json(['message' => 'getSecret method not implemented']);
    }

    public function deleteSecret(Request $request, $id)
    {
        return response()->json(['message' => 'deleteSecret method not implemented']);
    }
}
