@extends('admin.master')

@section('title', 'Create-Community')
@section('quickAccessicon', 'ri-building-4-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('community.store') }}" method="POST">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-building-4-line"></i> Create Community
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Description</label>
                        <div class="col-9">
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Status</label>
                        <div class="col-9">
                            <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row" style="align-items: end">
                        <div class="text-end">
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Create
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
