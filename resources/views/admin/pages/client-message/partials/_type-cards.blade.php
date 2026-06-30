@php
    $selectedTypeId = $selectedTypeId ?? null;
@endphp

<div class="row g-2 mb-4" id="typeCardsRow">
    @foreach ($types as $t)
        <div class="col-auto">
            <div class="type-card {{ (string) $selectedTypeId === (string) $t->id ? 'is-active' : '' }}"
                data-id="{{ $t->id }}" onclick="selectType({{ $t->id }})" role="button" tabindex="0"
                title="{{ $t->name }}">
                @if ($t->icon)
                    <img src="{{ asset($t->icon) }}" alt="" class="type-card-icon" style="height:28px;width:28px;object-fit:contain;">
                @else
                    <i class="ri-file-list-3-line type-card-icon fs-18"></i>
                @endif
                <span class="type-card-label">{{ $t->name }}</span>
            </div>
        </div>
    @endforeach
</div>

@push('style')
    <style>
        .type-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px 14px;
            min-width: 90px;
            max-width: 110px;
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.17s, box-shadow 0.17s, transform 0.15s, background 0.15s;
            text-align: center;
            user-select: none;
        }

        .type-card:hover {
            border-color: #6c8ebf;
            box-shadow: 0 4px 16px rgba(67, 110, 201, 0.13);
            transform: translateY(-2px);
            background: #f0f5ff;
        }

        .type-card.is-active {
            border-color: #3d6bce;
            border-width: 2px;
            background: #eaf0ff;
            box-shadow: 0 4px 18px rgba(61, 107, 206, 0.18);
        }

        .type-card-icon {
            display: block;
            color: #4a6fa5;
        }

        .type-card.is-active .type-card-icon {
            color: #3d6bce;
        }

        .type-card-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #444;
            line-height: 1.3;
            word-break: break-word;
        }

        .type-card.is-active .type-card-label {
            color: #3d6bce;
        }
    </style>
@endpush

@push('script')
    <script>
        function selectType(id) {
            document.querySelectorAll('.type-card').forEach(function (card) {
                card.classList.toggle('is-active', String(card.dataset.id) === String(id));
            });

            var hidden = document.getElementById('typeSelect');
            if (hidden) hidden.value = id;

            var section = document.getElementById('messageFormSection');
            if (section && section.style.display === 'none') {
                section.style.display = 'block';
                if (window.jQuery) window.jQuery(window).trigger('resize');
            }

            if (typeof showSpec === 'function') showSpec(id);
        }
    </script>
@endpush
