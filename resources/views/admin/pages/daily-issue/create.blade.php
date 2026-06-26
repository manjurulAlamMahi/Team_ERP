@extends('admin.master')

@section('title', 'Create Issue')
@section('quickAccessicon', 'ri-alert-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alert-line"></i> Create Issue
                </h5>
                <p class="text-muted">Date: <strong>{{ today()->format('Y-m-d') }}</strong> (automatically set, cannot be
                    changed)</p>

                <form action="{{ route('daily.issue.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                name="client_name" value="{{ old('client_name') }}">
                            @error('client_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile</label>
                            <input type="text" class="form-control @error('profile_name') is-invalid @enderror"
                                name="profile_name" value="{{ old('profile_name') }}">
                            @error('profile_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issue</label>
                        <textarea class="form-control @error('issue') is-invalid @enderror" name="issue"
                            rows="4">{{ old('issue') }}</textarea>
                        @error('issue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type">
                            <option value="">Select Type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.pages.daily-issue.partials._responsible-checklist', [
                        'members' => $members,
                        'selectedIds' => old('responsible_ids', []),
                    ])

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Create Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
