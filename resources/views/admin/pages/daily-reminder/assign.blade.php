@extends('admin.master')

@section('title', 'Assign Reminder')
@section('quickAccessicon', 'ri-alarm-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alarm-line"></i> Assign Reminder to Member
                </h5>

                <form action="{{ route('daily.reminder.assign.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Team Member</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" name="user_id">
                            <option value="">Select Member</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea class="form-control @error('details') is-invalid @enderror" name="details"
                            rows="4">{{ old('details') }}</textarea>
                        @error('details')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                            name="due_date" value="{{ old('due_date') }}">
                        @error('due_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Assign Reminder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
