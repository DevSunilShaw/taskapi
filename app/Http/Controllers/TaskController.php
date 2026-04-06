<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;




class TaskController extends Controller
{

    private function authorizeTask(Task $task)
    {
        Log::info('Authorizing task access', [
            'task_id' => $task->id,
            'task_user_id' => $task->user_id,
            'auth_user_id' => auth()->id(),
        ]);

        if ($task->user_id !== auth()->id()) {

            Log::warning('Unauthorized task access attempt', [
                'task_id' => $task->id,
                'auth_user_id' => auth()->id(),
            ]);

            abort(403, 'Unauthorized');
        }

        Log::info('Task authorization successful', [
            'task_id' => $task->id,
            'auth_user_id' => auth()->id(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            Log::info('Fetching task list', [
                'user_id' => $request->user()->id,
                'filters' => $request->all()
            ]);

            $query = $request->user()->tasks();

            if ($request->status) {
                $query->where('status', $request->status);

                Log::info('Filtering by status', [
                    'status' => $request->status
                ]);
            }

            if ($request->due_date) {
                $query->whereDate('due_date', $request->due_date);

                Log::info('Filtering by due_date', [
                    'due_date' => $request->due_date
                ]);
            }

            $tasks = $query->paginate(10);

            Log::info('Tasks fetched successfully', [
                'user_id' => $request->user()->id,
                'total_tasks' => $tasks->total()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tasks fetched successfully',
                'data' => $tasks
            ], 200);

        } catch (Exception $e) {

            Log::error('Error fetching tasks', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string',
    //         'description' => 'nullable|string',
    //         'status' => 'in:pending,in-progress,completed',
    //         'due_date' => 'nullable|date'
    //     ]);

    //     $task = $request->user()->tasks()->create($validated);

    //     return response()->json($task, 201);
    // }
    public function store(Request $request)
    {
        try {

            Log::info('Create Task API hit', [
                'user_id' => $request->user()->id,
                'payload' => $request->all()
            ]);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:pending,in-progress,completed',
                'due_date' => 'nullable|date'
            ]);

            $task = $request->user()->tasks()->create($validated);

            Log::info('Task created successfully', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id
            ]);

            return response()->json($task, 201);

        } catch (Exception $e) {

            Log::error('Task creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'message' => 'Task creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Task $task)
    // {
    //     $this->authorizeTask($task);
    //     return response()->json($task);
    // }

    public function show(Task $task)
    {
        try {

            Log::info('Show Task API hit', [
                'task_id' => $task->id
            ]);

            $this->authorizeTask($task);

            return response()->json($task);

        } catch (Exception $e) {

            Log::error('Fetch single task failed', [
                'error' => $e->getMessage(),
                'task_id' => $task->id ?? null
            ]);

            return response()->json([
                'message' => 'Unable to fetch task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Task $task)
    // {
    //     $this->authorizeTask($task);

    //     $task->update($request->all());

    //     return response()->json($task);
    // }
    public function update(Request $request, Task $task)
    {
        try {

            Log::info('Update Task API hit', [
                'task_id' => $task->id,
                'user_id' => $request->user()->id,
                'payload' => $request->all()
            ]);

            $this->authorizeTask($task);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|in:pending,in-progress,completed',
                'due_date' => 'nullable|date'
            ]);

            $task->update($validated);

            Log::info('Task updated successfully', [
                'task_id' => $task->id
            ]);

            return response()->json($task);

        } catch (Exception $e) {

            Log::error('Task update failed', [
                'error' => $e->getMessage(),
                'task_id' => $task->id ?? null
            ]);

            return response()->json([
                'message' => 'Task update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Task $task)
    // {
    //     $this->authorizeTask($task);

    //     $task->delete();

    //     return response()->json(['message' => 'Deleted']);
    // }
    public function destroy(Task $task)
    {
        try {

            Log::info('Delete Task API hit', [
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);

            $this->authorizeTask($task);

            $task->delete();

            Log::info('Task deleted successfully', [
                'task_id' => $task->id
            ]);

            return response()->json([
                'message' => 'Task deleted successfully'
            ]);

        } catch (Exception $e) {

            Log::error('Task delete failed', [
                'error' => $e->getMessage(),
                'task_id' => $task->id ?? null
            ]);

            return response()->json([
                'message' => 'Task delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    
}
