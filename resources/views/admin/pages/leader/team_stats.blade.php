@extends('admin.master')

@section('title', 'Team Stats')
@section('quickAccessicon', 'ri-bar-chart-line')

@section('content')
    <div class="row">
        <div class="col-lg-10 m-auto">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card card-body text-center">
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small class="text-muted">Total Members</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body text-center">
                        <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body text-center">
                        <h3 class="mb-0 text-secondary">{{ $stats['inactive'] }}</h3>
                        <small class="text-muted">Inactive</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-body text-center">
                        <h3 class="mb-0 text-warning">{{ $stats['probation']->count() }}</h3>
                        <small class="text-muted">On Probation</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-body mb-3">
                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="ri-shield-user-line"></i> Members by Role</h5>
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($stats['by_role'] as $role => $count)
                                    <tr>
                                        <td>{{ $role }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-muted">No members yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body mb-3">
                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="ri-stack-line"></i> Members by Stack</h5>
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($stats['by_stack'] as $stack => $count)
                                    <tr>
                                        <td>{{ $stack }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-muted">No members yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2"><i class="ri-time-line"></i> Probation Members</h5>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Stack</th>
                            <th>Probation End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stats['probation'] as $entry)
                            <tr>
                                <td>{{ $entry['user']->name }}</td>
                                <td>{{ $entry['user']->stack->name ?? 'N/A' }}</td>
                                <td>{{ $entry['user']->probation_end_date }}</td>
                                <td>
                                    @if ($entry['overdue'])
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-info">In Probation</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No members on probation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
