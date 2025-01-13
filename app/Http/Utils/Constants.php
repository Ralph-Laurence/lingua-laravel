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
}
