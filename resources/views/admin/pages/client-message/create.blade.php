@extends('admin.master')

@section('title', 'Send Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    <form action="{{ route('client.message.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-4">
                <div class="card border mb-3">
                    <div class="card-body">
                        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-file-list-3-line"></i> Message Type</h6>

                        <select id="typeSelect" name="client_message_type_id"
                            class="form-select @error('client_message_type_id') is-invalid @enderror" onchange="onTypeSelected()">
                            <option value="">Select a message type…</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" {{ (string) old('client_message_type_id', $selectedType?->id) === (string) $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_message_type_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                @include('admin.pages.client-message.partials._client-info-field', ['clients' => $clients])
            </div>
        </div>

        @include('admin.pages.client-message.partials._type-requirements-modal', ['types' => $types])

        <div id="messageFormSection" class="row" style="display: {{ old('client_message_type_id', $selectedType?->id) ? 'block' : 'none' }};">
            <div class="col-12">
                @include('admin.pages.client-message.partials._last-message-field')
            </div>
            <div class="col-12">
                @include('admin.pages.client-message.partials._message-sanitizer-field')
            </div>
            <div class="col-12">
                <div class="text-end mb-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('client.message.my.list') }}" class="btn btn-soft-secondary">
                        <i class="ri-close-line"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-send-plane-line"></i> Submit for Approval
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        function onTypeSelected() {
            var select = document.getElementById('typeSelect');
            var section = document.getElementById('messageFormSection');
            if (select.value) {
                section.style.display = 'block';
            }
            updateTypeRequirementsAlert();
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateTypeRequirementsAlert();
        });

        document.querySelector('form').addEventListener('submit', function () {
            if (typeof runFiverrSanitize === 'function') {
                runFiverrSanitize();
            }
        });
    </script>
@endpush
