@extends('admin.master')

@section('title', 'Client Message Detail')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-customer-service-2-line"></i> Submission
                </h5>
                @include('admin.pages.client-message.partials._submission')
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-information-line"></i> Type Requirements
                </h5>
                @include('admin.pages.client-message.partials._spec', ['type' => $clientMessage->type])
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('client.message.my.list') }}" class="btn btn-light">
            <i class="ri-arrow-left-line"></i> Back to My Messages
        </a>
    </div>
@endsection
