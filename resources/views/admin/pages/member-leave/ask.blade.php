@extends('admin.master')

@section('title', 'Ask For Leave')
@section('quickAccessicon', 'ri-calendar-close-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-close-line"></i> Ask For Leave
                </h5>
                <p class="text-muted small mb-3">
                    Let your team lead know you'll be absent, on leave, or working from home. This is logged
                    straight away - no approval needed - and your lead is notified immediately.
                </p>

                <form action="{{ route('member.leave.ask.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            @foreach (['absent' => 'Absent', 'leave' => 'Leave', 'home_office' => 'Home Office'] as $value => $label)
                                <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date <span class="text-muted small">(optional, for multi-day)</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" name="reason"
                            rows="3">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-soft-secondary">
                            <i class="ri-close-line"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
