<?php

namespace App\Services;

use App\Http\Utils\Constants;
use App\Http\Utils\HashSalts;
use App\Mail\RevertEmailUpdateMail;
use App\Models\FieldNames\DocProofFields;
use App\Models\FieldNames\PendingEmailUpdateFields;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingEmailUpdate;
use App\Models\Profile;
use App\Models\User;
use App\Rules\CheckCurrentPassword;
use App\Rules\CheckOldPassword;
use App\Rules\PreventPasswordReuse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Hashids\Hashids;

class MyProfileService
{
    private static $hashids = null;

    public function __construct(
        private MyProfileEducationDocumentsService $educDocSvc,
        private MyProfileWorkExpDocumentsService $workDocSvc,
        private MyProfileCertificationsDocumentsService $certDocSvc
    )
    {
    }
    //
    //==========================================
    //    C O N T R O L L E R   A C T I O N S
    //==========================================
    //
    public function index()
    {
        $userObj = null; //Auth::user();

        try
        {
            $userObj = User::where('id', Auth::id())->with('profile')->firstOrFail();
        }
        catch (ModelNotFoundException $ex)
        {
            return response()->view('errors.404', [], 404);
        }

        // Trim contact number to not show trailing zero
        $phone = $userObj->{UserFields::Contact};

        if ($phone[0] == '0')
            $phone = ltrim($phone, $phone[0]);

        $photo = $userObj->{UserFields::Photo};
        $photoExists = false;

        if (!empty($photo) && Storage::exists("public/uploads/profiles/$photo"))
        {
            $photoExists = true;
            $photo = asset(Storage::url("public/uploads/profiles/$photo"));
        }
        else
        {
            $photo = asset('assets/img/default_avatar.png');
        }

        $role = $userObj->{UserFields::Role};
        $user = [
            'firstname'     => $userObj->{UserFields::Firstname},
            'lastname'      => $userObj->{UserFields::Lastname},
            'username'      => $userObj->{UserFields::Username},
            'email'         => $userObj->email,
            'photo'         => $photo,//$userObj->photoUrl,
            'photoExists'   => $photoExists,
            'contact'       => $phone,
            'address'       => $userObj->{UserFields::Address},
            'role'          => $role
        ];

        //
        // Handle cases when the user has pending email change
        //
        $pendingEmailUpdate = PendingEmailUpdate::where(
            PendingEmailUpdateFields::UserId, Auth::id()
        );
        $pendingEmail = $pendingEmailUpdate->first();
        $hasPendingEmailUpdate = false;

        if ($pendingEmail)
        {
            $pendingEmail = $pendingEmail->{PendingEmailUpdateFields::NewEmail};
            $hasPendingEmailUpdate = true;
        }
        //
        //
        //
        if ($role == User::ROLE_TUTOR)
        {
            $educationProof = $userObj->profile->{ProfileFields::Education};
            $workProof      = $userObj->profile->{ProfileFields::Experience};
            $certProof      = $userObj->profile->{ProfileFields::Certifications};
            $skillsList     = $userObj->profile->{ProfileFields::Skills};

            if (!empty($educationProof))
            {
                $education = $this->educDocSvc->formatEducationProofList($educationProof);
                $userObj->profile->{ProfileFields::Education} = $education;
            }

            if (!empty($workProof))
            {
                $work = $this->workDocSvc->formatWorkProofList($workProof);
                $userObj->profile->{ProfileFields::Experience} = $work;
            }

            if (!empty($certProof))
            {
                $cert = $this->certDocSvc->formatCertificationProofList($certProof);
                $userObj->profile->{ProfileFields::Certifications} = $cert;
            }

            if (!empty($skillsList))
            {
                $skills = [];

                foreach ($skillsList as $skill)
                {
                    $skills[$skill] = User::SOFT_SKILLS[$skill];
                }

                $userObj->profile->{ProfileFields::Skills} = $skills;
            }

            $user['profile'] = $userObj->profile;
        }

        return view('myprofile.edit', compact('user', 'hasPendingEmailUpdate', 'pendingEmail'))
            ->with('disabilities', Constants::Disabilities);
    }

