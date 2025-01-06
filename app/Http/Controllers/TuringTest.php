<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class TuringTest extends Controller
{
    private function buildCaptcha($returnJson = true)
    {
        $captchaString = $this->randomStr(4);
        $captchaImages = [];

        foreach (str_split($captchaString) as $letter)
        {
            // Use public_path to get the correct file path
            $imagePath = public_path("assets/img/turing_test/{$letter}.png");
            $imageData = base64_encode(file_get_contents($imagePath));
            $captchaImages[] = $imageData;
        }

        $output = [
            'captchaImages' => $captchaImages,
            'captchaText' => base64_encode($captchaString)
        ];

        return $output;
        // if ($returnJson)
        //     return response()->json();

        // return $output;
    }

    public function generateNewCaptcha()
    {
        return response()->json($this->buildCaptcha());
    }

    public function validateCaptcha(Request $request)
    {
        // Validation rules
        $rules = [
            'captchaText' => 'required|string',
            'userInput'   => 'required|string|max:4'
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Incorrect captcha given by user, generate a new one
            $newCaptcha = $this->buildCaptcha();

            return response()->json([
                'status' => '422',
                'message' => 'Validation failed. Incorrect letters. Please try again.',
                'newcaptcha' => $newCaptcha
            ], 422);
        }

        // Validated inputs
        $inputs = $validator->validated();

        // Decode the captcha text
        $decodedCaptchaText = base64_decode($inputs['captchaText'], true);

        if ($decodedCaptchaText === false) {
            return response()->json([
                'status' => '422',
                'message' => 'Invalid base64 encoding.',
            ], 422);
        }

        // Check if the user input matches the decoded captcha text
        if ($inputs['userInput'] === $decodedCaptchaText)
        {
            return response()->json(['status' => '200']);
        }
        else {
            $newCaptcha = $this->buildCaptcha();

            return response()->json([
                'status' => '422',
                'message' => 'Incorrect letters. Please try again.',
                'newcaptcha' => $newCaptcha
            ], 422);
        }
    }

    private function randomStr($length = 4)
    {
        // Ensure the length does not exceed the number of unique letters available
        if ($length > 26) {
            throw new InvalidArgumentException('Length exceeds the number of unique letters available.');
        }

        $pool = 'abcdefghijklmnopqrstuvwxyz';

        // Shuffle the pool and take the first $length characters
        return substr(str_shuffle($pool), 0, $length);
    }
}
