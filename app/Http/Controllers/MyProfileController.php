<?php

namespace App\Http\Controllers;

use App\Models\FieldNames\UserFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MyProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Trim contact number to not show trailing zero
        $phone = $user->{UserFields::Contact};

        if ($phone[0] == '0')
            $phone = ltrim($phone, $phone[0]);

        $user = [
            'firstname' => $user->{UserFields::Firstname},
            'lastname'  => $user->{UserFields::Lastname},
            'username'  => $user->{UserFields::Username},
            'email'     => $user->email,
            'photo'     => $user->photoUrl,
            'contact'   => $phone,
            'address'   => $user->{UserFields::Address}
        ];
        return view('myprofile.edit', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        // Define validation rules
        $rules = [
            'current_password'      => 'required|string|passwordCheck', // |min:8
            'new_password'          => 'required|string|min:4|not_regex:/\s/',
            'password_confirmation' => 'required|string|same:new_password'
        ];

        // Define custom error messages
        $messages = [
            'current_password.required'         => 'Please enter your current password.',
            'current_password.passwordCheck'    => 'The current password is incorrect.',
            'new_password.required'             => 'Please enter a new password.',
            'new_password.min'                  => 'The new password must be at least 8 characters.',
            'new_password.not_regex'            => 'The new password must not contain spaces.',
            'password_confirmation.required'    => 'Please re-enter your new password.',
            'password_confirmation.same'        => 'The new password and confirmation password do not match.',
        ];

        // Create a custom validation rule for the current password check
        Validator::extend('passwordCheck', function ($attribute, $value, $parameters, $validator)
        {
            return Hash::check($value, auth()->user()->password);
        });

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        // return view('test.test', [
        //     'inputs' => $request->all()
        // ]);
        if ($validator->fails())
        {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        // Update the user's password
        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        session()->flash('profile_update_message', 'Password updated successfully!');
        return redirect()->back();
    }


    public function updatePhoto(Request $request)
    {
        $request->validate([
            'cropped_photo' => 'required',
        ]);

        $user = Auth::user();
        $base64Image = $request->input('cropped_photo');

        // Decode base64 string
        list($type, $data) = explode(';', $base64Image);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        // Create image instance
        $img = Image::make($data);

        // Crop and resize
        $img->fit(200, 200);

        // Generate filename
        $filename = time() . '.png';
        $path = 'uploads/profiles/' . $filename;

        // Save the image
        $img->save(storage_path('app/public/' . $path));

        // Delete old photo
        if ($user->photo) {
            Storage::disk('public')->delete('uploads/profiles/' . $user->photo);
        }

        $user->photo = $filename;
        $user->save();

        return redirect()->route('myprofile.edit');
    }

    /**
     * This function handles the updating of the user's username and email
     */
    public function updateAccount(Request $request)
    {
        $rules = [
            'username' => 'required|string|min:4|max:32|not_regex:/\s/',
            'email'    => 'required|email|max:50|not_regex:/\s/'
        ];

        $messages = [
            'username.not_regex' => 'The username must not contain spaces.',
            'email.not_regex' => 'The email must not contain spaces.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }
}