    public function updatePassword(Request $request)
    {
        // Define validation rules
        $rules = [
            'current_password'      => ['required', 'string', new CheckCurrentPassword], // |min:8
            'new_password'          => ['required', 'string', 'min:8', 'not_regex:/\s/', new PreventPasswordReuse],
            'password_confirmation' => 'required|string|same:new_password'
        ];

        // Define custom error messages
        $messages = [
            'current_password.required'         => 'Please enter your current password.',
            //'current_password.password_check'   => 'The current password is incorrect.',
            'new_password.required'             => 'Please enter a new password.',
            'new_password.min'                  => 'The new password must be at least 8 characters.',
            'new_password.not_regex'            => 'The new password must not contain spaces.',
            'password_confirmation.required'    => 'Please re-enter your new password.',
            'password_confirmation.same'        => 'The new password and confirmation password do not match.',
        ];

        // Validate the request
        $fields = array_keys($rules);
        $validator = Validator::make($request->only($fields), $rules, $messages);

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

    public function updateBio(Request $request)
    {
        $rules    = [ 'bio' => 'required|string|max:180' ];
        $messages = ['bio.required' => 'Please write a short note about yourself'];

        $validator = Validator::make($request->only('bio'), $rules, $messages);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try
        {
            $profile = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();

            // Update the bio
            $profile->{ProfileFields::Bio} = $request->bio;
            $profile->save();

            DB::commit();
            session()->flash('profile_update_message', "Your bio has been successfully updated.");
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            session()->flash('profile_update_message', "An error occurred while trying to update your bio. Please try again later.");
        }

        return redirect()->back();
    }

    public function removePhoto()
    {
        $user = Auth::user();
        $photo = $user->{UserFields::Photo};
        $photoExists = (!empty($photo) && Storage::exists("public/uploads/profiles/$photo"));

        if (!$photoExists)
        {
            session()->flash('profile_update_message', "We were unable to remove your profile picture. It may have already been removed or can't be accessed.");
            return redirect()->back();
        }

        DB::beginTransaction();

        try
        {
            // Remove photo from the file system
            Storage::delete("public/uploads/profiles/$photo");

            // Remove photo from the database
            $user->{UserFields::Photo} = null;
            $user->save();

            DB::commit();
            session()->flash('profile_update_message', "Your profile picture has been successfully removed.");
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            session()->flash('profile_update_message', "An error occurred while trying to remove your profile picture. Please try again.");
        }

        return redirect()->back();
    }

    /**
     * This function handles the updating of the user's username and email
     */
    public function updateAccount(Request $request)
    {
        $rules = [
            'username' => 'required|string|min:4|max:32|not_regex:/\s/|unique:users,'. UserFields::Username .',' . Auth::id(),
            'email'    => 'required|email|max:50|not_regex:/\s/|unique:users,email,' . Auth::id()
        ];

        $messages = [
            'username.not_regex' => 'The username must not contain spaces.',
            'email.not_regex' => 'The email must not contain spaces.'
        ];

        $fields = array_keys($rules);
        $validator = Validator::make($request->only($fields), $rules, $messages);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Don't proceed when there are actually no changes
        if ($user->username === $request->username &&
            $user->email === $request->email
        )
        {
            session()->flash('profile_update_message', 'No changes had been made. Your profile remains the same.');
            return redirect()->route('myprofile.edit');
        }

        try
        {
            DB::beginTransaction();

            // Update the username and immediately persist the changes.
            // We should only do this whenever there are actual changes.
            if ($user->username !== $request->username)
            {
                // $updatedFields[UserFields::Username] = $request->username;
                $user->update([UserFields::Username => $request->username]);
            }

            // If there are changes in the email input, we update the email.
            // The only exception is we cant update emails that are pending.
            $hasPendingEmailUpdate = PendingEmailUpdate::where(
                PendingEmailUpdateFields::UserId, Auth::id()
            )->exists();

            $encodedId = self::toHashedId(Auth::id());
            $verificationCode = mt_rand(100000, 999999);

            if ($user->email !== $request->email && !$hasPendingEmailUpdate)
            {
                PendingEmailUpdate::create([
                    PendingEmailUpdateFields::UserId    => Auth::id(),
                    PendingEmailUpdateFields::OldEmail  => $user->email,
                    PendingEmailUpdateFields::NewEmail  => $request->email,
                    PendingEmailUpdateFields::VerificationCode => $verificationCode
                ]);
            }

            // Disable AVAST Mail Shield "Outbound SMTP" before sending emails
            $emailData = [
                'action'    => url(route('myprofile.email-confirmation', [$encodedId])),
                'logo'      => public_path('assets/img/logo-brand-sm.png'),
                'newEmail'  => $request->email,
                'code'      => $verificationCode
            ];

            Mail::to($request->email)->send(new RevertEmailUpdateMail($emailData));

            DB::commit();

            session()->flash('profile_update_message', "We've sent you an email at " . $request->email);
            return redirect()->route('myprofile.edit');
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());
            DB::rollBack();

            session()->flash('profile_update_message', "We're experiencing technical issues while updating your profile. Please try again later.");
            return redirect()->back();
        }
    }

