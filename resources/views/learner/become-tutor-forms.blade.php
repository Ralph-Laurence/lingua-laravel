@php $includeFooter = false; @endphp
@extends('shared.base-members')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/lib/katex0.16.9/css/katex.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/quilljs2.0.3/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/fontawesome6.7.2/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/flagicons7.2.3/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lib/maxlength/maxlength.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/become-tutor-forms.css') }}">
@endpush()

@push('dialogs')
    @include('partials.messagebox')
@endpush

@section('content')
    <form method="post" id="main-form" novalidate
          action="{{ route('become-tutor.forms.submit') }}"
          enctype="multipart/form-data"
          class="needs-validation @if($errors->any()) was-validated @endif">

        <div id="form-carousel" class="carousel slide" data-interval="false">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    @include('learner.become-tutor-forms-step1')
                </div>
                <div class="carousel-item">
                    @include('learner.become-tutor-forms-step2')
                </div>
                <div class="carousel-item">
                    @include('learner.become-tutor-forms-step3')
                </div>
            </div>
        </div>

        @csrf
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('assets/lib/jquery-ui-1.14.1/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script src="{{ asset('assets/lib/katex0.16.9/js/katex.min.js') }}"></script>
    <script src="{{ asset('assets/lib/quilljs2.0.3/js/quill.min.js') }}"></script>
    <script src="{{ asset('assets/lib/maxlength/maxlength.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms-step1.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms-step2.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms-step3.js') }}"></script>
    <script>
        $(document).ready(function()
        {
            initStep1();

            if (typeof repopulateOldInput !== 'undefined' && typeof repopulateOldInput === 'function')
            {
                repopulateOldInput();
            }
        });
    </script>
    @if ($errors->any())
    <script>
        // Repopulate form fields with old input data
        const oldInput = @json(session()->getOldInput());

        function repopulateOldInput()
        {
            $('#fluency-level').val(oldInput['fluency-level']).selectmenu('refresh');

            if ('education-year-from-0' in oldInput)
            {
                let currentYr = new Date().getFullYear();
                let fromYr = oldInput['education-year-from-0'];
                let toYr   = oldInput['education-year-to-0'];

                $('#education-year-from-0').html(generateYearOptions(currentYr, 1980));
                $('#education-year-to-0').html(generateYearOptions(currentYr, fromYr));

                buildYearRangeSelect('#education-year-from-0', '#education-year-to-0');
                $('#education-year-from-0').val(fromYr).selectmenu('refresh')
                $('#education-year-to-0').val(toYr).selectmenu('refresh')
            }

            if (typeof quill !== 'undefined')
            {
                // Only when there are errors returned
                quill.root.innerHTML = $('#about').val();
            }

            for (const key in oldInput)
            {
                if (oldInput.hasOwnProperty(key))
                {
                    const input = oldInput[key];
                    const matches = key.match(/(education|work|certification)-(.*)-(\d+)/);

                    if (matches)
                    {
                        const [fullMatch, category, field, index] = matches;
                        const entryFieldName = `${category}-${field}-${index}`;

                        if (!$(`#${entryFieldName}`).length)
                        {
                            switch (category)
                            {
                                case 'education':

                                    // Exlucde the default entry from dynamic entries
                                    if (entryFieldName == 'education-year-from-0')
                                        continue;

                                    // Add the dynamic entries
                                    let educationEntry = new EntryItem({
                                        fieldPrefix:    'education',
                                        container:      '#education-entries',
                                        yearEntryMode:  'range',
                                        field1:         'Institution',
                                        field2:         'Degree',

                                        'repopulateFromYear' : oldInput[`education-year-from-${index}`],
                                        'repopulateToYear'   : oldInput[`education-year-to-${index}`],
                                    });

                                    educationEntry.add();
                                    break;

                                case 'work':
                                    let workEntry = new EntryItem({
                                        fieldPrefix:    'work',
                                        container:      '#work-entries',
                                        yearEntryMode:  'range',
                                        field1:         'Company',
                                        field2:         'Role',

                                        'repopulateFromYear' : oldInput[`work-year-from-${index}`],
                                        'repopulateToYear'   : oldInput[`work-year-to-${index}`],
                                    });

                                    workEntry.add();
                                    break;

                                case 'certification':
                                    let certEntry = new EntryItem({
                                        fieldPrefix:    'certification',
                                        container:      '#cert-entries',
                                        field1:         'Title',
                                        field2:         'Description',

                                        'repopulateFromYear' : oldInput[`certification-year-from-${index}`]
                                    });

                                    certEntry.add();
                                    break;
                            }
                        }

                        $(`#${entryFieldName}`).val(input);
                    }
                }
            }

            if (Object.keys(oldInput).length > 0)
            {
                MsgBox.showError('Please double check your entries and fill out all fields!', 'Registration Failed');
            }
        }
    </script>
    @endif
@endpush

