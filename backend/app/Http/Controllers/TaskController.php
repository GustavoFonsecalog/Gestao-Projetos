<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, $projectId)
    {
        $tasks = Task::with('subtasks', 'user')
            ->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
        return response()->json($tasks);
    }

    public function store(Request $request, $projectId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
            'prioridade' => 'required|string',
        ]);
        $task = Task::create(array_merge($data, [
            'project_id' => $projectId,
        ]));
        return response()->json($task->load('subtasks', 'user'), 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|required|string',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
            'prioridade' => 'sometimes|required|string',
        ]);
        $task->update($data);
        return response()->json($task->load('subtasks', 'user'));
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Tarefa excluída com sucesso.']);
    }
}
