<?php

use App\Http\Controllers\LearnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use App\Models\User;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

$RoleMw = 'role-mw:'; // Role middleware

Route::middleware(['auth', $RoleMw . User::ROLE_ADMIN])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});

Route::middleware(['auth', $RoleMw . User::ROLE_LEARNER])->group(function ()
{
    Route::controller(LearnerController::class)->group(function()
    {
        Route::get('/learner', 'index')->name('index');
        Route::get('/learner/my-tutors', 'myTutors')->name('mytutors');

        // Route::get('/sign-lingua/become-tutor',                 'becomeTutor')->name('become-tutor');
        // Route::get('/sign-lingua/become-tutor/forms',           'becomeTutorFormsPage')->name('become-tutor.forms');
        // Route::get('/sign-lingua/become-tutor/success',         'becomeTutorSuccess')->name('become-tutor.success');
        // Route::post('/sign-lingua/become-tutor/forms/submit',   'becomeTutorOnSubmit')->name('become-tutor.forms.submit');

        Route::get('/sign-lingua/become-tutor',                 'becomeTutor_index')->name('become-tutor');
        Route::get('/sign-lingua/become-tutor/forms',           'becomeTutor_create')->name('become-tutor.forms');
        Route::get('/sign-lingua/become-tutor/success',         'becomeTutor_success')->name('become-tutor.success');
        Route::post('/sign-lingua/become-tutor/forms/submit',   'becomeTutor_store')->name('become-tutor.forms.submit');
    });

    Route::controller(TutorController::class)->group(function()
    {
        Route::get('/learner/find-tutors', 'listTutors')->name('tutors.list');
        Route::get('/learner/tutor-details/{id}', 'show')->name('tutor.show');
        Route::post('/tutor/hire', 'hireTutor')->name('tutor.hire');
        Route::post('/tutor/end-contract', 'endContract')->name('tutor.end');
    });
});

Route::middleware(['auth', $RoleMw . User::ROLE_TUTOR])->group(function ()
{
    Route::get('/tutor', function (){
        return view('tutor.dashboard');
    });
});

Route::get('/generate-password', function () {
    $password = 'Joy_021428';
    $hashedPassword = Hash::make($password);
    echo $hashedPassword;
});

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

Route::get('/send-test-email', function ()
{
    // Disable AVAST Mail Shield "Outbound SMTP"
    Mail::to('bluescreen512@gmail.com')->send(new TestMail());
    return 'Test email sent!';
});

Route::get('/success', function() {
    return view('shared.registration-success');
});
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
