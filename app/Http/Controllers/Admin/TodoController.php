<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    use AjaxResponse;

    // "Fixed" todos: stored server-side so they follow the user across devices.

    public function index()
    {
        $todos = Todo::forUser(Auth::id())->orderBy('order')->orderBy('created_at')->get(['id', 'text', 'completed']);

        return $this->success($todos, 'ok', 200);
    }

    public function store(Request $request)
    {
        $request->validate(['text' => 'required|string|max:255']);

        $nextOrder = (int) (Todo::forUser(Auth::id())->max('order') ?? 0) + 1;

        $todo = Todo::create([
            'user_id'   => Auth::id(),
            'text'      => $request->text,
            'completed' => false,
            'order'     => $nextOrder,
        ]);

        return $this->success($todo, 'Task added', 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'   => 'required|exists:todos,id',
            'text' => 'required|string|max:255',
        ]);

        $todo = Todo::forUser(Auth::id())->findOrFail($request->id);
        $todo->update(['text' => $request->text]);

        return $this->success($todo, 'Task updated', 200);
    }

    public function toggle(Request $request)
    {
        $request->validate(['id' => 'required|exists:todos,id']);

        $todo = Todo::forUser(Auth::id())->findOrFail($request->id);
        $todo->update(['completed' => !$todo->completed]);

        return $this->success($todo, 'Task updated', 200);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:todos,id',
        ]);

        $todos = Todo::forUser(Auth::id())->whereIn('id', $request->ids)->get()->keyBy('id');

        foreach ($request->ids as $index => $id) {
            $todos->get($id)?->update(['order' => $index]);
        }

        return $this->success([], 'Order updated', 200);
    }

    public function toggleAll(Request $request)
    {
        $request->validate(['completed' => 'required|boolean']);

        Todo::forUser(Auth::id())->update(['completed' => $request->boolean('completed')]);

        return $this->success([], 'Tasks updated', 200);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:todos,id']);

        Todo::forUser(Auth::id())->where('id', $request->id)->delete();

        return $this->success([], 'Task deleted', 200);
    }

    public function clearAll()
    {
        Todo::forUser(Auth::id())->delete();

        return $this->success([], 'Tasks cleared', 200);
    }
}
