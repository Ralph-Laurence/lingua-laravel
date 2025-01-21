@php
    $sectionTitle = 'What learners say';

    if ($tutorDetails['averageRating'] < 1)
        $sectionTitle = 'Unrated';

@endphp
<div class="learner-reviews-wrapper mt-3">
    <h5 class="darker-text poppins-medium mb-4">{{ $sectionTitle }}</h5>
    <div class="row mx-auto my-3">
        <div class="col-3 ps-0">
            <h3 class="darker-text poppins-semibold mb-1">{{ $tutorDetails['averageRating'] }}</h3>
            @php
                // Decimal by default; We need to convert this to integer
                $starsGiven = intval($tutorDetails['averageRating']);
            @endphp
            <div class="flex-start gap-1 mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $starsGiven)
                        <i class="fas fa-star" style="color: #FFA30E;"></i>
                    @else
                        <i class="fas fa-star" style="color: #2F3333;"></i>
                    @endif
                @endfor
            </div>
            <div class="text-muted text-14">{{ $strTotalReviews }}</div>
        </div>
        <div class="col-9 gx-0">
            @php
                $minWidth = 'style="min-width: 30px;"';
                $maxValue = $tutorDetails['highestIndividualRating'];
            @endphp
            @foreach ($tutorDetails['totalIndividualRatings'] as $k => $v)
                @php
                    // Check for maxValue to avoid division by zero
                    $valueAmt = $maxValue > 0 ? ($v / $maxValue) * 100 : 0;
                    $valueStyle = 'style="width: '. $valueAmt .'%; background: #FA6127;"';
                @endphp
                <div class="ratings-meter d-flex align-items-center mb-1">
                    <p class="meter-legend-key text-center mb-0 text-14" {!! $minWidth !!}>{{ $k }}</p>
                    <div class="progress flex-fill" style="height: 8px;">
                        <div {!! $valueStyle !!} class="progress-bar" role="progressbar" aria-valuenow="{{ $v }}" aria-valuemin="0" aria-valuemax="{{ $maxValue }}"></div>
                    </div>
                    <p class="meter-legend-value text-center mb-0 text-14" {!! $minWidth !!}>(<span class="poppins-medium">{{ $v }}</span>)</p>
                </div>
            @endforeach
        </div>
    </div>
    @if(count($tutorDetails['ratingsAndReviews']) > 0)
        <div class="learner-reviews">
            @foreach ($tutorDetails['ratingsAndReviews'] as $k => $obj)
            @if (empty($obj['review']))
                {{-- We only need to display non-empty reviews --}}
                @continue
            @endif
            <div class="card review-card">
                <div class="card-body">
                    <div class="row mx-auto">
                        <div class="col-2 gx-0">
                            <img src="{{ $obj['learnerPhoto'] }}" alt="photo" class="border rounded-3 centered-image" width="40" height="40">
                        </div>
                        <div class="col-9">
                            <p class="darker-text text-14 poppins-medium mb-0 text-truncate">{{ $obj['learnerName'] }}</p>
                            <small class="text-muted text-12">{{ $obj['reviewDate'] }}</small>
                        </div>
                    </div>
                    <div class="learner-given-stars flex-start gap-1 text-14 p-2">
                        @php
                            // Decimal by default; We need to convert this to integer
                            $starsGiven = intval($obj['rating']);
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $starsGiven)
                                <i class="fas fa-star" style="color: #FFA30E;"></i>
                            @else
                                <i class="fas fa-star" style="color: #2F3333;"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="review-text text-14 my-2">{{ $obj['review'] }}</div>
                    <button tabindex="0" class="btn btn-link text-14 text-decoration-none p-0 popover-fullreview-toggle d-none" role="button" data-bs-toggle="popover" data-bs-trigger="focus" title="{{ $obj['learnerReviewName'] }} Review" data-bs-content="{{ $obj['review'] }}">Read More</button>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="w-100 my-4 text-muted text-center">
            This tutor haven't received any reviews yet.
        </div>
    @endif
</div>

@push('styles')
    <style>
        .review-card .review-text
        {
            height: 64px;
            font-size: 14px;
            line-height: 21px; /* Adjust line height to ensure proper spacing (16px font size * 1.5) */
            overflow: hidden;
            display: -webkit-box; /* Required for -webkit-line-clamp */
            -webkit-box-orient: vertical; /* Required for -webkit-line-clamp */
            -webkit-line-clamp: 3; /* Limit to 3 lines */
            white-space: normal; /* Ensure normal white-space behavior */
            text-overflow: ellipsis;
        }

        .review-card .popover-fullreview-toggle {
            color: #FA6127;
        }
        .review-card .popover-fullreview-toggle:focus {
            box-shadow: 0 0 0 .25rem rgba(250, 97, 39, .25);
        }
        .review-card .popover-fullreview-toggle:hover {
            color: #EF4444;
        }
    </style>
@endpush
