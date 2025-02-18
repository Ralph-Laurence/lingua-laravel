@php $includeFooter = false; @endphp
@extends('shared.base-admin')
@section('title')Review Registration Details @endsection
@section('content')
    <main class="workspace-wrapper">
        <aside class="workspace-sidepane" style="width: fit-content; overflow-x: hidden; overflow-y: auto;">
            <div class="action-pane" style="width: 260px;">
                <h6 class="action-pane-title border p-2 rounded text-center">
                    Registration Approval
                </h6>
                <hr class="border border-gray-800">
                <h6 class="text-13 fw-bold">
                    <i class="fas fa-user me-2"></i>Applicant Information
                </h6>
                <div class="readonly-field">
                    <label class="text-secondary text-12" for="applicant-name">Name</label>
                    <input type="text" class="form-control text-13 field-no-focus" id="applicant-name" readonly
                        value="{{ $applicantDetails['fullname'] }}">
                </div>
                @if (!empty($applicantDetails['disability']))
                    <div class="readonly-field">
                        <label class="text-secondary text-12">Disability</label>
                        <input type="text" class="form-control text-13 field-no-focus" readonly
                            value="{{ $applicantDetails['disability'] }}">
                    </div>
                @endif
                <div class="readonly-field">
                    <label class="text-secondary text-12" for="applicant-email">Email</label>
                    <input type="text" class="form-control text-13 field-no-focus" id="applicant-email" readonly
                        value="{{ $applicantDetails['email'] }}">
                </div>
                <div class="readonly-field">
                    <label class="text-secondary text-12" for="applicant-contact">Contact</label>
                    <input type="text" class="form-control text-13 field-no-focus" id="applicant-contact" readonly
                        value="{{ $applicantDetails['contact'] }}">
                </div>
                <div class="readonly-field mb-2">
                    <label class="text-secondary text-12" for="applicant-address">Address</label>
                    <textarea id="address" class="form-control no-resize text-12 field-no-focus" readonly>{{ $applicantDetails['address'] }}</textarea>
                </div>
                <a type="button" id="btn-approve" href="{{ route('admin.tutors-approve-registration', $applicantDetails['hashedId']) }}"
                    class="btn btn-sm btn-danger w-100 action-button btn-accept">Approve</a>
                <a role="button" id="btn-decline" href="{{ route('admin.tutors-decline-registration', $applicantDetails['hashedId']) }}"
                    class="btn btn-sm btn-outline-secondary w-100 mt-2 btn-decline">Decline</a>
            </div>
        </aside>
        <section class="workspace-workarea mb-4">

            <div class="card">
                <div class="card-body p-4">
                    <h6 class="mb-4">Documentary Proof(s)</h6>

                    @if (!empty($applicantDetails['education']))
                        <h5 class="darker-text mb-3 text-14 fw-bold">
                            <i class="fa-solid fa-paperclip me-2"></i>
                            EDUCATION
                        </h5>

                        @foreach ($applicantDetails['education'] as $k => $obj)
                            <div class="mb-4" id="education-entries">
                                {{-- BEGIN: USE THIS AS ENTRY TEMPLATE --}}
                                <div class="entry mt-3 p-3 border border-1 rounded rounded-3">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <label class="text-14 text-secondary">From Year</label>
                                            <div class="readonly-field">
                                                <input type="text" class="form-control text-13" readonly
                                                    value="{{ $obj['from'] }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="text-14 text-secondary">To Year</label>
                                            <div class="readonly-field">
                                                <input type="text" class="form-control text-13" readonly
                                                    value="{{ $obj['to'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label class="text-14 text-secondary">Institution</label>
                                            <div class="readonly-field">
                                                <input type="text" class="form-control text-13" readonly
                                                    value="{{ $obj['institution'] }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="text-14 text-secondary">Degree</label>
                                            <div class="readonly-field">
                                                <input type="text" class="form-control text-13" readonly
                                                    value="{{ $obj['degree'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="file-upload">
                                        <div class="text-14 text-secondary">Proof of Education</div>
                                        <button type="button" data-pdf-url="{{ asset($obj['docProof']) }}" class="btn btn-sm btn-secondary btn-view-doc-proof">
                                            View
                                        </button>
                                    </div>
                                </div>
                                {{-- END --}}
                            </div>
                        @endforeach
                    @endif

                    @if (!empty($applicantDetails['work']))
                        <h5 class="darker-text mb-3 text-14 fw-bold">
                            <i class="fa-solid fa-paperclip me-2"></i>
                            WORK EXPERIENCE
                        </h5>

                        @foreach ($applicantDetails['work'] as $k => $obj)
                        <div class="mb-4">
                            {{-- BEGIN: USE THIS AS ENTRY TEMPLATE --}}
                            <div class="entry mt-3 p-3 border border-1 rounded rounded-3">
                                <div class="row mb-2">
                                    <div class="col">
                                        <label class="text-14 text-secondary">From Year</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['from'] }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label class="text-14 text-secondary">To Year</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['to'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="text-14 text-secondary">Company</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['company'] }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label class="text-14 text-secondary">Role</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['role'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="file-upload">
                                    <div class="text-14 text-secondary">Proof of Work Experience</div>
                                    <button type="button" data-pdf-url="{{ asset($obj['docProof']) }}" class="btn btn-sm btn-secondary btn-view-doc-proof">
                                        View
                                    </button>
                                </div>
                            </div>
                            {{-- END --}}
                        </div>
                        @endforeach
                    @endif

                    @if (!empty($applicantDetails['certs']))
                        <h5 class="darker-text mb-3 text-14 fw-bold">
                            <i class="fa-solid fa-paperclip me-2"></i>
                            CERTIFICATIONS
                        </h5>

                        @foreach ($applicantDetails['certs'] as $k => $obj)
                        <div class="mb-4">
                            {{-- BEGIN: USE THIS AS ENTRY TEMPLATE --}}
                            <div class="entry mt-3 p-3 border border-1 rounded rounded-3">
                                <div class="row mb-2">
                                    <div class="col">
                                        <label class="text-14 text-secondary">Year</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['from'] }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="text-14 text-secondary">Title</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['title'] }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label class="text-14 text-secondary">Description</label>
                                        <div class="readonly-field">
                                            <input type="text" class="form-control text-13" readonly
                                                value="{{ $obj['description'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="file-upload">
                                    <div class="text-14 text-secondary">Proof of Work Experience</div>
                                    <button type="button" data-pdf-url="{{ asset($obj['docProof']) }}" class="btn btn-sm btn-secondary btn-view-doc-proof">
                                        View
                                    </button>
                                </div>
                            </div>
                            {{-- END --}}
                        </div>
                        @endforeach
                    @endif

                    <div class="card-body px-0">
                        <div class="alert alert-secondary text-12 text-center mb-0">
                            <i class="fas fa-circle-info"></i>
                            If you believe the submitted documents are valid and truthful, you may approve the registration request.<br>Otherwise, you may reject it.
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>
    {{-- <div id="pdf-viewer" class="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Documentary Proof</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdf-iframe" src="" style="width: 100%; min-height: 400px;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-dismiss="modal">OK, Close</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
@push('dialogs')
    <x-document-viewer-dialog />
@endpush
@push('style')
    <style>
        textarea#address {
            width: 100%;
            min-height: 50px;
            resize: none;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/lib/waitingfor/bootstrap-waitingfor.min.js') }}"></script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        let docViewerEvt = DocumentViewerDialog.events;

        $(document).ready(function()
        {
            const textarea = $('#address'); // Corrected selector
            autoResize(textarea.get(0)); // Adjust height on document ready

            // Optional: Add extra height if needed
            let addHeight = textarea.height() + 30;
            textarea.css('height', `${addHeight}px`);

            $('.btn-view-doc-proof').on('click', function()
            {
                let pdfUrl = $(this).data('pdf-url');
                // $('#pdf-iframe').attr('src', pdfUrl);
                // $('#pdf-viewer').modal('show');

                DocumentViewerDialog.show(pdfUrl);
            });

            $('#btn-approve').on('click', function(e)
            {
                e.preventDefault();
                waitingDialog.show("Please keep this page open", {
                    headerSize: 6,
                    headerText: 'Approving Registration...',
                    dialogSize: 'sm',
                    contentClass: 'text-13'
                });
                window.location.href = $(this).attr('href');
            });

            $('#btn-decline').on('click', function(e)
            {
                e.preventDefault();
                waitingDialog.show("Please keep this page open", {
                    headerSize: 6,
                    headerText: 'Declining Registration...',
                    dialogSize: 'sm',
                    contentClass: 'text-13'
                });
                window.location.href = $(this).attr('href');
            });
        })
        .on(docViewerEvt.NotFound, function()
        {
            MsgBox.showError("Sorry, we're unable to find the document. It might have already been removed.");
        })
        .on(docViewerEvt.LoadStarted,  () => showWaiting())
        .on(docViewerEvt.LoadFinished, () => waitingDialog.hide())
        .on('showWaitingDialog', () => showWaiting())
        .on('hideWaitingDialog', () => waitingDialog.hide());

    </script>
@endpush
