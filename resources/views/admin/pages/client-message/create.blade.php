@extends('admin.master')

@section('title', 'Send Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    <h5 class="mb-3 text-uppercase bg-light p-2">
        <i class="ri-customer-service-2-line"></i> Select Message Type
    </h5>

    @include('admin.pages.client-message.partials._type-cards', [
        'types' => $types,
        'selectedTypeId' => old('client_message_type_id', $selectedType?->id),
    ])

    <form action="{{ route('client.message.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="typeSelect" name="client_message_type_id"
            value="{{ old('client_message_type_id', $selectedType?->id) }}">
        @error('client_message_type_id')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <div id="messageFormSection" style="display: {{ old('client_message_type_id', $selectedType?->id) ? 'block' : 'none' }};">
            <div class="row">
                <div class="col-lg-6">
                    @include('admin.pages.client-message.partials._form-fields')

                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Submit for Approval
                        </button>
                    </div>
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
                            Select a message type above to see its format, restriction and mandatory requirements.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>
    <script>
        let theirMessageEditor;
        ClassicEditor.create(document.getElementById('their_message')).then(function(editor) {
            theirMessageEditor = editor;
        }).catch(function(error) {
            console.error(error);
        });

        document.querySelector('form').addEventListener('submit', function() {
            if (theirMessageEditor) {
                document.getElementById('their_message').value = theirMessageEditor.getData();
            }
        });
    </script>
@endpush
