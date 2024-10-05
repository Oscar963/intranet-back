<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    // Obtener todos los roles (GET /api/roles)
    public function index()
    {
        $activitys = ActivityLog::with('user')->orderBy('created_at', 'DESC')->get();
        return response()->json(['data' => $activitys], 200);
    }
}
