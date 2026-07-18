@extends('admin.master')

@section('title', 'Community-Edit-' . $community->name)
@section('quickAccessicon', 'ri-building-4-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('community.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $community->id }}">

                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-building-4-line"></i> Edit Community
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $community->name) }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Description</label>
                        <div class="col-9">
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4">{{ old('description', $community->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Status</label>
                        <div class="col-9">
                            <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $community->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $community->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row" style="align-items: end">
                        <div class="text-end d-flex justify-content-end gap-2">
                            <a href="{{ route('community.list') }}" class="btn btn-soft-secondary mt-2">
                                <i class="ri-close-line"></i> Cancel
                            </a>
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
