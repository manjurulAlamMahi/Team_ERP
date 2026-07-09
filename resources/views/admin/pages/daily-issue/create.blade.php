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
                <form action="{{ route('daily.issue.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control bg-light" value="{{ today()->format('d F Y') }}" readonly>
                    </div>

                    @include('admin.partials._client-select-field', [
                        'clients' => $clients,
                        'selected' => old('client_id'),
                        'fieldId' => 'issueClient',
                    ])

                    <div class="mb-3">
                        <label class="form-label">Issue</label>
                        <textarea class="form-control @error('issue') is-invalid @enderror" name="issue"
                            rows="4">{{ old('issue') }}</textarea>
                        @error('issue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Type</label>
                        @foreach ($types as $type)
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="type-{{ $type }}"
                                    value="{{ $type }}" {{ old('type', 'Normal') === $type ? 'checked' : '' }}>
                                <label class="form-check-label" for="type-{{ $type }}">{{ $type }}</label>
                            </div>
                        @endforeach
                        @error('type')
                            <div class="text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.pages.daily-issue.partials._category-field', [
                        'categories' => $categories,
                        'selected' => old('category'),
                        'fieldId' => 'createCategory',
                    ])

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
