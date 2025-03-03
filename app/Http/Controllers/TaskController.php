<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate', 15);

        $tasks = Task::query()->paginate($paginate);

        // Transform the paginated data using TaskResource collection
        $data = TaskResource::collection($tasks);
        $tasks->setCollection(collect($data));


        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // We Can use custom request form validation for better readability and reusability
        $data = $request->validate([
            'title' => 'required|string',
            'status' => 'sometimes|string',
        ]);

        $data['user_id'] = auth()->id();

        try{
            DB::beginTransaction();
            $task = Task::create($data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'failed to create task'], 500);
        }

        return response()->json(new TaskResource($task), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'status' => 'sometimes|string',
        ]);

        if($task->user_id !== auth()->id()){
            return response()->json(['message' => 'forbidden'], 403);
        }

        try{
            DB::beginTransaction();
            $task->update($data);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'failed to update task'], 500);
        }

        return response()->json(new TaskResource($task));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if($task->user_id !== auth()->id()){
            return response()->json(['message' => 'forbidden'], 403);
        }

        try{
            DB::beginTransaction();
            $task->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'failed to delete task'], 500);
        }

        return response()->json(['message' => 'task deleted successfully'], 204);
    }
}
