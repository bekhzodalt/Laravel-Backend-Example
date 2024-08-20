<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// Models
use App\Models\Admin;
use App\Models\VccCredential;

class AdminController extends Controller
{
    //
    public function loginAdmin(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'email' => 'required|email|string|max:255',
    		'password' => 'required|string|max:255|min:8',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}
        
        $admin = Admin::where('email', $request->email)->first();

        if (!($admin && Hash::check($request->password, $admin->password))) {
        	return response()->json(["status" => 400, "message" => "Invalid credentials."], 200);
        }

        return response()->json(["status" => 200, "data" => $admin, "message" => "Admin has logged in successfully."], 200);
    }

    //
    public function addCredential(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'username' => 'required|string|max:255',
    		'password' => 'required|string|max:255',
    		'is_active' => 'required',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        //
        $loginApi = "https://vcc.visagenet.com/api/Account/JsonLogin";
        $credentials = [
            'UserName' => $request->username,
            'Password' => $request->password
        ];
        //
        $response = Http::post($loginApi, $credentials);
        $headers = $response->headers();
        $cookie = $headers["Set-Cookie"][0];
        // dd('=== login response === ', $cookie);
        if($response["success"]) {
            // get
            $getApi = "https://vcc.visagenet.com/api/VisageSpa/GetSiteLocations";
            // $getParams = [
            //     'theSiteId' => 704,
            //     'theCourseId' => 1
            // ];
            $response1 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->get($getApi, []);
            // $response1 = Http::get($getApi);
            $orgData = $response1->json();
            // dd('===', $orgData[0]);

            //
            $credential = VccCredential::create([
                'username' => $request->username,
                'password' => $request->password,
                'is_active' => ($request->is_active == "true" ? true : false),
                'site_id' => $orgData[0]["Id"],
                'site_name' => $orgData[0]["Name"],
                'center_point_latitude' => $orgData[0]["CenterPointLatitude"],
                'center_point_longitude' => $orgData[0]["CenterPointLongitude"],
            ]);
    
            return response()->json(["status" => 200, "data" => $credential, "message" => "Successfully added."], 200);
        } else {

            return response()->json(["status" => 400, "data" => null, "message" => "Invalid Credential."], 200);
        }
    }

    //
    public function getCredentials(Request $request)
    {
        $credentials = VccCredential::all();

        return response()->json(["status" => 200, "data" => $credentials, "message" => ""], 200);
    }

    //
    public function removeCredential(Request $request)
    {
        $validator = Validator::make($request->all(), [
    		'id' => 'required|max:255',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["status" => 400, "message" => "Required param not passed."], 200);
    	}

        $credential = VccCredential::find($request->id);
        if($credential) {
            $credential->delete();
        }

        return response()->json(["status" => 200, "data" => null, "message" => "Successfully removed."], 200);
    }
}
