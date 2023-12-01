<?php

namespace App\Interfaces;

interface TodoRepositoryInterface
{
    public function index($title);
    public function store($data);
    public function update($data,$todo_id);
    public function delete($todo_id);
    public function show($todo_id);
}
