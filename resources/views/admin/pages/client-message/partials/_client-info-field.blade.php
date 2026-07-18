@php
    $clientMessage = $clientMessage ?? null;
@endphp

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-user-search-line"></i> Client Info</h6>

        @include('admin.partials._client-select-field', [
            'clients' => $clients,
            'selected' => old('client_id', $clientMessage->client_id ?? ''),
            'fieldId' => 'clientMessageClient',
        ])
    </div>
</div>