    public function updateIdentity(Request $request)
    {
        $user = Auth::user();

        $rules  = [
            'firstname' => 'required|string|max:32',
            'lastname'  => 'required|string|max:32',
            'contact'   => ['required', 'regex:/^9\d{9}$/', 'not_regex:/\s/'],
            'address'   => 'required|string|max:150',
        ];

        $messages = [
            'contact.regex' => 'Contact number must be in the format of 9xxxxxxxxx (ex. 9123456780)'
        ];

        $fields = array_keys($rules);
        $validator = Validator::make($request->only($fields), $rules, $messages);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Clone the user object to check for changes
        $tempUser = clone $user;
        $tempUser->fill($request->only($fields));

        // Check if there are any dirty attributes
        if (!$tempUser->isDirty())
        {
            session()->flash('profile_update_message', 'No changes had been made. Your profile remains the same.');
            return redirect()->route('myprofile.edit');
        }

        try
        {
            $user->update([
                UserFields::Firstname => $request->firstname,
                UserFields::Lastname  => $request->lastname,
                UserFields::Contact   => $request->contact,
                UserFields::Address   => $request->address,
            ]);

            session()->flash('profile_update_message', 'Your profile has been successfully updated!');
            return redirect()->route('myprofile.edit');
        }
        catch (Exception $ex)
        {
            session()->flash('profile_update_message', "We're experiencing technical issues while updating your profile. Please try again later.");
            return redirect()->back();
        }
    }

    public function revertEmailUpdate()
    {
        try
        {
            $pendingEmail = PendingEmailUpdate::where(
                PendingEmailUpdateFields::UserId, Auth::id()
            )->firstOrFail();

            DB::beginTransaction();

            $deleted = $pendingEmail->delete();

            if ($deleted)
            {
                DB::commit();
                session()->flash('profile_update_message', 'Your email change has been successfully reverted.');
            }
            else
            {
                DB::rollBack();
                session()->flash('profile_update_message', "We couldn't revert your email change due to an internal error.");
            }
        }
        catch (ModelNotFoundException $e)
        {
            session()->flash('profile_update_message', "No pending email change found to revert.");
        }
        catch (Exception $e)
        {
            DB::rollBack();
            session()->flash('profile_update_message', "We're unable to revert your email due to a technical error.");
        }

        return redirect()->route('myprofile.edit');
    }

    public function confirmEmailUpdate(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $userId = self::toRawId($request->input('profileId'));
            $pendingEmail = PendingEmailUpdate::where(
                PendingEmailUpdateFields::UserId, $userId
            )->firstOrFail();

            $code = $request->input('code');

            if ($pendingEmail->{PendingEmailUpdateFields::VerificationCode} != $code)
            {
                return redirect()->back()->withErrors(['code' => 'Incorrect verification code.'])->withInput();
            }

            // Grab the new email
            $newEmail = $pendingEmail->{PendingEmailUpdateFields::NewEmail};

            // Delete the pending email update
            $deleted = $pendingEmail->delete();

            // Update the user's email
            $updated = User::where('id', $userId)->update([ 'email' => $newEmail ]);

            if (!$deleted || !$updated)
            {
                // Trigger the catch block
                throw new Exception();
            }
            DB::commit();

            // Update the authenticated user's email
            $user = Auth::user();
            $user->email = $newEmail;
            Auth::setUser($user);

            session()->flash('profile_update_message', "Your email has been successfully updated.");
        }
        catch (ModelNotFoundException $mx)
        {
            return response()->view('errors.404', [], 404);
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            session()->flash('profile_update_message', "We're unable to revert your email due to a technical error.");
        }

