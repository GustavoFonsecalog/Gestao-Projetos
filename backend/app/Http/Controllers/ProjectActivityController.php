<?php

namespace App\Http\Controllers;

use App\Models\ProjectActivity;
use Illuminate\Http\Request;

class ProjectActivityController extends Controller
{
    public function index(Request $request, $projectId)
    {
        $activities = ProjectActivity::with('user')
            ->where('project_id', $projectId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        return response()->json($activities);
    }

    public function store(Request $request, $projectId)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'data' => 'nullable|array',
        ]);
        $activity = ProjectActivity::create([
            'project_id' => $projectId,
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'description' => $data['description'],
            'data' => $data['data'] ?? null,
        ]);
        return response()->json($activity->load('user'), 201);
    }
}
