@php $includeFooter = false; @endphp
@extends('shared.base-admin')
@section('title') Dashboard @endsection

@section('content')
<section class="container">

    <div class="row mb-3 counter-cards-container">
        <div class="col-3">
            <div class="card bg-primary">
                <div class="card-body text-white">
                    <div class="w-100 d-flex align-items-center">
                        <h6 class="flex-fill m-0">Total Members</h6>
                        <h5 class="fw-bold m-0">{{ $totals['totalMembers'] }}</h5>
                    </div>
                    <hr class="my-1">
                    <small class="flex-fill m-0 text-primary-accent text-12">*Sum of tutors and learners</small>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card bg-teal">
                <div class="card-body text-white">
                    <div class="w-100 d-flex align-items-center">
                        <h6 class="flex-fill m-0">Total Tutors</h6>
                        <h5 class="fw-bold m-0">{{ $totals['totalTutors'] }}</h5>
                    </div>
                    <hr class="my-1">
                    <small class="flex-fill m-0 text-teal-accent text-12">*All verified tutors</small>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card bg-orange">
                <div class="card-body text-white">
                    <div class="w-100 d-flex align-items-center">
                        <h6 class="flex-fill m-0">Total Learners</h6>
                        <h5 class="fw-bold m-0">{{ $totals['totalLearners'] }}</h5>
                    </div>
                    <hr class="my-1">
                    <small class="flex-fill text-orange-accent m-0 text-12">*All active learners</small>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card bg-danger">
                <div class="card-body text-white">
                    <div class="w-100 d-flex align-items-center">
                        <h6 class="flex-fill m-0">Pending Registrations</h6>
                        <h5 class="fw-bold m-0">{{ $totals['totalPending'] }}</h5>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex align-items-center gap-2">
                        <small class="flex-fill text-white fw-normal opacity-85 m-0 text-12">* Unapproved tutors</small>
                        @if (!empty($totals['totalPending']))
                            <a class="btn btn-sm btn-light text-12" href="{{ route('admin.dashboard.view-pending') }}">View</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-8 top-tutors-col">
            <div class="card">
                <div class="card-body">
                    <h6 id="chart-title">Top 5 tutors</h6>

                        @if (array_key_exists('topTutors', $totals) && !empty($totals['topTutors']))
                            <textarea id="chartData" class="d-none">
                                {{ $totals['topTutors'] }}
                            </textarea>
                        @endif

                    <div class="d-flex w-100">
                        <div class="container flex-fill">
                            <canvas id="topTutorsChart"></canvas>
                        </div>
                        <div class="photos d-flex align-items-center flex-column">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-2">
            <h6 id="chart-title" class="text-center">Tutor with most Learners</h6>
            <div class="card">
                <div class="card-body text-center">
                    <img class="mb-1" alt="photo" id="best-tutor-photo" width="80" height="80">
                    <p class="fw-bold my-1">
                        <i class="fas fa-award me-1 text-primary"></i>
                        <span id="best-tutor-name" class="text-14"></span>
                    </p>
                    <a id="best-tutor-details" class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Tutor</a>
                </div>
            </div>
        </div> --}}
        <div class="col-2">
            <h6 id="chart-title" class="text-center">Tutor with most Learners</h6>
            <div class="card">
                <div class="card-body text-center">
                    @if (isset($totals['topTutor']) && !empty($totals['topTutor']))
                        <img src="{{ $totals['topTutor']['tutorPhoto'] }}" class="mb-1" alt="photo" id="best-tutor-photo" width="80" height="80">
                        <p class="fw-bold my-1">
                            <i class="fas fa-medal me-1 text-primary"></i>
                            <span id="best-tutor-name" class="text-14">{{ $totals['topTutor']['tutorName'] }}</span>
                        </p>
                        <a href="{{ $totals['topTutor']['tutorDetails'] }}" class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Tutor</a>
                    @else
                        <div class="text-secondary text-14">
                            None yet
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-2">
            <h6 id="chart-title" class="text-center light">Learner with most Tutors</h6>
            <div class="card">
                <div class="card-body text-center">
                    @if (isset($totals['topLearner']) && !empty($totals['topLearner']))
                        <img src="{{ $totals['topLearner']['learnerPhoto'] }}" class="mb-1" alt="photo" id="best-learner-photo" width="80" height="80">
                        <p class="fw-bold my-1">
                            <i class="fas fa-star me-1 text-primary"></i>
                            <span id="best-tutor-name" class="text-14">{{ $totals['topLearner']['learnerName'] }}</span>
                        </p>
                        <a href="{{ $totals['topLearner']['learnerDetails'] }}" class="btn btn-sm btn-primary w-100 mt-2 text-13" role="button">About Learner</a>
                    @else
                        <div class="text-secondary text-14">
                            None yet
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
    <style>
        #chart-title {
            width: fit-content;
            padding: 4px 8px;
            border-radius: 8px;
            background: #2E244D;
            color: white;
            font-size: 13px;
        }
        #chart-title.light {
            background: #6c757d;
        }
        .text-primary-accent {
            color: #a2c3f5;
        }
        .bg-teal {
            background: #20A264;
        }
        .text-teal-accent {
            color: #97e7c1;
        }
        .bg-orange {
            background: #FF7701;
        }
        .text-orange-accent {
            color: #fcd5b4;
        }

        .photo-item {
            width: fit-content;
        }
        .photo-item img {
            height: 36px;
            width: 36px;
            border-radius: 4px;
            border: 1px solid #E5E5E5;
        }
        #best-tutor-photo,
        #best-learner-photo {
            width: 80px;
            height: 80px;
            border-radius: .25rem;
        }
        .counter-cards-container .card {
            max-height: 90px;
            height: 90px;
        }
    </style>
