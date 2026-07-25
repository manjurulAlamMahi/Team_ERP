@extends('admin.master')

@section('title', 'Leave / Attendance Log')
@section('quickAccessicon', 'ri-calendar-close-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-calendar-close-line"></i> Leave / Attendance Log</span>
                    <a href="{{ route('member.leave.create') }}" class="btn btn-sm btn-success">
                        <i class="ri-add-line"></i> Add Record
                    </a>
                </h5>
                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Member</th>
                            <th>Status</th>
                            <th>Date(s)</th>
                            <th>Reason</th>
                            <th>Recorded By</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($leaves as $key => $leave)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $leave->user->name ?? '-' }}</td>
                                <td><span class="badge {{ $leave->statusBadgeClass() }} rounded-pill">{{ $leave->statusLabel() }}</span></td>
                                <td>{{ $leave->dateRangeLabel() }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>{{ $leave->creator->name ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="javascript: void(0);" class="btn btn-sm btn-soft-primary copy-leave-btn" title="Copy"
                                            data-date="{{ $leave->start_date->isSameDay($leave->end_date) ? $leave->start_date->format('d F Y') : $leave->start_date->format('d F Y') . ' - ' . $leave->end_date->format('d F Y') }}"
                                            data-name="{{ $leave->user->name ?? '-' }}"
                                            data-empid="{{ $leave->user->employee_id ?? '-' }}"
                                            data-type="{{ $leave->statusLabel() }}"
                                            data-reason="{{ $leave->reason }}">
                                            <i class="ri-file-copy-2-line"></i>
                                        </a>
                                        <a href="{{ route('member.leave.edit', $leave->id) }}" class="btn btn-sm btn-soft-secondary" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteLeave({{ $leave->id }})"
                                            class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        function copyTextToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }

            return new Promise(function(resolve, reject) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try {
                    document.execCommand('copy');
                    resolve();
                } catch (err) {
                    reject(err);
                } finally {
                    document.body.removeChild(textarea);
                }
            });
        }

        $(document).on('click', '.copy-leave-btn', function() {
            const $btn = $(this);
            const text = $btn.data('date') + '\n' +
                $btn.data('name') + ' - ' + $btn.data('empid') + '\n' +
                $btn.data('type') + '\n' +
                'Reason : ' + $btn.data('reason');

            copyTextToClipboard(text).then(function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Copied to clipboard',
                });
            }).catch(function() {
                Toast.fire({
                    icon: 'error',
                    title: 'Could not copy to clipboard',
                });
            });
        });

        async function deleteLeave(id) {
            const result = await Swal.fire({
                icon: 'warning',
                title: "Are you sure you want to delete this record?",
                confirmButtonText: "Yes, delete it",
                showCancelButton: true,
                cancelButtonText: "No"
            });

            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('id', id);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('member.leave.destroy') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Something Went Wrong',
                        });
                    }
                });
            }
        }
    </script>
@endpush
