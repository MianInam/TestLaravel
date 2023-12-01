<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoStoreRequest;
use App\Http\Requests\TodoUpdateRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    use CommonTrait;

    public function index(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('title', 'like', '%' . $request->title . '%')->paginate(10);
            return $this->sendSuccess('Todo fetched successfully', TodoResource::collection($todo));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }


    public function store(TodoStoreRequest $request)
    {
        try {
            $request = $request->validated();
            $request['user_id'] = Auth::user()->id;
            Todo::create($request);
            return $this->sendSuccess('Todo created successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function update(TodoUpdateRequest $request,$todo_id)
    {
        try {
            $request = $request->validated();
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('id',$todo_id)->first();
            if (!$todo){
                return $this->sendError('Todo not found', true);
            }
            $todo->update($request);
            return $this->sendSuccess('Todo updated successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function delete($todo_id)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('id',$todo_id)->first();
            if (!$todo){
                return $this->sendError('Todo not found', true);
            }
            $todo->delete();
            return $this->sendSuccess('Todo deleted successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function show($todo_id)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('id',$todo_id)->first();
            if (!$todo){
                return $this->sendError('Todo not found', true);
            }
            return $this->sendSuccess('Todo deleted successfully', new TodoResource($todo));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }
}
