<!-- Quick Access Model -->
<div class="modal fade" id="bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mySmallModalLabel">Add To Quick Access</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('add.quick.access') }}" method="POST">
                    @csrf
                    <input hidden type="text" class="form-control" value="" name="icon" id="icon-input">
                    <input hidden type="text" class="form-control" value="{{ Route::currentRouteName() }}"
                        name="route">

                    <div class="mb-3">
                        <label for="" class="form-label">URL</label>
                        <input readonly type="text" class="form-control" value="{{ url()->current() }}"
                            name="url">
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" value="" name="name">
                    </div>

                    <div class="text-end">
                        <button class="btn btn-success btn-sm">ADD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
