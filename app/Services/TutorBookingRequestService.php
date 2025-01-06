<?php

namespace App\Services;

use App\Http\Utils\HashSalts;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\FieldNames\BookingFields;
use App\Models\FieldNames\BookingRequestFields;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TutorBookingRequestService
{
    //private $tutorHashIds;
    private $learnerHashIds;

    function __construct()
    {
        //$this->tutorHashIds   = new Hashids(HashSalts::Tutors, 10);
        $this->learnerHashIds = new Hashids(HashSalts::Learners, 10);
    }

    public function acceptHireRequest(Request $request, $tutorId)
    {
        $rules = [
            'learner-id' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        $error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
        {
            return $error500;
        }

        $hashedLearnerId = $validator->validated()['learner-id'];
        $learnerId = $this->learnerHashIds->decode($hashedLearnerId)[0];

        try
        {
            DB::beginTransaction();

            $bookingReq = BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                ->where(BookingRequestFields::SenderId, $learnerId)
                ->with('sender')
                ->firstOrFail();

            Booking::create([
                BookingFields::LearnerId => $learnerId,
                BookingFields::TutorId => $tutorId
            ]);

            $learnerName = implode(' ', [
                $bookingReq->sender->{UserFields::Firstname},
                $bookingReq->sender->{UserFields::Lastname}
            ]);

            // Delete the request. We no longer need it.
            $bookingReq->deleteOrFail();

            DB::commit();

            session()->flash('booking_request_action', 'accept');
            session()->flash('accept_booking_request', "$learnerName has been successfully added to your learners.");

            return redirect()->route('tutor.hire-requests');
        }
        catch (ModelNotFoundException $ex) {
            DB::rollBack();
            return response()->view('errors.404', [], 404);
        }
        catch (Exception $ex) {
            DB::rollBack();
            return $error500;
        }
    }

    public function declineHireRequest(Request $request, $tutorId)
    {
        $rules = [
            'learner-id' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        $error500  = response()->view('errors.500', [], 500);

        if ($validator->fails())
            return $error500;

        $hashedLearnerId = $validator->validated()['learner-id'];
        $learnerId = $this->learnerHashIds->decode($hashedLearnerId)[0];

        try
        {
            DB::beginTransaction();

            $bookingReq = BookingRequest::where(BookingRequestFields::ReceiverId, $tutorId)
                        ->where(BookingRequestFields::SenderId, $learnerId)
                        ->with('sender')
                        ->firstOrFail();

            $learnerName = implode(' ', [
                $bookingReq->sender->{UserFields::Firstname},
                $bookingReq->sender->{UserFields::Lastname}
            ]);
            $learnerName = $this->toPossessiveName($learnerName);

            // Delete the request. We no longer need it.
            $bookingReq->deleteOrFail();

            DB::commit();

            session()->flash('booking_request_action', 'decline');
            session()->flash('decline_booking_request', "$learnerName request has been declined.");
            return redirect()->route('tutor.hire-requests');
        }
        catch (ModelNotFoundException $ex) {
            DB::rollBack();
            return response()->view('errors.404', [], 404);
        }
        catch (Exception $ex) {
            DB::rollBack();
            return $error500;
        }
    }

    // Add an 's or not. This is depending on the name's last letter.
    function toPossessiveName($name)
    {
        return $name . (substr($name, -1) === 's' ? "'" : "'s");
    }
}
