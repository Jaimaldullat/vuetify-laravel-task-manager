<?php

namespace App\Http\Controllers;

use App\Http\Resources\Task as TaskResource;
use App\Http\Resources\TaskCollection;
use App\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends controller
{
    /**
     * Get all the tasks
     *
     * @return TaskCollection
     */
    public function getTasks()
    {
        return new TaskCollection(Task::orderBy('id', 'desc')->paginate());
    }

    /**
     * Get specified task
     *
     * @param int $id
     * @return TaskResource|JsonResponse
     */
    public function getTask($id)
    {
        try {
            $task = Task::findOrFail($id);
            return new TaskResource($task);
        } catch (\Throwable $ex) {
            return $this->handleException($ex);
        }
    }

    /**
     * Handle Exception
     *
     * @param $ex
     * @return \Illuminate\Contracts\Validation\Validator|JsonResponse
     */
    private function handleException($ex)
    {
        if ($ex instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'message' => 'Something went wrong'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create new task
     *
     * @param Request $request
     * @return TaskResource|JsonResponse
     */
    public function store(Request $request)
    {
        $validator = $this->validateTask();

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $task = new Task();
            $task->title = $request->get('title');
            $task->description = $request->get('description');
            $task->start_date = $request->get('start_date');
            $task->due_date = $request->get('due_date');
            $task->status = $request->get('status');
            $task->save();

            return response()->json([
                'message' => 'Task created successfully'
            ], Response::HTTP_OK);
        } catch (\Throwable $ex) {
            return $this->handleException($ex);
        }
    }

    /**
     * Validate Task fields "title" and "start_date"
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateTask()
    {
        return Validator::make(request()->all(), [
            'title' => 'required',
            'start_date' => 'required',
            'status' => 'required',
        ]);
    }

    /**
     * Update existing Task
     *
     * @param Request $request
     * @return TaskResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateTask();

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $task = Task::findOrFail($id);
            $task->title = $request->get('title');
            $task->description = $request->get('description');
            $task->start_date = $request->get('start_date');
            $task->due_date = $request->get('due_date');
            $task->status = $request->get('status');
            $task->save();

            return response()->json([
                'message' => 'Task updated successfully'
            ], Response::HTTP_OK);
        } catch (\Throwable $ex) {
            return $this->handleException($ex);
        }
    }

    /**
     * Remove the specified Task
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json([
                'message' => 'Task deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Throwable $ex) {
            return $this->handleException($ex);
        }
    }
}
