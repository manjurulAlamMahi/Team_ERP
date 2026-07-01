@extends('admin.master')
@section('title', 'To Do List')
@section('quickAccessicon', 'ri-checkbox-multiple-line')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="header-title mb-0"><i class="ri-checkbox-multiple-line me-1"></i> My To Do List</h4>
                <span id="todo-count" class="badge bg-primary">0</span>
            </div>
            <div class="card-body">
                <form id="todo-form-page" class="mb-3">
                    <div class="input-group">
                        <input type="text" id="todo-input-page" class="form-control" placeholder="Add a new task...">
                        <button class="btn btn-primary" type="submit"><i class="ri-add-line me-1"></i> Add</button>
                    </div>
                </form>
                <ul class="list-group" id="todo-list-page"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function() {
    var userID = "{{ Auth::id() }}";
    var storageKey = 'tasks_' + userID;

    function loadTasks() {
        var tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        var list = document.getElementById('todo-list-page');
        var count = document.getElementById('todo-count');
        list.innerHTML = '';
        count.textContent = tasks.filter(function(t) { return !t.completed; }).length;

        if (!tasks.length) {
            list.innerHTML = '<li class="list-group-item text-muted text-center py-4"><i class="ri-inbox-line fs-2 d-block mb-1"></i>No tasks yet. Add one above!</li>';
            return;
        }

        tasks.forEach(function(task, index) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex align-items-center gap-2 px-3 py-2' + (task.completed ? ' list-group-item-light' : '');
            li.dataset.index = index;
            li.innerHTML = '<input type="checkbox" class="form-check-input todo-done flex-shrink-0" style="width:18px;height:18px;" ' + (task.completed ? 'checked' : '') + '>'
                + '<span class="flex-grow-1' + (task.completed ? ' text-decoration-line-through text-muted' : '') + '">' + task.text + '</span>'
                + '<button class="btn btn-sm btn-outline-danger delete-btn py-0 px-1"><i class="ri-delete-bin-line"></i></button>';
            list.appendChild(li);
        });
    }

    document.getElementById('todo-form-page').addEventListener('submit', function(e) {
        e.preventDefault();
        var text = document.getElementById('todo-input-page').value.trim();
        if (!text) return;
        var tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        tasks.push({ text: text, completed: false });
        localStorage.setItem(storageKey, JSON.stringify(tasks));
        document.getElementById('todo-input-page').value = '';
        loadTasks();
    });

    document.getElementById('todo-list-page').addEventListener('click', function(e) {
        var li = e.target.closest('li[data-index]');
        if (!li) return;
        var index = parseInt(li.dataset.index);
        var tasks = JSON.parse(localStorage.getItem(storageKey)) || [];

        if (e.target.classList.contains('todo-done') || e.target.closest('.todo-done')) {
            tasks[index].completed = !tasks[index].completed;
            localStorage.setItem(storageKey, JSON.stringify(tasks));
            loadTasks();
        } else if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            tasks.splice(index, 1);
            localStorage.setItem(storageKey, JSON.stringify(tasks));
            loadTasks();
        }
    });

    loadTasks();
})();
</script>
@endpush
