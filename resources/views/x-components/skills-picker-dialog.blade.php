@push('scripts')
    <script src="{{ asset('assets/js/components/skills-picker-dialog.js') }}"></script>
@endpush
<!-- Modal -->
<div class="modal fade" id="skillsPickerModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="skillsPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title" id="skillsPickerModalLabel darker-text">Pick your skills</h6>
                {{-- <button type="button btn-sm" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody class="skill-picker-body">
                        @php
                            $keys = array_keys($skillsMap);
                            $values = array_values($skillsMap);
                        @endphp
                        @foreach(array_chunk($keys, 3) as $chunk)
                            <tr>
                                @foreach($chunk as $index)
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input skill-checkbox" type="checkbox" name="skillset[]" value="{{ $index }}" id="skill_{{ $index }}">
                                            <label class="form-check-label text-13" for="skill_{{ $index }}">
                                                {{ $skillsMap[$index] }}
                                            </label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer py-2">
                <x-sl-button type="button" class="px-2 btn-cancel" style="secondary" text="Cancel"/>
                <x-sl-button type="button" class="px-3 btn-ok" style="primary" text="OK"/>
            </div>
        </div>
    </div>
</div>
