@extends('admin.master')

@section('title', 'Edit Client')
@section('quickAccessicon', 'ri-team-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/css/select2-theme.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-team-line"></i> Edit Client
                </h5>

                <form action="{{ route('client.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $client->id }}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" value="{{ old('username', $client->username) }}">
                            @error('username')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fiverr Profile <span class="text-danger">*</span></label>
                            <select class="form-select select2-field @error('profile_id') is-invalid @enderror" name="profile_id">
                                <option value="">Select Profile</option>
                                @foreach ($profiles as $profile)
                                    <option value="{{ $profile->id }}" {{ (string) old('profile_id', $client->profile_id) === (string) $profile->id ? 'selected' : '' }}>
                                        {{ $profile->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('profile_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                name="client_name" value="{{ old('client_name', $client->client_name) }}">
                            @error('client_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select class="form-select select2-field @error('country') is-invalid @enderror" name="country">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}"
                                        {{ old('country', $client->country) === $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Sales Man Name</label>
                            <input type="text" class="form-control @error('sales_man_name') is-invalid @enderror"
                                name="sales_man_name" value="{{ old('sales_man_name', $client->sales_man_name) }}">
                            @error('sales_man_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sales Man WhatsApp</label>
                            <input type="text" class="form-control @error('sales_man_whatsapp') is-invalid @enderror"
                                name="sales_man_whatsapp"
                                value="{{ old('sales_man_whatsapp', $client->sales_man_whatsapp) }}">
                            @error('sales_man_whatsapp')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end d-flex justify-content-end gap-2">
                        <a href="{{ route('client.list') }}" class="btn btn-soft-secondary">
                            <i class="ri-close-line"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Update Client
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
            $('.select2-field').select2({ width: '100%' });
        });
    </script>
@endpush
