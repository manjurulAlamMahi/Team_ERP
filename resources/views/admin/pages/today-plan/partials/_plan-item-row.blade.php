<div class="row plan-item-row mb-3 align-items-end" data-index="{{ $index }}">
    <div class="col-md-3">
        <label class="form-label">Client Name</label>
        <input type="text" class="form-control" name="items[{{ $index }}][client_name]"
            value="{{ old('items.' . $index . '.client_name', $item['client_name'] ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Profile</label>
        <input type="text" class="form-control" name="items[{{ $index }}][profile_name]"
            value="{{ old('items.' . $index . '.profile_name', $item['profile_name'] ?? '') }}" required>
    </div>
    <div class="col-md-5">
        <label class="form-label">Plan Details</label>
        <textarea class="form-control" name="items[{{ $index }}][details]" rows="2"
            required>{{ old('items.' . $index . '.details', $item['details'] ?? '') }}</textarea>
    </div>
    <div class="col-md-1 text-end">
        <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove this plan">
            <i class="ri-delete-bin-2-line"></i>
        </button>
    </div>
</div>
