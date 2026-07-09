<div class="row plan-item-row mb-3 align-items-end" data-index="{{ $index }}">
    <div class="col-md-7">
        @include('admin.partials._client-select-field', [
            'clients' => $clients ?? collect(),
            'selected' => old('items.' . $index . '.client_id', $item['client_id'] ?? ''),
            'fieldId' => 'planClient' . $index,
            'inputName' => "items[{$index}][client_id]",
            'autoInit' => $index !== '__INDEX__',
        ])
    </div>
    <div class="col-md-4">
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
