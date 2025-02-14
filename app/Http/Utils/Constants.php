<?php

namespace App\Http\Utils;

class Constants {
    const MinPageEntries = 10;
    const PageEntries = [10, 25, 50, 100];
    const StarRatings = [
        '1' => 'Not Satisfied',
        '2' => 'Needs Improvement',
        '3' => 'Acceptable',
        '4' => 'Very Good',
        '5' => 'Excellent'
    ];
    const Disabilities = [
        '0' => 'No Impairments',
        '1' => 'Deaf or Hard of Hearing',
        '2' => 'Non-Verbal',
        '3' => 'Both Deaf and Non-Verbal'
    ];
    const DocPathEducation      = 'public/documentary_proofs/education/';
    const DocPathWorkExp        = 'public/documentary_proofs/work_experience/';
    const DocPathCertification  = 'public/documentary_proofs/certification/';

    /**
     * We dont want to hurt our users' feelings.
     * DisabilitiesPublic is a non-offensive description
     * whereas DisabilitiesAdmin is explicitly for admin.
     */
    const DisabilitiesPublic = [
        '0' => 'No Impairments',
        '1' => 'Deaf or Hard of Hearing',
        '2' => 'Non-Verbal',
        '3' => 'Both Deaf and Non-Verbal'
    ];

    const DisabilitiesAdmin = [
        '0' => 'None',
        '1' => 'Deaf',
        '2' => 'Mute',
        '3' => 'Both Mute & Deaf'
    ];
}
