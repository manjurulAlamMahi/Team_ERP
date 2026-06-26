<div class="mb-3">
    <label class="form-label">Responsible Person(s)</label>
    <div class="row">
        @foreach ($members as $member)
            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="responsible_ids[]" value="{{ $member->id }}"
                        id="responsible-{{ $member->id }}"
                        {{ in_array($member->id, $selectedIds ?? []) ? 'checked' : '' }}>
                    <label class="form-check-label" for="responsible-{{ $member->id }}">
                        {{ $member->name }}
                        <span class="text-muted small">({{ $member->getRoleNames()->first() }})</span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    @error('responsible_ids')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
