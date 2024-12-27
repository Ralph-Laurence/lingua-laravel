<?php

namespace App\Http\Controllers;

use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LearnerController extends Controller
{
    public function index()
    {
        return view('learner.index');
    }

    public function myTutors()
    {
        $myTutorsIds = Booking::where(BookingFields::LearnerId, Auth::user()->id)
                        ->pluck(BookingFields::TutorId)
                        ->toArray();

        $myTutors = [];

        if (!empty($myTutorsIds))
        {
            $hashids = new Hashids(HashSalts::Tutors, 10);
            $tutors  = User::whereIn('id', $myTutorsIds)->get();

            foreach ($tutors as $key => $obj)
            {
                $photo = $obj->{UserFields::Photo};

                if (empty($photo))
                    $photo = asset('assets/img/default_avatar.png');

                else
                    $photo = Storage::url("public/uploads/profiles/$photo");

                $myTutors[] = [
                    'tutorId'   => $hashids->encode($obj['id']),
                    'shortName' => $this->getShortNameAttribute($obj->{UserFields::Firstname}, $obj->{UserFields::Lastname}),
                    'photo'     => $photo
                ];
            }
        }

        return view('learner.my-tutors')->with('myTutors', $myTutors);
    }

    public function becomeTutor()
    {
        return view('learner.become-tutor');
    }

    public function becomeTutorFormsPage()
    {
        $currentYear = date('Y');
        return view('learner.become-tutor-forms', compact('currentYear'));
    }

    public function becomeTutorOnSubmit(Request $request)
    {
        // return print_r($request->input());
        $rules = [

            // All input files
            '*-file-upload-*' => 'nullable|file|mimetypes:application/pdf|max:2048',

            // Non dynamic entries
            'bio'   => 'required|string|max:180',
            'about' => 'required|string|max:2000',

            // Education
            'education-institution.*'   => 'required|string|max:255',
            'education-degree.*'        => 'required|string|max:255',
            'education-year-from.*'     => 'required|integer|min:1900|max:' . date('Y'),
            'education-year-to.*'       => 'required|integer|min:1900|max:' . date('Y'),
        ];

        if ($request->has('work-company.0'))
        {
            $rules = array_merge($rules, [
                'work-company.*'            => 'required|string|max:255',
                'work-role.*'               => 'required|string|max:255',
                'work-year-from.*'          => 'required|integer|min:1900|max:' . date('Y'),
                'work-year-to.*'            => 'required|integer|min:1900|max:' . date('Y'),
            ]);
        }

        if ($request->has('certification-title.0'))
        {
            $rules = array_merge($rules, [
                'certification-title.*'         => 'required|string|max:255',
                'certification-description.*'   => 'required|string|max:255',
                'certification-year-from.*'     => 'required|integer|min:1900|max:' . date('Y'),
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return var_dump($validator->validated());
    }

    private function getShortNameAttribute($firstName, $lastName)
    {
        // Take the first character of the last name
        $lastNameInitial = strtoupper(mb_substr($lastName, 0, 1)) . '.';

        return "{$firstName} {$lastNameInitial}";
    }

    public function updateEducation(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $educationEntries = [];
        for ($i = 0; $i < count($request->input('year-from')); $i++) {
            $educationEntries[] = [
                'from' => $request->input("year-from.$i"),
                'to' => $request->input("year-to.$i"),
                'degree' => $request->input("degree.$i"),
                'institution' => $request->input("institution.$i"),
            ];
        }

        $user->education = $educationEntries;
        $user->save();

        return redirect()->back()->with('success', 'Education background updated successfully.');
    }
}

