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

                        @include('admin.partials._client-select-field', [
                            'clients' => $clients,
                            'selected' => old('client_id'),
                            'fieldId' => 'addTaskClient',
                        ])

                        @include('admin.pages.daily-task.partials._plan-details-field', ['fieldId' => 'addPlanDetails', 'selected' => old('plan_details')])

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
