<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoStoreRequest;
use App\Http\Requests\TodoUpdateRequest;
use App\Http\Resources\TodoResource;
use App\Interfaces\TodoRepositoryInterface;
use App\Models\Todo;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    use CommonTrait;

    public function __construct(TodoRepositoryInterface $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }


    public function index(Request $request)
    {
        return $this->todoRepository->index($request->title);
    }


    public function store(TodoStoreRequest $request)
    {
        return $this->todoRepository->store($request->validated());
    }

    public function update(TodoUpdateRequest $request, $todo_id)
    {
        return $this->todoRepository->update($request->validated(),$todo_id);
    }

    public function delete($todo_id)
    {
        return $this->todoRepository->delete($todo_id);
    }

    public function show($todo_id)
    {
        return $this->todoRepository->show($todo_id);
    }
}
