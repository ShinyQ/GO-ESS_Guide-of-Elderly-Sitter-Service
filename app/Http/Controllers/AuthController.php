<?php

namespace App\Http\Controllers;

use App\Sitter;
use App\User;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Api;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $data, $code;

    public function __construct()
    {
        $this->code = 200;
        $this->data = [];
    }

    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'unique:users', 'max:255'],
                'password' => [
                    'required', 'string', 'min:8', 'confirmed', 'min:9',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&.s]/'],
                'phone' => ['required', 'string'],
                'address' => ['required', 'string'],
                'status' => ['required', 'integer'],
            ]);

            if ($validator->fails()) {
                return Api::apiRespond(400, $validator->errors()->all());
            }

            $user = User::create([
                'name' => $request->name,
                'email'=> $request->email,
                'password' => Bcrypt($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status
            ]);

            if ($request->status == 1){
                $this->register_sitter($user->id, $request);
            }

            $this->data = $user;
        } catch (Exception $e) {
            $this->code = 500;
            $this->data = $e;
        }

        return Api::apiRespond($this->code, $this->data);
    }

    public function register_sitter($id, $request){
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'string'],
            'ktp' => ['required', 'string'],
            'description' => ['required', 'string'],
            'education' => ['required', 'string'],
            'skill' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Api::apiRespond(400, $validator->errors()->all());
        }

        Sitter::create([
            'id_user' => $id,
            'photo' => $request->photo,
            'ktp' => $request->ktp,
            'description' => $request->description,
            'education' => $request->education,
            'skill' => $request->skill,
            'is_ready' => 0
        ]);
    }

    public function login(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if ($validator->fails()) {
                return Api::apiRespond(400, $validator->errors()->all());
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $data['user'] = $user;
                $data['token'] = $user->createToken('auth-api-goess')->accessToken;

                if($user->status == 1){
                    $data['sitter'] = Sitter::findOrFail($user->id);
                }

                $this->data = $data;
            } else {
                return Api::apiRespond(401, []);
            }
        } catch (Exception $e) {
            $this->code = 500;
            $this->data = $e;
        }

        return Api::apiRespond($this->code, $this->data);
    }

    public function profile(){
        try {
            $user = auth()->guard('api')->user();

            if ($user->status == 1){
                $data['sitter'] = Sitter::findOrFail($user->id);
            }

            $data['user'] = $user;
            $this->data = $data;
        } catch (Exception $e){
            $this->code = 500;
            $this->data = $e;
        }

        return Api::apiRespond($this->code, $this->data);
    }

    public function logout() {
        try {
            auth()->guard('api')->user()->tokens->each(function($token) {
                $token->delete();
            });
        } catch (Exception $e){
            $this->code = 500;
            $this->data = $e;
        }

        return Api::apiRespond($this->code, $this->data);
    }
}
