@push('styles')
    <style>
        #slava-ukraini {
            background: #141414;
            color: white;
            height: 64px;
            position: relative;
        }

        #ukr-flag {
            border-radius: 3px;
        }

        #stand-with-ukr {
            font-family: 'Poppins-SemiBold';
            font-size: 13px;
        }

        #btn-ukr-close {
            background: #141414;
            border: none;
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
        }
        #btn-ukr-close:hover {
            background: #1c1b23;
        }
    </style>
@endpush

<header id="slava-ukraini" class="flex-center p-3">
    <div class="flex-center gap-3">
        <img id="ukr-flag" src="{{ asset('assets/img/ukraine.png') }}" alt="ukraine flag" height="24">
        <span id="stand-with-ukr">SignLingua stands with Ukraine and its people</span>
    </div>
    <button class="btn btn-dark" id="btn-ukr-close">
        <i class="fas fa-times text-white"></i>
    </button>
</header>
