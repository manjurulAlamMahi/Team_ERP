@extends('admin.master')
@section('title', 'To Do List')
@section('quickAccessicon', 'ri-checkbox-multiple-line')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="header-title mb-0"><i class="ri-checkbox-multiple-line me-1"></i> My To Do List</h4>
                <div class="d-flex align-items-center gap-2">
                    <span id="todo-count" class="badge bg-primary">0</span>
                    <button type="button" id="todo-mark-all" class="btn btn-sm btn-outline-success"><i class="ri-checkbox-multiple-line me-1"></i> Mark All</button>
                    <button type="button" id="todo-unmark-all" class="btn btn-sm btn-outline-secondary"><i class="ri-checkbox-multiple-blank-line me-1"></i> Unmark All</button>
                    <button type="button" id="todo-clear-all" class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line me-1"></i> Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <form id="todo-form-page" class="mb-3">
                    <div class="input-group">
                        <input type="text" id="todo-input-page" class="form-control" placeholder="Add a new task...">
                        <span class="input-group-text">
                            <input type="checkbox" id="todo-fixed-page" class="form-check-input mt-0 me-1">
                            <label for="todo-fixed-page" class="mb-0 fs-13" title="Show this task on every device you log in on">Fixed</label>
                        </span>
                        <button class="btn btn-primary" type="submit"><i class="ri-add-line me-1"></i> Add</button>
                    </div>
                </form>
                <ul class="list-group" id="todo-list-page"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        #todo-list-page .todo-drag-handle { cursor: grab; }
        #todo-list-page .todo-drag-handle:active { cursor: grabbing; }
        #todo-list-page li.gu-transit { opacity: .3; }
        #todo-list-page .todo-edit-input { max-width: 100%; }
    </style>
