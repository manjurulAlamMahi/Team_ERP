@extends('admin.master')

@section('title', 'Send Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <form action="{{ route('client.message.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-customer-service-2-line"></i> Send Client Message
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Message Type</label>
                        <div class="col-9">
                            <select name="client_message_type_id" id="typeSelect"
                                class="form-control form-control-sm @error('client_message_type_id') is-invalid @enderror"
                                onchange="showSpec(this.value)">
                                <option value="">-- Select Type --</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('client_message_type_id', $selectedType?->id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_message_type_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @include('admin.pages.client-message.partials._form-fields')

                    <div class="row mb-3">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-send-plane-line"></i> Submit for Approval
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-6">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-information-line"></i> Type Requirements
                </h5>
                @foreach ($types as $t)
                    <div class="type-spec" id="spec-{{ $t->id }}"
                        style="display: {{ old('client_message_type_id', $selectedType?->id) == $t->id ? 'block' : 'none' }}">
                        @include('admin.pages.client-message.partials._spec', ['type' => $t])
                    </div>
                @endforeach
                <div id="spec-empty" class="text-muted"
                    style="display: {{ old('client_message_type_id', $selectedType?->id) ? 'none' : 'block' }}">
                    Select a message type to see its format, restriction and mandatory requirements.
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function showSpec(id) {
            document.querySelectorAll('.type-spec').forEach(function(el) {
                el.style.display = 'none';
            });
            var empty = document.getElementById('spec-empty');
            if (id) {
                var el = document.getElementById('spec-' + id);
                if (el) el.style.display = 'block';
                if (empty) empty.style.display = 'none';
            } else if (empty) {
                empty.style.display = 'block';
            }
        }
    </script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('their_message');
    </script>
@endpush
