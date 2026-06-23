@extends('admin.master')

@section('title', 'Team-Edit-' . $team->name)
@section('quickAccessicon', 'ri-team-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('team.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $team->id }}">

                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-team-line"></i> Edit Team
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Community</label>
                        <div class="col-9">
                            <select name="community_id" class="form-control form-control-sm @error('community_id') is-invalid @enderror">
                                <option value="">-- Select Community --</option>
                                @foreach ($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id', $team->community_id) == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Team Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $team->name) }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Started Date</label>
                        <div class="col-9">
                            <input type="date" class="form-control form-control-sm @error('started_at') is-invalid @enderror"
                                name="started_at" value="{{ old('started_at', $team->started_at?->format('Y-m-d')) }}">
                            @error('started_at')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Description</label>
                        <div class="col-9">
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4">{{ old('description', $team->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Status</label>
                        <div class="col-9">
                            <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $team->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $team->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row" style="align-items: end">
                        <div class="text-end">
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
