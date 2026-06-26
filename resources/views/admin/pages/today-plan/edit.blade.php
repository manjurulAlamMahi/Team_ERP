@extends('admin.master')

@section('title', 'Edit Plan')
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-todo-line"></i>
                    Edit {{ $task->source === 'leader_assigned' ? 'Assigned Task' : 'Plan' }}
                </h5>

                <form
                    action="{{ $task->source === 'leader_assigned' ? route('today.plan.assigned.update') : route('today.plan.update') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $task->id }}">

                    <div class="mb-3">
                        <label class="form-label">Client Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                            name="client_name" value="{{ old('client_name', $task->client_name) }}">
                        @error('client_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile</label>
                        <input type="text" class="form-control @error('profile_name') is-invalid @enderror"
                            name="profile_name" value="{{ old('profile_name', $task->profile_name) }}">
                        @error('profile_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea class="form-control @error('details') is-invalid @enderror" name="details"
                            rows="4">{{ old('details', $task->details) }}</textarea>
                        @error('details')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
