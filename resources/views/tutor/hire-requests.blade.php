@php $includeFooter = false; @endphp
@extends('shared.base-members')

@push('dialogs')
    @include('partials.messagebox')
    @include('partials.learner-popover')
@endpush

@section('content')
    <section class="container">

        @if ($hireRequests->count() < 1)
        <div class="d-flex flex-column align-items-center gap-3 mt-5">
            <p class="text-center text-secondary">Hmm... Looks like you don't have any hire requests yet.</p>
            <a role="button" href="{{ route('tutor.find-learners') }}" class="btn btn-outline-secondary">
                <span class="me-2">Find Learners</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @else
        <div class="hire-reqs-list-view py-4 d-flex flex-wrap gap-3">
            @foreach ($hireRequests as $obj)
                <div class="card shadow-sm hire-req-item-card">
                    <div class="card-body p-4 d-flex flex-column align-items-center">
                        <div class="photo-container mb-2">
                            <img class="learner-photo centered-image" src="{{ $obj['photo'] }}"/>
                        </div>
                        <div class="learner-name w-100 mb-1">
                            <div class="text-truncate text-center">{{ $obj['name']}}</div>
                        </div>
                        <button type="button" data-learner-id="{{ $obj['user_id'] }}" class="btn btn-sm btn-outline-secondary btn-details-popover w-100 text-12">See Profile</button>
                        <div class="d-flex w-100 align-items-center pt-2 gap-2">
                            <button type="button" data-learner-id="{{ $obj['user_id'] }}" class="btn btn-sm btn-outline-secondary flex-fill text-12 btn-decline-request">Decline</button>
                            <button type="button" data-learner-id="{{ $obj['user_id'] }}" class="btn btn-sm btn-primary flex-fill text-12 btn-accept-request action-button">Accept</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $hireRequests->links() }}
        @endif
    </section>
    <form action="{{ route('tutor.accept-hire-request') }}" method="post" class="d-none" id="frm-accept-request">
        @csrf
        <input type="hidden" name="learner-id" id="learner-id">
    </form>
    <form action="{{ route('tutor.decline-hire-request') }}" method="post" class="d-none" id="frm-decline-request">
        @csrf
        <input type="hidden" name="learner-id" id="learner-id">
    </form>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/fontawesome6.7.2/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tutor-workspace.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/breadcrumb.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script src="{{ asset('assets/js/shared/fetch-learner-details.js') }}"></script>
    <script src="{{ asset('assets/js/tutor-hire-requests.js') }}"></script>
@endpush

@push('styles')
    <style>
        .hire-req-item-card
        {
            width: fit-content;
            min-width: 220px;
            max-width: 220px;
            font-family: 'Poppins';
        }
        .hire-req-item-card .card-body {
            overflow: hidden;
        }
        .hire-req-item-card .photo-container {
            width: 170px;
            height: 170px;
        }
        .hire-req-item-card .learner-photo {
            width: 170px;
            height: 170px;
            border-radius: 3px;
            margin-left: 0;
            margin-right: 0;
        }
        .hire-req-item-card .learner-name {
            font-size: 13px;
            font-family: 'Poppins-Medium';
        }
    </style>
@endpush

@push('dialogs')
    <x-toast-container>
        @if (session('booking_request_action'))
            @php
                $action = session('booking_request_action');
                $message = '';
                $title = '';

                if ($action == 'accept')
                {
                    $message = session('accept_booking_request');
                    $title   = 'Request Accepted';
                }
                else
                {
                    $message = session('decline_booking_request');
                    $title   = 'Request Declined';
                }
            @endphp
            @include('partials.toast', [
                'toastMessage'  => $message,
                'toastTitle'    => $title,
                'autoClose'     => 'true'
            ])
        @endif
    </x-toast-container>
@endpush
