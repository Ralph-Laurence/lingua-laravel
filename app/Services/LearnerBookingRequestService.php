<?php

namespace App\Services;

use App\Mail\HireTutorRequestMail;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LearnerBookingRequestService
{
    public function hireTutor($learnerId, $tutorId) : int
    {
        if (empty($tutorId) || empty($learnerId))
            return 500;

        try
        {
            $tutor   = User::findOrFail($tutorId);
            $learner = User::findOrFail($learnerId);

            $friendRequest = new BookingRequest();
            $friendRequest->sender()->associate($learner); // learner is the sender
            $friendRequest->receiver()->associate($tutor); // tutor is the reciever
            $friendRequest->save();

            $learnerName = implode(' ', [$learner->{UserFields::Firstname}, $learner->{UserFields::Lastname}]);

            // Disable AVAST Mail Shield "Outbound SMTP" before sending emails
            $emailData = [
                'name'      => $learnerName,
                'action'    => route('tutor.hire-requests'),
                'logo'      => public_path('assets/img/logo-brand-sm.png')
            ];
        
            Mail::to($tutor->email)->send(new HireTutorRequestMail($emailData));

            return 200;
        }
        catch (ModelNotFoundException $ex)
        {
            return 404;
        }
        catch (Exception $ex)
        {
            return 500;
        }
    }

    public function cancelHireTutor($learnerId, $tutorId) : int
    {
        if (empty($tutorId) || empty($learnerId))
        {
            return 500;
        }

        try
        {
            DB::beginTransaction();

            $bookingRequest = BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                ->where(BookingRequestFields::SenderId, Auth::user()->id)
                ->firstOrFail();

            $deleted = $bookingRequest->delete();

            if ($deleted)
            {
                DB::commit();
                return 200;
            }
            else
            {
                DB::rollBack();
                return 500;
            }
        }
        catch (ModelNotFoundException $ex)
        {
            DB::rollBack();
            return 404;
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return 500;
        }
    }
}