@endpush

@push('scripts')
<script src="{{ asset('assets/lib/chartjs/chart.umd.js') }}"></script>
<script>
    $(document).ready(function()
    {
        if (!document.getElementById('chartData'))
        {
            $('.top-tutors-col').addClass('d-none');
            return;
        }

        let chartData = $('#chartData').val().trim();

        if (!chartData)
        {
            $('.top-tutors-col').addClass('d-none');
            return;
        }

        chartData = JSON.parse(chartData);

        let chartProps = {
            'labels': [],
            'data': []
        };

        let contest = {};

        chartData.forEach((data, index) => {

            contest[data.totalLearners] = {
                'tutorName'     : data.tutorName,
                'tutorPhoto'    : data.tutorPhoto,
                'tutorDetails'  : data.tutorDetails
            };

            chartProps.labels.push(data.tutorName);
            chartProps.data.push(data.totalLearners);

            $('.photos').append(`<div class="photo-item d-block text-center">
                                    <img src="${data.tutorPhoto}" alt="photo">
                                    <p class="mb-1">
                                        <strong class="text-12 text-truncate">${data.tutorFname}</strong>
                                    </p>
                                </div>`);
        });

        // Find the tutor with most students
        // let keys = Object.keys(contest).map(Number);
        // let bestTutor_key = Math.max(...keys);
        // let bestTutor = contest[bestTutor_key];

        // $('#best-tutor-photo').attr('src', bestTutor.tutorPhoto);
        // $('#best-tutor-name').text(bestTutor.tutorName);
        // $('#best-tutor-details').attr('href', bestTutor.tutorDetails);

        // Display all top 5 tutors
        let ctx = document.getElementById('topTutorsChart').getContext('2d');
        let chartOptions = {
            type: 'bar',
            data: {
                labels: chartProps.labels,
                datasets: [{
                    label: "Tutor's Learners",
                    data: chartProps.data,
                    backgroundColor: [
                        '#6f42c1',
                        '#9F81D5',
                        '#6f42c1',
                        '#9F81D5',
                        '#6f42c1'
                    ],
                    barThickness: 16,
                    borderColor: [
                      '#6610f2',
                      '#9F6DD5',
                      '#6610f2',
                      '#9F6DD5',
                      '#6610f2',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    y: {
                        ticks: {
                            font: {
                                weight: 'bold', // Make x-axis labels bold,
                            },
                            color: '#2E244D'
                        }
                    },
                    x: {
                        beginAtZero: true
                    }
                }
            }
        };
        let topTutorsChart = new Chart(ctx, chartOptions);
    });
</script>
@endpush
