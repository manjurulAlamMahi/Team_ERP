@extends('admin.master')

@section('title', 'Add Multiple Clients')
@section('quickAccessicon', 'ri-team-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/css/select2-theme.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-9 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-team-line"></i> Add Multiple Clients
                </h5>
                <p class="text-muted mb-3">
                    Pick a default profile to quickly add several clients under it (e.g. XX, YY, TT, OO all under
                    one profile), or change the profile on individual rows to add clients across different
                    profiles at once.
                </p>

                <div class="mb-3">
                    <label class="form-label">Default Profile</label>
                    <select id="defaultProfile" class="form-select select2-field">
                        <option value="">Select a profile to prefill new rows</option>
                        @foreach ($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                        @endforeach
                    </select>
                </div>

                <form action="{{ route('client.bulk.store') }}" method="POST">
                    @csrf

                    <div class="row mb-1">
                        <div class="col-md-6"><label class="form-label small text-muted mb-0">Username Client</label></div>
                        <div class="col-md-5"><label class="form-label small text-muted mb-0">Fiverr Profile</label></div>
                    </div>

                    <div id="clientRows">
                        @php
                            $rows = old('clients', [['username' => '', 'profile_id' => ''], ['username' => '', 'profile_id' => ''], ['username' => '', 'profile_id' => '']]);
                        @endphp
                        @foreach ($rows as $index => $row)
                            @include('admin.pages.client.partials._bulk-client-row', ['index' => $index, 'row' => $row, 'profiles' => $profiles])
                        @endforeach
                    </div>

                    <template id="rowTemplate">
                        @include('admin.pages.client.partials._bulk-client-row', ['index' => '__INDEX__', 'row' => null, 'profiles' => $profiles])
                    </template>

                    <div class="mb-3">
                        <button type="button" id="addRowBtn" class="btn btn-light btn-sm">
                            <i class="ri-add-line"></i> Add Another Client
                        </button>
                    </div>

                    <div class="text-end d-flex justify-content-end gap-2">
                        <a href="{{ route('client.list') }}" class="btn btn-soft-secondary">
                            <i class="ri-close-line"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Add All Clients
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/select2/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#defaultProfile').select2({ width: '100%' });
            $('.bulk-profile-select').select2({ width: '100%' });
        });

        let rowIndex = document.querySelectorAll('#clientRows .bulk-client-row').length;
        const rowTemplate = document.getElementById('rowTemplate');

        document.getElementById('addRowBtn').addEventListener('click', function () {
            const html = rowTemplate.innerHTML.replace(/__INDEX__/g, rowIndex);
            document.getElementById('clientRows').insertAdjacentHTML('beforeend', html);

            const $newSelect = $('#clientRows .bulk-client-row[data-index="' + rowIndex + '"] .bulk-profile-select');
            $newSelect.select2({ width: '100%' });

            const defaultProfile = $('#defaultProfile').val();
            if (defaultProfile) {
                $newSelect.val(defaultProfile).trigger('change');
            }

            rowIndex++;
        });

        document.getElementById('clientRows').addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-row');
            if (!btn) return;
            const rows = document.querySelectorAll('#clientRows .bulk-client-row');
            if (rows.length > 1) {
                btn.closest('.bulk-client-row').remove();
            } else {
                Toast.fire({ icon: 'warning', title: 'At least one client row is required.' });
            }
        });
    </script>
@endpush
