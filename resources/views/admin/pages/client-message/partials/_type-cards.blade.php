@php
    $selectedTypeId = $selectedTypeId ?? null;
@endphp

<div class="row g-3 mb-4">
    @foreach ($types as $t)
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card type-card h-100 {{ (string) $selectedTypeId === (string) $t->id ? 'border-primary' : '' }}"
                data-id="{{ $t->id }}" onclick="selectType({{ $t->id }})" role="button" tabindex="0">
                <div class="card-body text-center p-2">
                    @if ($t->icon)
                        <img src="{{ asset($t->icon) }}" alt="" class="mb-2" style="height: 36px; width: 36px; object-fit: contain;">
                    @else
                        <i class="ri-file-list-3-line fs-3 text-primary mb-2 d-block"></i>
                    @endif
                    <h6 class="mb-1 small fw-semibold">{{ $t->name }}</h6>
                    <p class="text-muted small mb-0">{{ $t->short_description ?: 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('style')
    <style>
        .type-card {
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .type-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .type-card.border-primary {
            border-width: 2px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        function selectType(id) {
            document.querySelectorAll('.type-card').forEach(function(card) {
                card.classList.toggle('border-primary', String(card.dataset.id) === String(id));
            });

            var hidden = document.getElementById('typeSelect');
            if (hidden) {
                hidden.value = id;
            }

            var section = document.getElementById('messageFormSection');
            if (section && section.style.display === 'none') {
                section.style.display = 'block';
                if (window.jQuery) {
                    window.jQuery(window).trigger('resize');
                }
            }

            if (typeof showSpec === 'function') {
                showSpec(id);
            }
        }
    </script>
@endpush
