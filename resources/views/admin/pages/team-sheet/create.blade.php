@extends('admin.master')

@section('title', 'Add Team Sheet')
@section('quickAccessicon', 'ri-file-excel-2-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-file-excel-2-line"></i> Add Team Sheet
                </h5>

                <form action="{{ route('team.sheet.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Sheet Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            value="{{ old('title') }}" placeholder="e.g. Client Onboarding Tracker">
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sheet Link</label>
                        <input type="url" class="form-control @error('link') is-invalid @enderror" name="link"
                            value="{{ old('link') }}" placeholder="https://docs.google.com/spreadsheets/...">
                        @error('link')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end d-flex justify-content-end gap-2">
                        <a href="{{ route('team.sheet.list') }}" class="btn btn-soft-secondary">
                            <i class="ri-close-line"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Add Sheet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
