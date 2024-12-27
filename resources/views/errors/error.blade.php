<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>

    <!-- FRAMEWORKS, LIBRARIES -->
    <link rel="shortcut icon" href="{{ asset('asset/img/logo-s.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/lib/bootstrap5/bootstrap.min.css')}}">

    <!-- MAIN STYLES -->
    <link rel="stylesheet" href="{{ asset('assets/css/root.css') }}">
    <style>
        body {
            background-color: #FAFBFD;
        }

        h6 {
            font-family: 'Poppins-Medium';
        }
        p {
            color: #868A8E;
            font-size: 14px;
        }
        a.btn-go-back {
            background-color: var(--accent-dark);
            color: white;
            border: none;
            border-radius: 1rem;
        }
        a.btn-go-back:hover {
            background-color: var(--accent-tertiary);
        }
        a.btn-go-back:active {
            background-color: var(--accent-secondary);
        }
        .card {
            border-radius: .75rem;
        }
    </style>
</head>

<body>
    <div class="flex-center w-100 h-100">
        <div class="card shadow-sm p-4">
            <div class="card-body text-center">
                {{-- @if (!empty($errCode))

                    @switch($errCode)
                        @case(404)
                            @include('errors.404')
                            @break

                        @case(500)
                            @include('errors.500')
                            @break

                        @default

                    @endswitch
                @endif --}}
                @yield('content')
                <a href="/" class="btn btn-primary btn-go-back shadow">Back to Safety</a>
            </div>
        </div>

    </div>
</body>

</html>
