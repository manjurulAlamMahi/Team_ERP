@extends('admin.master')

@section('title', 'Add Leave Record')
@section('quickAccessicon', 'ri-calendar-close-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-close-line"></i> Add Leave Record
                </h5>

                <form action="{{ route('member.leave.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Member</label>
                        <select class="form-select select2 @error('user_id') is-invalid @enderror" name="user_id">
                            <option value="">Select member</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

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
                        <a href="{{ route('member.leave.list') }}" class="btn btn-soft-secondary">
                            <i class="ri-close-line"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/css/select2-theme.css" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/select2/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });
        });
    </script>
@endpush
