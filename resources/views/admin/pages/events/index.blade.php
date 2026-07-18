@extends('admin.master')

@section('title', 'Events')
@section('quickAccessicon', 'ri-calendar-event-line')

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <form id="STORE_EVENTS" action="{{ route('events.store') }}" method="POST">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-calendar-event-line me-1"></i> Events
                    </h5>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Start Date</label>
                        <div class="col-9">
                            <input type="date"
                                class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                                name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">End Date</label>
                        <div class="col-9">
                            <input type="date"
                                class="form-control form-control-sm @error('end_date') is-invalid @enderror" name="end_date"
                                value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Message</label>
                        <div class="col-9">
                            <textarea name="message" class="form-control form-control-sm @error('message') is-invalid @enderror" rows="4">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p class="text-info">
                        <strong>Note:</strong> The event date will only store the month and day. The year does not matter.
                    </p>
                    <div class="row" style="align-items: end">
                        <div class="text-end">
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Create
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <form id="UPDATE_EVENTS" style="display: none;" action="{{ route('events.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-calendar-event-line me-1"></i> Events
                    </h5>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Start Date</label>
                        <div class="col-9">
                            <input type="date"
                                class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                                name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">End Date</label>
                        <div class="col-9">
                            <input type="date"
                                class="form-control form-control-sm @error('end_date') is-invalid @enderror" name="end_date"
                                value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Message</label>
                        <div class="col-9">
                            <textarea name="message" class="form-control form-control-sm @error('message') is-invalid @enderror" rows="4">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <p class="text-info">
                        <strong>Note:</strong> The event date will only store the month and day. The year does not matter.
                    </p>
                    <div class="row" style="align-items: end">
                        <div class="text-end">
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-8">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-event-fill"></i> Events List
                </h5>

                <table class="table table-bordered table-centered">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Event Start Date</th>
                            <th>Event End Date</th>
                            <th>Event Message</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $key => $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ Carbon\Carbon::parse($item->start_date)->format('M-d') }}</td>
                                <td>{{ Carbon\Carbon::parse($item->end_date)->format('M-d') }}</td>
                                <td>{{ $item->message }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="javascript: void(0);" onclick="editEvent({{ $item->id }})"
                                            class="btn btn-sm btn-soft-secondary" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteRow({{ $item->id }})"
                                            class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No Events Found</td>
                            </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- Edit --}}
    <script>
        function editEvent(id) {
            $.ajax({
                url: "{{ route('events.get') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response) {
                        $('#UPDATE_EVENTS').show();
                        $('#STORE_EVENTS').hide();
                        $('#UPDATE_EVENTS input[name="id"]').val(response[0].id);
                        $('#UPDATE_EVENTS input[name="name"]').val(response[0].name);
                        $('#UPDATE_EVENTS input[name="start_date"]').val(response[0].start_date);
                        $('#UPDATE_EVENTS input[name="end_date"]').val(response[0].end_date);
                        $('#UPDATE_EVENTS textarea[name="message"]').val(response[0].message);

                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

<script>
    async function deleteRow(id) {
        const {
            value: password
        } = await Swal.fire({
            icon: 'info',
            title: "Are you sure you want to delete this account?",
            input: "password",
            inputLabel: "Enter your password",
            inputPlaceholder: "Enter your password",
            inputAttributes: {
                maxlength: "100",
                autocapitalize: "off",
                autocorrect: "off"
            },
            confirmButtonText: "Yes",
            showCancelButton: true,
            cancelButtonText: "No"
        });

        if (password) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('password', password);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('events.destroy') }}",
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
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) { // Handle incorrect password case
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Incorrect Password',
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something Went Wrong',
                        });
                    }
                }
            });
        }
    }
</script>
@endpush
