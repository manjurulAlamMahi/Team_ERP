@extends('admin.master')

@section('title', 'My Tasks')
@section('quickAccessicon', 'ri-task-line')

@php
    $incompleteTasks = $tasks->where('status', 'pending')->values();
    $completedTasks = $tasks->where('status', 'completed')->values();
@endphp

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">My Daily Tasks</h5>
        <a href="{{ route('daily.task.add') }}" class="btn btn-primary btn-sm">
            <i class="ri-add-line me-1"></i> Add Task
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-tabs mb-3" id="myTasksTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="incomplete-tab" data-bs-toggle="tab" data-bs-target="#incomplete-pane"
                type="button" role="tab" aria-controls="incomplete-pane" aria-selected="true">
                Incomplete Tasks <span class="badge bg-primary ms-1" id="incomplete-count">{{ $incompleteTasks->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane"
                type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                Completed Tasks <span class="badge bg-success ms-1" id="completed-count">{{ $completedTasks->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTasksTabContent">
        <div class="tab-pane fade show active" id="incomplete-pane" role="tabpanel" aria-labelledby="incomplete-tab">
            @include('admin.pages.daily-task.partials._my-tasks-table', ['tasks' => $incompleteTasks, 'emptyMessage' => 'No incomplete tasks. Add your first task.'])
        </div>
        <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
            @include('admin.pages.daily-task.partials._my-tasks-table', ['tasks' => $completedTasks, 'emptyMessage' => 'No completed tasks yet.'])
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('change', '.task-checkbox', function () {
            const $cb  = $(this);
            const id   = $cb.data('id');

            $.ajax({
                url: "{{ route('daily.task.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status) {
                        const done = res.data.status === 'completed';
                        Toast.fire({ icon: 'success', title: done ? 'Task marked as completed!' : 'Task reopened.' });
                        setTimeout(function () {
                            location.hash = done ? '#completed-pane' : '#incomplete-pane';
                            location.reload();
                        }, 600);
                    }
                },
                error: function () {
                    $cb.prop('checked', !$cb.prop('checked'));
                    Toast.fire({ icon: 'error', title: 'Something went wrong.' });
                }
            });
        });

        $(document).ready(function () {
            if (location.hash === '#completed-pane') {
                new bootstrap.Tab(document.getElementById('completed-tab')).show();
            }
        });
    </script>
@endpush
