@extends('admin.master')

@section('title', 'Client Message Review History')
@section('quickAccessicon', 'ri-customer-service-2-line')

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
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-customer-service-2-line"></i> {{ $team->name }} - Review History
                </h5>
                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Submitted By</th>
                            <th>Type</th>
                            <th>Client Name</th>
                            <th>Status</th>
                            <th>Reviewed By</th>
                            <th>Reviewed At</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $item)
                            @php
                                $statusColor = $item->status === 'approved' ? 'success' : 'danger';
                            @endphp
                            <tr>
                                <td>{{ $item->submitter->name ?? 'N/A' }}</td>
                                <td>{{ $item->type->name ?? 'N/A' }}</td>
                                <td>{{ $item->client_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} rounded-pill text-uppercase">{{ $item->status }}</span>
                                    @if ($item->status === 'rejected' && $item->rejection_reason)
                                        <a href="javascript:void(0)" class="btn btn-sm btn-soft-danger reason-trigger ms-1" title="View Reason"
                                            data-reason="{{ $item->rejection_reason }}">
                                            <i class="ri-information-line"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $item->reviewer->name ?? 'N/A' }}</td>
                                <td>{{ $item->reviewed_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('client.message.review.show', $item->id) }}" class="btn btn-sm btn-soft-primary" title="View">
                                            <i class="ri-eye-line"></i>
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

    @include('admin.pages.client-message.partials._reason-modal')
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>
@endpush
