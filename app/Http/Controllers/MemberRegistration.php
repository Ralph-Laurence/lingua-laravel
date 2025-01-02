<?php

namespace App\Http\Controllers;

use App\Http\Utils\FluencyLevels;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberRegistration extends Controller
{
    private function getFluencyFilters()
    {
        $fluencyFilter = [];

        foreach (FluencyLevels::Learner as $key => $obj)
        {
            $fluencyFilter[$key] = $obj['Level'];
        }

        return $fluencyFilter;
    }

    public function showMemberRegistrationForm()
    {
        $fluencyFilter = $this->getFluencyFilters();
        return view('learner.registration', compact('fluencyFilter'));
    }

    public function registerLearner(Request $request)
    {
        $rules = [
            'firstname' => 'required|string|max:32',
            'lastname'  => 'required|string|max:32',
            'contact'   => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'fluency'   => 'required|integer|max:3|in:' . implode(',', array_keys(FluencyLevels::Learner)),
            'email'     => 'required|string|email|max:255|unique:users',
            'username'  => 'required|string|max:32|unique:users',
            'password'  => 'required|string|min:4|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);
        //$error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $validator->validated();

        // return view('test.test', compact('inputs'));
        $user = User::create([
            UserFields::Firstname   => $inputs['firstname'],
            UserFields::Lastname    => $inputs['lastname'],
            UserFields::Contact     => $inputs['contact'],
            UserFields::Address     => $inputs['address'],
            'email'                 => $inputs['email'],
            UserFields::Username    => $inputs['username'],
            'password'              => Hash::make($inputs['password'])
        ]);

        Profile::create([
            ProfileFields::UserId   => $user->id,
            ProfileFields::Fluency  => $inputs['fluency']
        ]);

        // Optionally, log the user in after registration
        auth()->login($user);

        return redirect()->to('/'); // Adjust the route as needed
    }
}
