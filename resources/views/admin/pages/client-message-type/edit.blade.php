@extends('admin.master')

@section('title', 'Edit Client Message Type - ' . $type->name)
@section('quickAccessicon', 'ri-file-list-3-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('client.message.type.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $type->id }}">

                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-file-list-3-line"></i> Edit Client Message Type
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $type->name) }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Format</label>
                        <div class="col-9">
                            <textarea id="format" name="format" class="form-control @error('format') is-invalid @enderror" rows="3">{{ old('format', $type->format) }}</textarea>
                            @error('format')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Restriction</label>
                        <div class="col-9">
                            <textarea id="restriction" name="restriction" class="form-control @error('restriction') is-invalid @enderror" rows="3">{{ old('restriction', $type->restriction) }}</textarea>
                            @error('restriction')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Mandatory</label>
                        <div class="col-9">
                            <textarea id="mandatory" name="mandatory" class="form-control @error('mandatory') is-invalid @enderror" rows="3">{{ old('mandatory', $type->mandatory) }}</textarea>
                            @error('mandatory')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Status</label>
                        <div class="col-9">
                            <input type="hidden" name="status" value="inactive">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="statusSwitch" name="status"
                                    value="active" {{ old('status', $type->status) == 'active' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusSwitch">Active</label>
                            </div>
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

@push('script')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        ['format', 'restriction', 'mandatory'].forEach(function(id) {
            CKEDITOR.replace(id);
        });
    </script>
@endpush
