@extends('admin.master')

@section('title', 'Create Issue')
@section('quickAccessicon', 'ri-alert-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alert-line"></i> Create Issue
                </h5>
                <form id="createIssueForm" action="{{ route('daily.issue.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control bg-light" value="{{ today()->format('d F Y') }}" readonly>
                    </div>

                    @include('admin.partials._client-select-field', [
                        'clients' => $clients,
                        'selected' => old('client_id'),
                        'fieldId' => 'issueClient',
                    ])

                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-muted small">(Optional)</span></label>
                        <textarea class="form-control @error('issue') is-invalid @enderror" name="issue"
                            rows="4">{{ old('issue') }}</textarea>
                        @error('issue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Issue Type</label>
                        @foreach ($types as $type)
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="type-{{ $type }}"
                                    value="{{ $type }}" {{ old('type', 'Normal') === $type ? 'checked' : '' }}>
                                <label class="form-check-label" for="type-{{ $type }}">{{ $type }}</label>
                            </div>
                        @endforeach
                        @error('type')
                            <div class="text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.pages.daily-issue.partials._category-field', [
                        'categories' => $categories,
                        'selected' => old('category'),
                        'fieldId' => 'createCategory',
                    ])

                    @include('admin.pages.daily-issue.partials._responsible-checklist', [
                        'members' => $members,
                        'selectedIds' => old('responsible_ids', []),
                    ])

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-send-plane-line"></i> Create Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('submit', '#createIssueForm', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');

            $form.find('.is-invalid').removeClass('is-invalid');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function (response) {
                    if (response.status) {
                        Toast.fire({ icon: 'success', title: response.message });
                        setTimeout(function () {
                            window.location.href = response.data.redirect;
                        }, 800);
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                        $btn.prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                    Object.keys(errors).forEach(function (field) {
                        $form.find('[name="' + field + '"]').addClass('is-invalid');
                    });
                    const message = Object.values(errors).length
                        ? Object.values(errors).flat().join(' ')
                        : 'Unable to create issue.';
                    Toast.fire({ icon: 'error', title: message });
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
