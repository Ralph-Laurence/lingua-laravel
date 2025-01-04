<?php

namespace App\Services;

use App\Models\BookingRequest;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

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
            error_log('empty IDs');
            return 500;
        }

        try
        {
            $bookingRequest = BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                ->where(BookingRequestFields::SenderId, Auth::user()->id)
                ->firstOrFail();
            
            $deleted = $bookingRequest->delete();
            
            if ($deleted)
                return 200;
            else
                return 500;
        }
        catch (ModelNotFoundException $ex)
        {
            return 404;
        }
        catch (Exception $ex)
        {
            error_log('GENERAL ERROR --> ');
            error_log($ex->getMessage());
            return 500;
        }
    }
}