@extends('admin.master')

@section('title', 'Edit Daily Reminder')
@section('quickAccessicon', 'ri-alarm-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alarm-line"></i> Edit Daily Reminder
                </h5>

                <form action="{{ route('daily.reminder.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $reminder->id }}">
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea class="form-control @error('details') is-invalid @enderror" name="details"
                            rows="4">{{ old('details', $reminder->details) }}</textarea>
                        @error('details')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                name="due_date" value="{{ old('due_date', $reminder->due_date->format('Y-m-d')) }}">
                            @error('due_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Time</label>
                            <input type="time" class="form-control @error('due_time') is-invalid @enderror"
                                name="due_time" value="{{ old('due_time', substr($reminder->due_time, 0, 5)) }}">
                            @error('due_time')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Update Reminder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
