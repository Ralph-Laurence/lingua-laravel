<?php

namespace App\Http\Utils;

class FluencyLevels {

    const Tutor = [
        '0' => ['Level' => 'Apprentice',   'Badge Color' => 'badge-gray',  'Badge Icon' => 'fa-lightbulb',      'Description' => 'Gaining experience in teaching ASL'],
        '1' => ['Level' => 'Intermediate', 'Badge Color' => 'badge-cyan',  'Badge Icon' => 'fa-book-bookmark',  'Description' => 'Moderate experience and skill in teaching ASL'],
        '2' => ['Level' => 'Advanced',     'Badge Color' => 'badge-green', 'Badge Icon' => 'fa-graduation-cap', 'Description' => 'High proficiency in teaching, capable of instructing complex topics'],
        '3' => ['Level' => 'Fluent',       'Badge Color' => 'badge-pro',   'Badge Icon' => 'fa-crown',          'Description' => 'Extensive experience, certified or native signer, able to teach all levels']
    ];
}
