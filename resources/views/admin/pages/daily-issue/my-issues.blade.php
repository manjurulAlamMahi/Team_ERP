@extends('admin.master')

@section('title', 'My Issues')
@section('quickAccessicon', 'ri-alert-line')

@push('style')
    @include('admin.pages.daily-issue.partials._issue-card-styles')
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="ri-alert-line me-1"></i> My Issues
            <span class="badge bg-danger align-middle">{{ $issues->count() }}</span>
        </h5>
    </div>

    @php $authUser = Auth::user(); @endphp

    {{-- Filter bar --}}
    <div class="card card-body mb-3">
        <form method="GET" action="{{ route('daily.issue.my') }}" class="row gy-2 gx-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1 small text-muted">Filter</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Incomplete Issue</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed Issue</option>
                </select>
            </div>

            @if ($status === 'completed')
                <div class="col-auto">
                    <label class="form-label mb-1 small text-muted">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm"
                        value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                </div>
                <input type="hidden" name="status" value="completed">
            @endif

            <div class="col-auto">
                <label class="form-label mb-1 small text-muted">Issue Assigned By</label>
                <select name="created_by" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach ($creators as $creator)
                        <option value="{{ $creator->id }}" {{ (string) request('created_by') === (string) $creator->id ? 'selected' : '' }}>
                            {{ $creator->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-1 small text-muted">Issue Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                <a href="{{ route('daily.issue.my', $status === 'completed' ? ['status' => 'completed'] : []) }}" class="btn btn-sm btn-light">Reset</a>
            </div>
        </form>
    </div>

    @if ($status === 'completed')
        <p class="text-muted small mb-2">Showing issues you completed on <strong>{{ $date->format('d F Y') }}</strong>.</p>
    @endif

    @if ($issues->isEmpty())
        <div class="card card-body text-center text-muted py-5">
            <i class="ri-checkbox-circle-line fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0 fs-15">
                @if ($status === 'completed')
                    No issues completed on {{ $date->format('d F Y') }}.
                @else
                    No open issues assigned to you. Everything looks good!
                @endif
            </p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($issues as $issue)
                @if ($status === 'pending')
                    @include('admin.pages.daily-issue.partials._issue-grid-card')
                @else
                    @include('admin.pages.daily-issue.partials._issue-row-card')
                @endif
            @endforeach
        </div>
    @endif

    @include('admin.pages.daily-issue.partials._comments-modal')
@endsection

@push('script')
    <script>
        function markComplete(id) {
            $.ajax({
                url: "{{ route('daily.issue.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status) {
                        $('#issue-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
                        Toast.fire({ icon: 'success', title: response.message });
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                        location.reload();
                    }
                },
                error: function () {
                    Toast.fire({ icon: 'error', title: 'Something went wrong.' });
                    location.reload();
                }
            });
        }

        function deleteIssue(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete this issue?',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.destroy') }}",
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status) {
                                $('#issue-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Toast.fire({ icon: 'error', title: response.message });
                            }
                        }
                    });
                }
            });
        }

        function reverseIssue(id) {
            Swal.fire({
                title: 'Reverse to Not Completed',
                input: 'textarea',
                inputPlaceholder: 'Explain why this is not actually completed...',
                showCancelButton: true,
                confirmButtonText: 'Reverse',
                inputValidator: function (value) {
                    if (!value) return 'A comment is required to reverse this issue.';
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.reverse') }}",
                        type: 'POST',
                        data: { id: id, comment: result.value, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status) {
                                Toast.fire({ icon: 'success', title: response.message });
                                setTimeout(() => location.reload(), 900);
                            } else {
                                Toast.fire({ icon: 'error', title: response.message });
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.errors
                                ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                                : 'Unable to reverse issue.';
                            Toast.fire({ icon: 'error', title: msg });
                        }
                    });
                }
            });
        }
    </script>
@endpush
