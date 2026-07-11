@php
    $clients = $clients ?? collect();
    $selected = $selected ?? '';
    $fieldId = $fieldId ?? 'client';
    $inputName = $inputName ?? 'client_id';
    $disabled = $disabled ?? false;
    $autoInit = $autoInit ?? true;
    $hasClients = $clients->isNotEmpty();
    $emptyMessage = $emptyMessage ?? 'No clients found for your team yet.';
@endphp

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Client <span class="text-danger">*</span></label>
        <select id="{{ $fieldId }}Select" name="{{ $inputName }}"
            class="form-select client-select-field @error('client_id') is-invalid @enderror"
            {{ !$hasClients || $disabled ? 'disabled' : '' }}>
            <option value="">{{ $hasClients ? 'Select Client' : $emptyMessage }}</option>
            @foreach ($clients as $client)
                @php $profileName = $client->profile->name ?? 'N/A'; @endphp
                <option value="{{ $client->id }}" data-profile="{{ $profileName }}"
                    {{ (string) $selected === (string) $client->id ? 'selected' : '' }}>
                    {{ $client->username . ' - ' . $profileName }}
                </option>
            @endforeach
        </select>
        @if (!$hasClients)
            <div class="form-text text-danger" id="{{ $fieldId }}EmptyHint">{{ $emptyMessage }}</div>
        @endif
        @error('client_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Profile</label>
        <input type="text" id="{{ $fieldId }}Profile" class="form-control" readonly
            placeholder="Auto-filled from selected client">
    </div>
</div>

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/css/select2-theme.css" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/select2/js/select2.min.js"></script>
@endpush

@once
    @push('script')
        <script>
            function initClientSelectField(fieldId, dropdownParent) {
                var $select = $('#' + fieldId + 'Select');
                var $profile = $('#' + fieldId + 'Profile');

                $select.select2({
                    width: '100%',
                    placeholder: $select.find('option').first().text(),
                    dropdownParent: dropdownParent ? $(dropdownParent) : undefined,
                });

                function sync() {
                    var profile = $select.find('option:selected').data('profile');
                    $profile.val(profile || '');
                }

                $select.on('change', sync);
                sync();

                return { select: $select, profile: $profile };
            }

            function setClientSelectOptions(field, clients, emptyMessage) {
                var $select = field.select;
                $select.val(null).empty();

                if (!clients.length) {
                    $select.append(new Option(emptyMessage || 'No clients found for your team.', '', true, true));
                    $select.prop('disabled', true);
                } else {
                    $select.append(new Option('Select Client', '', true, true));
                    clients.forEach(function (client) {
                        var option = new Option(client.label, client.id, false, false);
                        $(option).attr('data-profile', client.profile);
                        $select.append(option);
                    });
                    $select.prop('disabled', false);
                }

                $select.trigger('change');
                field.profile.val('');
            }
        </script>
    @endpush
@endonce

@if ($autoInit)
    @push('script')
        <script>
            $(document).ready(function () {
                window.clientSelectFields = window.clientSelectFields || {};
                window.clientSelectFields['{{ $fieldId }}'] = initClientSelectField('{{ $fieldId }}');
            });
        </script>
    @endpush
@endif
