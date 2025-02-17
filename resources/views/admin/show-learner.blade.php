@php $includeFooter = false; @endphp
@extends('shared.base-admin')
@section('content')
{{-- @dd($learnerDetails) --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tutor-details.css') }}">
@endpush

    <section class="px-2 pt-2 pb-3 mx-auto w-50">
        <div class="flex-start gap-2">
            <a role="button" href="{{ URL::previous() }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Back
            </a>
            <h6 class="mb-0">Learners / Details <span class="text-secondary">/ {{ $learnerDetails['fullname'] }}</span></h6>
        </div>
    </section>

    <section class="px-2 mt-3 mx-auto w-50">
        <div class="card">
            <div class="card-body d-flex">
                <div class="profile-photo-wrapper">
                    <img src="{{ $learnerDetails['photo'] }}" alt="profile-photo" height="160">
                </div>
                <div class="profile-captions ps-4">
                    <div class="tutor-name flex-start gap-2 mb-2">
                        <h4 class="mb-0 darker-text">{{ $learnerDetails['fullname'] }}</h4>
                    </div>
                    @if (!empty($learnerDetails['disabilityBadge']))
                    <div class="tutor-badges flex-start mb-2">
                        <span data-bs-toggle="tooltip" title="{{ $learnerDetails['disabilityDesc'] }}" class="badge awareness_badge disability-tooltip {{ $learnerDetails['disabilityBadge'] }}">{{  $learnerDetails['disability'] }}</span>
                    </div>
                    <hr class="border-light border-1">
                    @endif
                    <div class="tutor-address text-secondary mb-2">
                        <i class="fas fa-location-dot me-2"></i>
                        {{ $learnerDetails['address'] }}
                    </div>
                    <div class="tutor-email text-secondary mb-2">
                        <i class="fas fa-at me-2"></i>
                        {{ $learnerDetails['email'] }}
                    </div>
                    <div class="tutor-contact text-secondary mb-3">
                        <i class="fas fa-phone me-2"></i>
                        {{ $learnerDetails['contact'] }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
