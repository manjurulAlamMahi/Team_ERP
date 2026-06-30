@extends('admin.master')

@section('title', 'Add Daily Task')
@section('quickAccessicon', 'ri-task-line')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-add-circle-line me-1"></i> Add Daily Task</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('daily.task.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" value="{{ today()->format('d F Y') }}" readonly>
                            <small class="text-muted">Tasks are always created for today's date.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="client_name"
                                    class="form-control @error('client_name') is-invalid @enderror"
                                    value="{{ old('client_name') }}" placeholder="Enter client name">
                                @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Profile Name <span class="text-danger">*</span></label>
                                <input type="text" name="profile_name"
                                    class="form-control @error('profile_name') is-invalid @enderror"
                                    value="{{ old('profile_name') }}" placeholder="Enter profile name">
                                @error('profile_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Plan Details <span class="text-danger">*</span></label>
                            <textarea name="plan_details" rows="5"
                                class="form-control @error('plan_details') is-invalid @enderror"
                                placeholder="Describe the task in detail...">{{ old('plan_details') }}</textarea>
                            @error('plan_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Expected Complete Date <span class="text-muted">(optional)</span></label>
                            <input type="date" name="expected_complete_date"
                                class="form-control @error('expected_complete_date') is-invalid @enderror"
                                value="{{ old('expected_complete_date') }}"
                                min="{{ today()->format('Y-m-d') }}">
                            @error('expected_complete_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Add Task
                            </button>
                            <a href="{{ route('daily.task.my') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
