<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\MemberRegistration;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(HomeController::class)->group(function()
{
    Route::get('/', 'index');
    //Route::get('/sign-lingua/become-tutor', 'becomeTutor_index')->name('guest-become-tutor');
    // Route::get('/sign-lingua/register-tutor/forms', 'becomeTutor_create')->name('guest-become-tutor.forms');
});

Route::get('/dashboard', function () {
    // return view('dashboard');
    // return view('shared.common-home-page');

    if (Auth::check())
    {
        $role = Auth::user()->{UserFields::Role};

        switch ($role) {
            case User::ROLE_LEARNER:
            case User::ROLE_STR_TUTOR:
                return redirect()->to('/');
                break;

            case User::ROLE_ADMIN:
                return redirect()->route('admin.dashboard');
                break;
        }
    }

    return redirect()->to('/');

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

$RoleMw = 'role-mw:'; // Role middleware

Route::middleware(['auth', $RoleMw . User::ROLE_ADMIN])->group(function ()
{
    Route::controller(AdminController::class)->group(function()
    {
        Route::get('/admin/dashboard',                          'index')->name('admin.dashboard');
        Route::get('/admin/tutors',                             'tutors_index')->name('admin.tutors-index');
        Route::get('/admin/tutors/filter/clear',                'tutors_clear_filter')->name('admin.tutors-clear-filter');
        Route::get('/admin/tutors/review/{id}',                 'tutors_review_registration')->name('admin.tutors-review-registration');
        Route::get('/admin/tutors/registration/approve/{id}',   'tutors_approve_registration')->name('admin.tutors-approve-registration');
        Route::get('/admin/tutors/registration/decline/{id}',   'tutors_decline_registration')->name('admin.tutors-decline-registration');
        Route::get('/admin/tutors/details/{id}',                'tutors_show')->name('admin.tutors-show');
        Route::post('/admin/tutors/filter',                     'tutors_filter')->name('admin.tutors-filter');

        Route::get('/admin/learners',                           'learners_index')->name('admin.learners-index');
        Route::get('/admin/learners/filter/clear',              'learners_clear_filter')->name('admin.learners-clear-filter');
        Route::get('/admin/learners/details/{id}',              'learners_show')->name('admin.learners-show');
        Route::post('/admin/learners/filter',                   'learners_filter')->name('admin.learners-filter');
    });
});

Route::controller(LearnerController::class)->group(function() use ($RoleMw)
{
    // The default user role is Learner. Pending or unregistered tutors are
    // given a role of "learner", meaning they have access to learner routes.
    // We have to make sure they can't access any learner and tutor routes
    // unless their profile has been verified, thus "ensureNotPending" .
    Route::middleware(['auth', 'ensureNotPending', $RoleMw . User::ROLE_LEARNER])->group(function()
    {
        Route::get('/learner', 'index')->name('learner.index');
        Route::get('/learner/my-tutors', 'myTutors')->name('mytutors');

        Route::get('/sign-lingua/become-tutor',                 'becomeTutor_index')->name('become-tutor');
        Route::get('/sign-lingua/become-tutor/forms',           'becomeTutor_create')->name('become-tutor.forms');
        Route::post('/sign-lingua/become-tutor/forms/submit',   'becomeTutor_store')->name('become-tutor.forms.submit');
    });

    Route::middleware('guest')->group(function()
    {
        // These cant be accessed by an authenticated learner
        // as these should be a guest-only route
        Route::get('/signlingua/learner/register',  'registerLearner_create')->name('learner.register');
        Route::post('/signlingua/learner/register', 'registerLearner_store')->name('learner.register-submit');
    });

    Route::get('/sign-lingua/become-tutor/success', 'becomeTutor_success')->name('become-tutor.success');
});

Route::controller(TutorController::class)->group(function() use ($RoleMw)
{
    Route::middleware(['auth', 'ensureNotPending', $RoleMw . User::ROLE_TUTOR])->group(function()
    {
        Route::get('/learner/find-tutors', 'listTutors')->name('tutors.list');
        Route::get('/learner/tutor-details/{id}', 'show')->name('tutor.show');

        Route::get('/tutor/my-learners',             'myLearners')->name('mylearners');
        Route::get('/tutor/filter/learners/clear',   'learners_clear_filter')->name('tutor.learners-clear-filter');
        Route::get('/tutor/my-learner-details',      'myLearners_show')->name('tutor.learners-show');
        Route::post('/tutor/filter/learners',        'learners_filter')->name('tutor.learners-filter');

        Route::post('/tutor/hire', 'hireTutor')->name('tutor.hire');
        Route::post('/tutor/end-contract', 'endContract')->name('tutor.end');
    });

    Route::middleware('guest', 'ensureNotPending')->group(function()
    {
        // Redirects authenticated learners to /sign-lingua/become-tutor
        // if they try to access tutor registration routes.
        Route::get('/signlingua/tutor/register',  'registerTutor_create')
            ->middleware('learnerToTutor')
            ->name('tutor.register');

        Route::post('/signlingua/tutor/register', 'registerTutor_store')
            ->middleware('learnerToTutor')
            ->name('tutor.register-submit');
    });
});

Route::middleware(['auth', $RoleMw . User::ROLE_TUTOR])->group(function ()
{
    Route::get('/tutor', function (){
        return view('tutor.dashboard');
    });
});

// Route::get('/generate-password', function () {
//     $password = 'Joy_021428';
//     $hashedPassword = Hash::make($password);
//     echo $hashedPassword;
// });

// use App\Mail\TestMail;
// use Illuminate\Support\Facades\Mail;

// Route::get('/send-test-email', function ()
// {
//     // Disable AVAST Mail Shield "Outbound SMTP"
//     Mail::to('bluescreen512@gmail.com')->send(new TestMail());
//     return 'Test email sent!';
// });

// Route::get('/mail', function() {
//     return view('mails.registration-declined');
// });
/*
---------------------------------------------------------------------------
Verb	    URI	                    Typical Method Name	    Route Name
---------------------------------------------------------------------------
GET	        /photos	                index()	                photos.index
GET	        /photos/create	        create()	            photos.create
POST	    /photos	                store()	                photos.store
GET	        /photos/{photo}	        show()	                photos.show
GET	        /photos/{photo}/edit	edit()	                photos.edit
PUT /PATCH	/photos/{photo}	        update()	            photos.update
DELETE	    /photos/{photo}	        destroy()	            photos.destroy
*/
