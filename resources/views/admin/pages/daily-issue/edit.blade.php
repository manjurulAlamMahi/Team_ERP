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
                <form action="{{ route('daily.issue.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $issue->id }}">

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control bg-light" value="{{ $issue->issue_date->format('d F Y') }}" readonly>
                    </div>

                    @include('admin.partials._client-select-field', [
                        'clients' => $clients,
                        'selected' => old('client_id', $issue->client_id),
                        'fieldId' => 'editIssueClient',
                    ])

                    <div class="mb-3">
                        <label class="form-label">Issue</label>
                        <textarea class="form-control @error('issue') is-invalid @enderror" name="issue"
                            rows="4">{{ old('issue', $issue->issue) }}</textarea>
                        @error('issue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Type</label>
                        @foreach ($types as $type)
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="edit-type-{{ $type }}"
                                    value="{{ $type }}" {{ old('type', $issue->type) === $type ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit-type-{{ $type }}">{{ $type }}</label>
                            </div>
                        @endforeach
                        @error('type')
                            <div class="text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.pages.daily-issue.partials._category-field', [
                        'categories' => $categories,
                        'selected' => old('category', $issue->category),
                        'fieldId' => 'editCategory',
                    ])

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
