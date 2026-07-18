@extends('admin.master')

@section('title', 'New Announcement')
@section('quickAccessicon', 'ri-megaphone-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-megaphone-line"></i> New Announcement
                </h5>

                <form action="{{ route('announcement.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            value="{{ old('title') }}" placeholder="e.g. Office closed on Friday">
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                            rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror" name="priority">
                                @foreach (['info' => 'Info', 'warning' => 'Warning', 'urgent' => 'Urgent'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Show Until</label>
                            <input type="date" class="form-control @error('ends_at') is-invalid @enderror"
                                name="ends_at" min="{{ today()->format('Y-m-d') }}" value="{{ old('ends_at') }}">
                            @error('ends_at')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Post Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