@endpush

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/dragula/dragula.min.js"></script>
<script>
(function() {
    var userID = "{{ Auth::id() }}";
    var storageKey = 'tasks_' + userID;
    var fixedTasks = [];

    function uid() {
        return 'l' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
    }

    function getLocalTasks() {
        var tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        var changed = false;
        tasks.forEach(function(t) {
            if (!t.uid) { t.uid = uid(); changed = true; }
        });
        if (changed) setLocalTasks(tasks);
        return tasks;
    }

    function setLocalTasks(tasks) {
        localStorage.setItem(storageKey, JSON.stringify(tasks));
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function buildItem(text, completed, source, key, fixed) {
        var li = document.createElement('li');
        li.className = 'list-group-item d-flex align-items-center gap-2 px-3 py-2' + (completed ? ' list-group-item-light' : '');
        li.dataset.source = source;
        li.dataset.key = key;
        li.innerHTML = '<i class="ri-draggable todo-drag-handle text-muted flex-shrink-0" title="Drag to reorder"></i>'
            + '<input type="checkbox" class="form-check-input todo-done flex-shrink-0" style="width:18px;height:18px;" ' + (completed ? 'checked' : '') + '>'
            + (fixed ? '<i class="ri-pushpin-2-fill text-primary flex-shrink-0" title="Fixed - synced across your devices"></i>' : '')
            + '<span class="todo-text flex-grow-1' + (completed ? ' text-decoration-line-through text-muted' : '') + '">' + escapeHtml(text) + '</span>'
            + '<button type="button" class="btn btn-sm btn-outline-secondary edit-btn py-0 px-1"><i class="ri-edit-line"></i></button>'
            + '<button type="button" class="btn btn-sm btn-outline-danger delete-btn py-0 px-1"><i class="ri-delete-bin-line"></i></button>';
        return li;
    }

    function render() {
        var localTasks = getLocalTasks();
        var list = document.getElementById('todo-list-page');
        var count = document.getElementById('todo-count');
        list.innerHTML = '';

        var total = fixedTasks.length + localTasks.length;
        var pending = fixedTasks.filter(function(t) { return !t.completed; }).length
            + localTasks.filter(function(t) { return !t.completed; }).length;
        count.textContent = pending;

        if (!total) {
            list.innerHTML = '<li class="list-group-item text-muted text-center py-4"><i class="ri-inbox-line fs-2 d-block mb-1"></i>No tasks yet. Add one above!</li>';
            return;
        }

        fixedTasks.forEach(function(task) {
            list.appendChild(buildItem(task.text, task.completed, 'server', task.id, true));
        });
        localTasks.forEach(function(task) {
            list.appendChild(buildItem(task.text, task.completed, 'local', task.uid, false));
        });
    }

    function fetchFixedTasks(callback) {
        $.get("{{ route('todo.items') }}", function(res) {
            fixedTasks = (res && res.data) ? res.data : [];
        }).fail(function() {
            fixedTasks = [];
        }).always(function() {
            if (callback) callback();
        });
    }

    document.getElementById('todo-form-page').addEventListener('submit', function(e) {
        e.preventDefault();
        var input = document.getElementById('todo-input-page');
        var fixedCheckbox = document.getElementById('todo-fixed-page');
        var text = input.value.trim();
        if (!text) return;

        if (fixedCheckbox.checked) {
            $.post("{{ route('todo.store') }}", { text: text, _token: '{{ csrf_token() }}' }, function(res) {
                fixedTasks.push(res.data);
                input.value = '';
                fixedCheckbox.checked = false;
                render();
            }).fail(function() {
                Toast.fire({ icon: 'error', title: 'Could not add task' });
            });
        } else {
            var tasks = getLocalTasks();
            tasks.push({ text: text, completed: false, uid: uid() });
            setLocalTasks(tasks);
            input.value = '';
            render();
        }
    });

    function startEdit(li) {
        var source = li.dataset.source;
        var key = li.dataset.key;
        var $span = $(li).find('.todo-text');
        var currentText = $span.text();

        var $input = $('<input type="text" class="form-control form-control-sm todo-edit-input">').val(currentText);
        $span.replaceWith($input);
        $input.trigger('focus').select();

        function finishEdit(save) {
            var newText = $input.val().trim();
            if (save && newText && newText !== currentText) {
                if (source === 'server') {
                    $.post("{{ route('todo.update') }}", { id: key, text: newText, _token: '{{ csrf_token() }}' }, function(res) {
                        var task = fixedTasks.find(function(t) { return String(t.id) === String(key); });
                        if (task) task.text = res.data.text;
                        render();
                    }).fail(function() {
                        Toast.fire({ icon: 'error', title: 'Could not update task' });
                        render();
                    });
                } else {
                    var tasks = getLocalTasks();
                    var task = tasks.find(function(t) { return String(t.uid) === String(key); });
                    if (task) task.text = newText;
                    setLocalTasks(tasks);
                    render();
                }
            } else {
                render();
            }
        }

        $input.on('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); finishEdit(true); }
            if (e.key === 'Escape') { e.preventDefault(); finishEdit(false); }
        }).on('blur', function() { finishEdit(true); });
    }

    document.getElementById('todo-list-page').addEventListener('click', function(e) {
        var li = e.target.closest('li[data-key]');
        if (!li) return;
        var source = li.dataset.source;
        var key = li.dataset.key;

        if (e.target.classList.contains('todo-done') || e.target.closest('.todo-done')) {
            if (source === 'server') {
                $.post("{{ route('todo.toggle') }}", { id: key, _token: '{{ csrf_token() }}' }, function(res) {
                    var task = fixedTasks.find(function(t) { return String(t.id) === String(key); });
                    if (task) task.completed = res.data.completed;
                    render();
                }).fail(function() {
                    Toast.fire({ icon: 'error', title: 'Could not update task' });
                });
            } else {
                var tasks = getLocalTasks();
                var task = tasks.find(function(t) { return String(t.uid) === String(key); });
                if (task) task.completed = !task.completed;
                setLocalTasks(tasks);
                render();
            }
        } else if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            if (source === 'server') {
                $.post("{{ route('todo.destroy') }}", { id: key, _token: '{{ csrf_token() }}' }, function() {
                    fixedTasks = fixedTasks.filter(function(t) { return String(t.id) !== String(key); });
                    render();
                }).fail(function() {
                    Toast.fire({ icon: 'error', title: 'Could not delete task' });
                });
            } else {
                var tasks = getLocalTasks();
                tasks = tasks.filter(function(t) { return String(t.uid) !== String(key); });
                setLocalTasks(tasks);
                render();
            }
        } else if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
            startEdit(li);
        }
    });

    // Drag-to-reorder: only the grip handle initiates a drag, so text selection /
    // button clicks inside a row are unaffected.
    var todoDrake = dragula([document.getElementById('todo-list-page')], {
        moves: function(el, container, handle) {
            return !!handle.closest && !!handle.closest('.todo-drag-handle');
        }
    });

    todoDrake.on('drop', function() {
        var order = Array.prototype.map.call(
            document.getElementById('todo-list-page').querySelectorAll('li[data-key]'),
            function(li) { return { source: li.dataset.source, key: li.dataset.key }; }
        );

        var serverIds = order.filter(function(o) { return o.source === 'server'; }).map(function(o) { return o.key; });
        var localKeys = order.filter(function(o) { return o.source === 'local'; }).map(function(o) { return o.key; });

        if (serverIds.length) {
            fixedTasks.sort(function(a, b) {
                return serverIds.indexOf(String(a.id)) - serverIds.indexOf(String(b.id));
            });
            $.post("{{ route('todo.reorder') }}", { ids: serverIds, _token: '{{ csrf_token() }}' }).fail(function() {
                Toast.fire({ icon: 'error', title: 'Could not save new order' });
            });
        }

        if (localKeys.length) {
            var tasks = getLocalTasks();
            tasks.sort(function(a, b) {
                return localKeys.indexOf(String(a.uid)) - localKeys.indexOf(String(b.uid));
            });
            setLocalTasks(tasks);
        }
    });

    function setAllCompleted(completed) {
        var tasks = getLocalTasks();
        tasks.forEach(function(t) { t.completed = completed; });
        setLocalTasks(tasks);

        if (!fixedTasks.length) {
            render();
            return;
        }

        $.post("{{ route('todo.toggle.all') }}", { completed: completed ? 1 : 0, _token: '{{ csrf_token() }}' }, function() {
            fixedTasks.forEach(function(t) { t.completed = completed; });
            render();
        }).fail(function() {
            Toast.fire({ icon: 'error', title: 'Could not update tasks' });
            render();
        });
    }

    document.getElementById('todo-mark-all').addEventListener('click', function() {
        setAllCompleted(true);
    });

    document.getElementById('todo-unmark-all').addEventListener('click', function() {
        setAllCompleted(false);
    });

    document.getElementById('todo-clear-all').addEventListener('click', function() {
        if (!confirm('Clear all tasks? This cannot be undone.')) return;
        setLocalTasks([]);

        $.post("{{ route('todo.clear.all') }}", { _token: '{{ csrf_token() }}' }, function() {
            fixedTasks = [];
            render();
        }).fail(function() {
            Toast.fire({ icon: 'error', title: 'Could not clear tasks' });
            fixedTasks = [];
            render();
        });
    });

    fetchFixedTasks(render);
})();
</script>
@endpush
