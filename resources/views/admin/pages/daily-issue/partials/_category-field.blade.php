@php
    $fieldId = $fieldId ?? 'category';
    $selected = $selected ?? '';
    $isCustom = $selected !== '' && !in_array($selected, $categories, true);
@endphp

<div class="mb-3">
    <label class="form-label">Issue Category <span class="text-danger">*</span></label>
    <select id="{{ $fieldId }}Select" class="form-select category-select @error('category') is-invalid @enderror">
        <option value="">Select Category</option>
        @foreach ($categories as $option)
            <option value="{{ $option }}" {{ ($isCustom && $option === 'Other') || $selected === $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <input type="text" id="{{ $fieldId }}Other"
        class="form-control mt-2 category-other {{ $isCustom ? '' : 'd-none' }}"
        placeholder="Enter custom category" value="{{ $isCustom ? $selected : '' }}">
    <input type="hidden" name="category" id="{{ $fieldId }}Hidden" value="{{ $selected }}">
    @error('category')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

@push('script')
    <script>
        function initCategoryField(fieldId) {
            var $select = $('#' + fieldId + 'Select');
            var $other = $('#' + fieldId + 'Other');
            var $hidden = $('#' + fieldId + 'Hidden');

            function sync() {
                if ($select.val() === 'Other') {
                    $other.removeClass('d-none');
                    $hidden.val($other.val());
                } else {
                    $other.addClass('d-none');
                    $hidden.val($select.val() || '');
                }
            }

            $select.on('change', sync);
            $other.on('input', function () { $hidden.val($(this).val()); });
            sync();
        }

        $(document).ready(function () {
            initCategoryField('{{ $fieldId }}');
        });
    </script>
@endpush