        return redirect()->route('myprofile.edit');
    }

    public function showEmailConfirmation($id)
    {
        try
        {
            $userId = self::toRawId($id);
            $pendingEmail = PendingEmailUpdate::where(
                PendingEmailUpdateFields::UserId, $userId
            )->firstOrFail();

            return view('myprofile.email-confirmation')
                ->with('profileId', $id)
                ->with('newEmail', $pendingEmail->{PendingEmailUpdateFields::NewEmail})
                ->with('oldEmail', $pendingEmail->{PendingEmailUpdateFields::OldEmail});
        }
        catch (ModelNotFoundException $mx)
        {
            session()->flash('profile_update_message', "No pending email change found for your account.");
        }
        catch (Exception $ex)
        {
            session()->flash('profile_update_message', "We're unable to handle the request due to a technical error.");
        }

        return redirect()->route('myprofile.edit');
    }

    public function addSkills(Request $request)
    {
        $errMsg = "The requested action can't be processed right now. Please try again later.";
        $err500 = response()->view('errors.500', ["message" => $errMsg], 500);
        $err404 = response()->view('errors.500', ["message" => "The skills record doesn't exist or can't be found"], 404);

        if (!$request->filled('skillsKeys'))
        {
            session()->flash('profile_update_message', $errMsg);
            return redirect()->back();
        }

        // Convert the comma-separated string to an array
        $keysArray = explode(',', $request->input('skillsKeys'));

        $rules  = [
            'skillsKeys'     => 'required|array',
            'skillsKeys.*'   => 'required|numeric|in:'.implode(',',array_keys(User::SOFT_SKILLS))
        ];

        $messages = [
            'skillsKeys.required'  => 'Please select atleast one skill!',
            'skillsKeys.array'     => 'The data was not in a valid format.',
            'skillsKeys.*.numeric' => 'The selected skill is invalid.',
            'skillsKeys.*.in'      => 'One or more skills are not allowed.',
        ];

        $validator = Validator::make(['skillsKeys' => $keysArray], $rules, $messages);

        if ($validator->fails())
        {

            // Use 422 status code for validation errors
            session()->flash('profile_update_err_alert', $errMsg);
            return redirect()->back()->withErrors($validator);
        }

        try
        {
            DB::beginTransaction();

            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $model->{ProfileFields::Skills} = $keysArray;
            $updated = $model->save();

            if (!$updated)
                return $err500;

            DB::commit();

            $message = count($keysArray) == 1
                     ? 'A skill has been been successfully added.'
                     : 'Skill entries has been successfully added.';

            session()->flash('profile_update_message', $message);
            return redirect()->route('myprofile.edit');
        }
        catch (ModelNotFoundException $ex)
        {
            return $err404;
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());
            DB::rollBack();
            return $err500;
        }
    }

    public function updateSkills(Request $request)
    {
        $err500Body = ["message" => "We're unable to read the record. Please try again later."];
        $err500 = response()->json($err500Body, 500);
        $err404 = response()->json(["message" => "The skills record doesn't exist or can't be found"], 404);
        $rules  = [
            'keys'     => 'required|array',
            'keys.*'   => 'required|numeric|in:'.implode(',',array_keys(User::SOFT_SKILLS))
        ];

        $messages = [
            'keys.required'  => 'Please select atleast one skill!',
            'keys.array'     => 'The data was not in a valid format.',
            'keys.*.numeric' => 'The selected skill is invalid.',
            'keys.*.in'      => 'One or more skills are not allowed.',
        ];

        $validator = Validator::make($request->only(['keys']), $rules, $messages);

        if ($validator->fails())
        {
            $err500Body['allowEdit'] = true;
            $err500Body['errors'] = $validator->errors();

            return response()->json($err500Body, 422); // Use 422 status code for validation errors
        }

        try
        {
            DB::beginTransaction();

            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $model->{ProfileFields::Skills} = $request->keys;

            $updated = $model->save();

            if (!$updated)
                return $err500;

            $updatedSkills = [];

            if (!empty($request->keys))
                $updatedSkills = array_intersect_key(User::SOFT_SKILLS, array_flip($request->keys));

            DB::commit();

            return response()->json([
                'message'   => 'Skills entry has been successfully updated!',
                'data'      => $updatedSkills
            ], 200);
        }
        catch (ModelNotFoundException $ex)
        {
            return $err404;
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $err500;
        }
    }

    public function removeSkills(Request $request)
    {
        $err500 = response()->json(["message" => "We're unable to read the record. Please try again later."], 500);
        $err404 = response()->json(["message" => "The skills record doesn't exist or can't be found"], 404);

        try
        {
            DB::beginTransaction();

            $model = Profile::where(ProfileFields::UserId, Auth::id())->firstOrFail();
            $model->{ProfileFields::Skills} = [];
            $updated = $model->save();

            if (!$updated)
                return $err500;

            DB::commit();

            session()->flash('profile_update_message', "Skill entries has been successfully cleared.");
            return redirect()->route('myprofile.edit');
        }
        catch (ModelNotFoundException $ex)
        {
            return $err404;
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $err500;
        }
    }
    //
    //==========================================
    //              H A S H I N G
    //==========================================
    //
    public static function getHashidInstance()
    {
        if (self::$hashids == null)
            self::$hashids = new Hashids(HashSalts::Profile, 10);

        return self::$hashids;
    }

    public static function toHashedId($rawId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->encode($rawId);
    }

    public static function toRawId($hashedId)
    {
        $hashid = self::getHashidInstance();
        return $hashid->decode($hashedId)[0] ?? 0;
    }
}
