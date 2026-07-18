@extends('admin.master')

@section('title', 'Team Sheets')
@section('quickAccessicon', 'ri-file-excel-2-line')

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
                    <span><i class="ri-file-excel-2-line"></i> Team Sheets</span>
                    @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                        <a href="{{ route('team.sheet.create') }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> Add Sheet
                        </a>
                    @endif
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th>Added By</th>
                            <th>Date</th>
                            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sheets as $key => $sheet)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $sheet->title }}</td>
                                <td>
                                    <a href="{{ $sheet->link }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                        <i class="ri-external-link-line"></i> Open
                                    </a>
                                </td>
                                <td>{{ $sheet->creator->name ?? '-' }}</td>
                                <td>{{ $sheet->created_at->format('d M Y') }}</td>
                                @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                                    <td>
                                        <a href="{{ route('team.sheet.edit', $sheet->id) }}" class="text-reset fs-16 px-1">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteSheet({{ $sheet->id }})"
                                            class="text-reset fs-16 px-1">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                    </td>
                                @endif
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
        async function deleteSheet(id) {
            const result = await Swal.fire({
                icon: 'warning',
                title: "Are you sure you want to delete this sheet?",
                confirmButtonText: "Yes, delete it",
                showCancelButton: true,
                cancelButtonText: "No"
            });

            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('id', id);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('team.sheet.destroy') }}",
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
