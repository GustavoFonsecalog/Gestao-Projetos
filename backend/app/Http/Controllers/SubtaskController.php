<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function index(Request $request, $taskId)
    {
        $subtasks = Subtask::where('task_id', $taskId)->orderBy('order')->get();
        return response()->json($subtasks);
    }

    public function store(Request $request, $taskId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'status' => 'required|string',
            'is_checked' => 'boolean',
            'order' => 'nullable|integer',
        ]);
        $subtask = Subtask::create(array_merge($data, [
            'task_id' => $taskId,
        ]));
        return response()->json($subtask, 201);
    }

    public function update(Request $request, $id)
    {
        $subtask = Subtask::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'is_checked' => 'boolean',
            'order' => 'nullable|integer',
        ]);
        $subtask->update($data);
        return response()->json($subtask);
    }

    public function destroy($id)
    {
        $subtask = Subtask::findOrFail($id);
        $subtask->delete();
        return response()->json(['message' => 'Subtarefa excluída com sucesso.']);
    }
}
