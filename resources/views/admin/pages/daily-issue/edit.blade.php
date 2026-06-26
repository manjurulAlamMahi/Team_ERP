@extends('admin.master')

@section('title', 'Edit Issue')
@section('quickAccessicon', 'ri-alert-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alert-line"></i> Edit Issue
                </h5>
                <p class="text-muted">Date: <strong>{{ $issue->issue_date->format('Y-m-d') }}</strong></p>

                <form action="{{ route('daily.issue.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $issue->id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                name="client_name" value="{{ old('client_name', $issue->client_name) }}">
                            @error('client_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile</label>
                            <input type="text" class="form-control @error('profile_name') is-invalid @enderror"
                                name="profile_name" value="{{ old('profile_name', $issue->profile_name) }}">
                            @error('profile_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issue</label>
                        <textarea class="form-control @error('issue') is-invalid @enderror" name="issue"
                            rows="4">{{ old('issue', $issue->issue) }}</textarea>
                        @error('issue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type">
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ old('type', $issue->type) === $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.pages.daily-issue.partials._responsible-checklist', [
                        'members' => $members,
                        'selectedIds' => old('responsible_ids', $issue->responsibles->pluck('id')->toArray()),
                    ])

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
