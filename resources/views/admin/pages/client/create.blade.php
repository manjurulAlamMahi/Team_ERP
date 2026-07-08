@extends('admin.master')

@section('title', 'Add Client')
@section('quickAccessicon', 'ri-team-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-team-line"></i> Add New Client
                </h5>

                <form action="{{ route('client.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username Client <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                name="username" value="{{ old('username') }}">
                            @error('username')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fiverr Profile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('profile') is-invalid @enderror"
                                name="profile" value="{{ old('profile') }}">
                            @error('profile')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                name="client_name" value="{{ old('client_name') }}">
                            @error('client_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select class="form-select @error('country') is-invalid @enderror" name="country">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" {{ old('country') === $country ? 'selected' : '' }}>
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
                                name="sales_man_name" value="{{ old('sales_man_name') }}">
                            @error('sales_man_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sales Man WhatsApp</label>
                            <input type="text" class="form-control @error('sales_man_whatsapp') is-invalid @enderror"
                                name="sales_man_whatsapp" value="{{ old('sales_man_whatsapp') }}">
                            @error('sales_man_whatsapp')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Add Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
