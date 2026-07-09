@extends('admin.master')

@section('title', 'Assign Task')
@section('quickAccessicon', 'ri-user-add-line')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-user-add-line me-1"></i> Assign Task to Team Member</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('daily.task.assign.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Assign To <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">— Select Member —</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                        @if ($member->stack) ({{ $member->stack->name }}) @endif
                                        — {{ $member->getRoleNames()->first() ?? 'No Role' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Date</label>
                            <input type="text" class="form-control bg-light" value="{{ today()->format('d F Y') }}" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Client Name <span class="text-danger">*</span></label>
                                <input type="text" name="client_name"
                                    class="form-control @error('client_name') is-invalid @enderror"
                                    value="{{ old('client_name') }}" placeholder="Client name">
                                @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Profile Name <span class="text-danger">*</span></label>
                                <input type="text" name="profile_name"
                                    class="form-control @error('profile_name') is-invalid @enderror"
                                    value="{{ old('profile_name') }}" placeholder="Profile name">
                                @error('profile_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @include('admin.pages.daily-task.partials._plan-details-field', ['fieldId' => 'assignPlanDetails', 'selected' => old('plan_details')])

                        <div class="mb-3">
                            <label class="form-label fw-medium">Remarks <span class="text-muted">(optional)</span></label>
                            <textarea name="remarks" rows="3"
                                class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Add any guidance or instructions...">{{ old('remarks') }}</textarea>
                            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Expected Complete Date <span class="text-muted">(optional)</span></label>
                            <input type="date" name="expected_complete_date"
                                class="form-control @error('expected_complete_date') is-invalid @enderror"
                                value="{{ old('expected_complete_date') }}">
                            @error('expected_complete_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-send-plane-line me-1"></i> Assign Task
                            </button>
                            <a href="{{ route('daily.task.all') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
