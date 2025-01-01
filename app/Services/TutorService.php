<?php

namespace App\Services;

use App\Http\Utils\FluencyLevels;
use App\Http\Utils\HashSalts;
use App\Mail\RegistrationApprovedMail;
use App\Models\FieldNames\ProfileFields;
use App\Models\FieldNames\UserFields;
use App\Models\PendingRegistration;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class TutorService
{
    const Role = User::ROLE_TUTOR;

    private $tutorHashIds;

    function __construct()
    {
        $this->tutorHashIds = new Hashids(HashSalts::Tutors, 10);
    }

    public static function FindById($id)
    {
        try
        {
            $tutor = User::where('id', $id)->where('role', self::Role)->firstOrFail();
            return $tutor;
        }
        catch (ModelNotFoundException $e)
        {
            return null;
        }
    }

    public function showReviewRegistration($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their pending registration data
            $tutorId      = $decodedId[0];
            $tutor        = User::findOrFail($tutorId);
            $pending      = PendingRegistration::where(ProfileFields::UserId, $tutorId)->firstOrFail();
            $fluencyLevel = FluencyLevels::Tutor[$pending->{ProfileFields::Fluency}];
            $skills       = [];

            if ($pending->{ProfileFields::Skills})
            {
                foreach($pending->{ProfileFields::Skills} as $skill)
                {
                    $skills[] = User::SOFT_SKILLS[$skill];
                }
            }

            $educationProof = $pending->{ProfileFields::Education};
            $workProof      = $pending->{ProfileFields::Experience};
            $certProof      = $pending->{ProfileFields::Certifications};

            if (!empty($educationProof))
            {
                foreach ($educationProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $educationProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $educationProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            if (!empty($workProof))
            {
                foreach ($workProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $workProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $workProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            if (!empty($certProof))
            {
                foreach ($certProof as $k => $obj)
                {
                    $pdfPath = $obj['full_path'];

                    // Ensure the PDF path is sanitized and validated
                    if (!Storage::exists($pdfPath))
                        $certProof[$k]['docProof'] = '-1'; // 'corrupted'

                    // Generate a secure URL for the PDF file
                    $certProof[$k]['docProof'] = Storage::url($pdfPath);
                }
            }

            $applicantDetails = [
                'hashedId'           => $id,
                'fullname'           => implode(' ', [$tutor->{UserFields::Firstname}, $tutor->{UserFields::Lastname}]),
                'email'              => $tutor->email,
                'contact'            => $tutor->{UserFields::Contact},
                'address'            => $tutor->{UserFields::Address},
                'verified'           => $tutor->{UserFields::IsVerified} == 1,
                'bio'                => $pending->{ProfileFields::Bio},
                'about'              => $pending->{ProfileFields::About},
                'work'               => $workProof,
                'education'          => $educationProof,
                'certs'              => $certProof,
                'skills'             => $skills,
                'fluencyBadgeIcon'   => $fluencyLevel['Badge Icon'],
                'fluencyBadgeColor'  => $fluencyLevel['Badge Color'],
                'fluencyLevelText'   => $fluencyLevel['Level'],
            ];

            $fluencyFilter = $this->getFluencyFilters();

            // Return the view with the tutor data
            return view('admin.tutors-review', compact('applicantDetails', 'fluencyFilter'));
        }
        catch (ModelNotFoundException $e)
        {
            error_log($e->getMessage());
            // Return custom 404 page
            return view('errors.404');
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            // Return custom 404 page
            return view('errors.500');
        }
    }

    public function approveRegistration($id)
    {
        try
        {
            // Decode the hashed ID
            $decodedId = $this->tutorHashIds->decode($id);

            // Check if the ID is empty
            if (empty($decodedId)) {
                return view('errors.404');
            }

            // Fetch the tutor along with their pending registration data
            $userId = $decodedId[0];

            // Start a transaction
            DB::beginTransaction();

            // Retrieve the pending registration record
            $pending = PendingRegistration::where('user_id', $userId)->firstOrFail();

            // Create or update the profile record
            $existingProfile = Profile::where(ProfileFields::UserId, $userId)->first();
            $upsertData = [
                ProfileFields::UserId           => $userId,
                ProfileFields::About            => $pending->{ProfileFields::About},
                ProfileFields::Bio              => $pending->{ProfileFields::Bio},
                ProfileFields::Fluency          => $pending->{ProfileFields::Fluency},
                ProfileFields::Education        => $pending->{ProfileFields::Education},
                ProfileFields::Experience       => $pending->{ProfileFields::Experience},
                ProfileFields::Certifications   => $pending->{ProfileFields::Certifications},
                ProfileFields::Skills           => $pending->{ProfileFields::Skills}
            ];

            if ($existingProfile)
            {
                // Update existing profile
                $existingProfile->update($upsertData);
            }
            else
            {
                // Update the profile
                $existingProfile->create($upsertData);
            }

            // Delete the pending registration record
            $pending->delete();

            // Find the applicant
            $applicant = User::findOrFail($userId);
                        // where('id', $userId)
                        //->select(UserFields::Firstname, UserFields::Lastname, 'email')
                        //->firstOrFail();

            // Update his details to be a tutor
            $applicant->update([
                UserFields::IsVerified => 1,
                UserFields::Role => User::ROLE_TUTOR
            ]);

            $applicantName = implode(' ', [$applicant->{UserFields::Firstname}, $applicant->{UserFields::Lastname}]);

            // Disable AVAST Mail Shield "Outbound SMTP" before sending emails
            $emailData = [
                'firstname' => $applicant->{UserFields::Firstname},
                'login'     => route('login'),
                'logo'      => public_path('assets/img/logo-brand-sm.png')
            ];

            Mail::to($applicant->email)->send(new RegistrationApprovedMail($emailData));

            // Commit the transaction
            DB::commit();

            // Return a success response
            return redirect()
                ->route('admin.tutors-index')
                ->with('registerSuccessMsg', "Registration approved successfully for $applicantName");
        }
        catch (ModelNotFoundException $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.404');
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return view('errors.500');
        }
    }

    public function declineRegistration($id)
    {

    }

    private function getFluencyFilters()
    {
        $fluencyFilter = [];

        foreach (FluencyLevels::Tutor as $key => $obj)
        {
            $fluencyFilter[$key] = $obj['Level'];
        }

        return $fluencyFilter;
    }
}
