<?php

namespace App\Repositories;

use App\Http\Resources\TodoResource;
use App\Interfaces\TodoRepositoryInterface;
use App\Models\Todo;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;

class TodoRepository implements TodoRepositoryInterface
{
    use CommonTrait;

    public function index($title)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('title', 'like', '%' . $title . '%')->paginate(10);
            return $this->sendSuccess('Todo fetched successfully', TodoResource::collection($todo));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function store($data)
    {
        try {
            $data['user_id'] = Auth::user()->id;
            Todo::create($data);
            return $this->sendSuccess('Todo created successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function update($data,$todo_id)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('id', $todo_id)->first();
            if (!$todo) {
                return $this->sendError('Todo not found', true);
            }
            $todo->update($data);
            return $this->sendSuccess('Todo updated successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function delete($todo_id)
    {
        try {
            $user_id = Auth::user()->id;
            $todo = Todo::where('user_id', $user_id)->where('id', $todo_id)->first();
            if (!$todo) {
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
            $todo = Todo::where('user_id', $user_id)->where('id', $todo_id)->first();
            if (!$todo) {
                return $this->sendError('Todo not found', true);
            }
            return $this->sendSuccess('Todo fetched successfully', new TodoResource($todo));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

}
