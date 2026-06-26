@extends('admin.master')

@section('title', 'Create Client Message Type')
@section('quickAccessicon', 'ri-file-list-3-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('client.message.type.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-file-list-3-line"></i> Create Client Message Type
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
                        <label class="col-3 col-form-label">Icon</label>
                        <div class="col-9">
                            <input type="file" accept="image/*" class="form-control form-control-sm @error('icon') is-invalid @enderror"
                                name="icon">
                            <div class="form-text">Shown on the type-selection card. Optional.</div>
                            @error('icon')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Short Description</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('short_description') is-invalid @enderror"
                                name="short_description" value="{{ old('short_description') }}"
                                placeholder="One line shown on the type-selection card" maxlength="255">
                            @error('short_description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Format</label>
                        <div class="col-9">
                            <textarea id="format" name="format" class="form-control @error('format') is-invalid @enderror" rows="3" placeholder="How the message should be formatted">{{ old('format') }}</textarea>
                            @error('format')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Restriction</label>
                        <div class="col-9">
                            <textarea id="restriction" name="restriction" class="form-control @error('restriction') is-invalid @enderror" rows="3" placeholder="What is NOT allowed in this type of message">{{ old('restriction') }}</textarea>
                            @error('restriction')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Mandatory</label>
                        <div class="col-9">
                            <textarea id="mandatory" name="mandatory" class="form-control @error('mandatory') is-invalid @enderror" rows="3" placeholder="What must be included in this type of message">{{ old('mandatory') }}</textarea>
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
                                    value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
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
                                <i class="ri-save-line"></i> Create
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>
    <script>
        const richTextEditors = {};
        ['format', 'restriction', 'mandatory'].forEach(function(id) {
            ClassicEditor.create(document.getElementById(id)).then(function(editor) {
                richTextEditors[id] = editor;
            }).catch(function(error) {
                console.error(error);
            });
        });

        document.querySelector('form').addEventListener('submit', function() {
            Object.keys(richTextEditors).forEach(function(id) {
                document.getElementById(id).value = richTextEditors[id].getData();
            });
        });
    </script>
@endpush
