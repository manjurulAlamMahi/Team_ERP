@php
    $planDetailsOptions = [
        'API Development', 'API Integration', 'Backend Development', 'Frontend Development',
        'Mobile App Development', 'Database Design', 'UI Design', 'UX Improvement',
        'Project Bug Fix', 'Project Fixing', 'Project Analysis', 'Requirement Analysis',
        'Code Review', 'QA Review', 'Server Setup', 'App Deployment', 'Hosting Configuration',
        'Deployment', 'Client Management', 'Client Meeting', 'Other',
    ];
    $fieldId = $fieldId ?? 'planDetails';
    $selected = $selected ?? '';
    $autoInit = $autoInit ?? true;
    $isCustom = $selected !== '' && !in_array($selected, $planDetailsOptions, true);
@endphp

<div class="mb-3">
    <label class="form-label fw-medium">Plan Details <span class="text-danger">*</span></label>
    <select id="{{ $fieldId }}Select" class="form-select plan-details-select @error('plan_details') is-invalid @enderror">
        <option value="">— Select Plan Details —</option>
        @foreach ($planDetailsOptions as $option)
            <option value="{{ $option }}" {{ ($isCustom && $option === 'Other') || $selected === $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <input type="text" id="{{ $fieldId }}Other"
        class="form-control mt-2 plan-details-other {{ $isCustom ? '' : 'd-none' }}"
        placeholder="Enter custom plan details" value="{{ $isCustom ? $selected : '' }}">
    <input type="hidden" name="plan_details" id="{{ $fieldId }}Hidden" value="{{ $selected }}">
    @error('plan_details')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/select2/js/select2.min.js"></script>
    <script>
        function initPlanDetailsField(fieldId, dropdownParent) {
            var $select = $('#' + fieldId + 'Select');
            var $other = $('#' + fieldId + 'Other');
            var $hidden = $('#' + fieldId + 'Hidden');

            $select.select2({
                width: '100%',
                placeholder: '— Select Plan Details —',
                dropdownParent: dropdownParent ? $(dropdownParent) : undefined,
            });

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

            return { select: $select, other: $other, hidden: $hidden, sync: sync };
        }

        function setPlanDetailsValue(field, value) {
            var isKnown = !!value && field.select.find('option').filter(function () {
                return this.value === value;
            }).length > 0;

            if (isKnown) {
                field.other.val('');
                field.select.val(value).trigger('change');
            } else {
                field.other.val(value || '');
                field.select.val('Other').trigger('change');
            }
        }

        @if ($autoInit)
            $(document).ready(function () {
                initPlanDetailsField('{{ $fieldId }}');
            });
        @endif
    </script>
@endpush
