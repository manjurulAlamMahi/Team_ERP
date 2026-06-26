@extends('admin.master')

@section('title', 'Client Message Detail')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    @include('admin.pages.client-message.partials._submission-header')

    <div class="row">
        <div class="col-lg-6">
            @include('admin.pages.client-message.partials._submission-info')
        </div>
        <div class="col-lg-6">
            @include('admin.pages.client-message.partials._submission-message')
        </div>
    </div>

    @include('admin.pages.client-message.partials._file-preview-modal')

    <div class="mt-3">
        <a href="{{ route('client.message.my.list') }}" class="btn btn-light">
            <i class="ri-arrow-left-line"></i> Back to My Messages
        </a>
    </div>
@endsection
