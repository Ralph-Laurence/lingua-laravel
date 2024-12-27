@extends('partials.base')

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
    <form action="{{ route('become-tutor.forms.submit') }}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div id="form-carousel" class="carousel slide" data-interval="false">
            <div class="carousel-inner">
                <div class="carousel-item active" id="step1">
                    @include('learner.become-tutor-forms-step1')
                </div>
                <div class="carousel-item" id="step2">
                    @include('learner.become-tutor-forms-step2')
                </div>
            </div>
        </div>
        @csrf
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/bootstrap5-form-novalidate.js') }}"></script>
    <script src="{{ asset('assets/js/become-tutor-forms.js') }}"></script>

    @if ($errors->any())
    <script>
        // Repopulate form fields with old input data
        const oldInput = @json(session()->getOldInput());

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
                            let educationEntry = new EntryItem({
                                    fieldPrefix:    'education',
                                    container:      '#education-entries',
                                    yearEntryMode:  'range',
                                    field1:         'Institution',
                                    field2:         'Degree'
                                });

                                educationEntry.add();
                                break;

                            case 'work':
                                workEntry.add();
                                break;

                            case 'certification':
                                certificationEntry.add();
                                break;
                        }
                    }

                    $(`#${entryFieldName}`).val(input);
                }
            }
        }

        $(() => {
            if (Object.keys(oldInput).length > 0)
            {
                MsgBox.showError('Please double check your entries and fill out all fields!', 'Registration Failed');
            }
        });
    </script>
    @endif
@endpush

