<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Todo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function fetch_all_todos($title)
    {
        $user_id = Auth::user()->id;
        $todo = Todo::where('user_id', $user_id)->where('title', 'like', '%' . $title . '%')->paginate(10);
        return $todo;
    }

    public static function create_todo($data)
    {
        $data['user_id'] = Auth::user()->id;
        Todo::create($data);
        return true;
    }
}
