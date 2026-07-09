@extends('admin.master')

@section('title', "Submit Today's Plan")
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-10 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-todo-line"></i> Submit Today's Plan
                </h5>
                <p class="text-muted">Date: <strong>{{ today()->format('Y-m-d') }}</strong> (automatically set, cannot be
                    changed)</p>

                <form action="{{ route('today.plan.store') }}" method="POST">
                    @csrf
                    <div id="planRows">
                        @php
                            $items = old('items', [['client_name' => '', 'profile_name' => '', 'details' => '']]);
                        @endphp
                        @foreach ($items as $index => $item)
                            @include('admin.pages.today-plan.partials._plan-item-row', ['index' => $index, 'item' => $item])
                        @endforeach
                    </div>

                    <template id="rowTemplate">
                        @include('admin.pages.today-plan.partials._plan-item-row', ['index' => '__INDEX__', 'item' => null])
                    </template>

                    <div class="mb-3">
                        <button type="button" id="addRowBtn" class="btn btn-light btn-sm">
                            <i class="ri-add-line"></i> Add Another Plan
                        </button>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Submit for Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let rowIndex = document.querySelectorAll('#planRows .plan-item-row').length;
        const rowTemplate = document.getElementById('rowTemplate');

        document.getElementById('addRowBtn').addEventListener('click', function() {
            const html = rowTemplate.innerHTML.replace(/__INDEX__/g, rowIndex);
            document.getElementById('planRows').insertAdjacentHTML('beforeend', html);
            window.clientSelectFields = window.clientSelectFields || {};
            window.clientSelectFields['planClient' + rowIndex] = initClientSelectField('planClient' + rowIndex);
            rowIndex++;
        });

        document.getElementById('planRows').addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-row');
            if (!btn) return;
            const rows = document.querySelectorAll('#planRows .plan-item-row');
            if (rows.length > 1) {
                btn.closest('.plan-item-row').remove();
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: 'At least one plan is required.'
                });
            }
        });
    </script>
@endpush
