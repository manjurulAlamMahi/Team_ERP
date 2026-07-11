@php
    $rowProfileId = old('clients.' . $index . '.profile_id', $row['profile_id'] ?? '');
@endphp
<div class="row bulk-client-row mb-2 align-items-start" data-index="{{ $index }}">
    <div class="col-md-6">
        <input type="text"
            class="form-control @error('clients.' . $index . '.username') is-invalid @enderror"
            name="clients[{{ $index }}][username]"
            placeholder="Username Client"
            value="{{ old('clients.' . $index . '.username', $row['username'] ?? '') }}">
        @error('clients.' . $index . '.username')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-5">
        <select class="form-select bulk-profile-select @error('clients.' . $index . '.profile_id') is-invalid @enderror"
            name="clients[{{ $index }}][profile_id]">
            <option value="">Select Profile</option>
            @foreach ($profiles as $profile)
                <option value="{{ $profile->id }}" {{ (string) $rowProfileId === (string) $profile->id ? 'selected' : '' }}>
                    {{ $profile->name }}
                </option>
            @endforeach
        </select>
        @error('clients.' . $index . '.profile_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-1 text-end">
        <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove this row">
            <i class="ri-delete-bin-2-line"></i>
        </button>
    </div>
</div>
