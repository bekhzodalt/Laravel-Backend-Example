<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
// Model
use App\Models\VccCredential;

class ApiController extends Controller
{
    private $centers = [
        [
            'Hole' => 1,
            'Latitude' => 42.85020298029517,
            'Longitude' => -86.13638164863089,
            'Usage' => 1,
            'X' => 348,
            'Y' => 276,
        ],
        [
            'Hole' => 2,
            'Latitude' => 42.85104860459472,
            'Longitude' => -86.14464695875583,
            'Usage' => 1,
            'X' => 387,
            'Y' => 255,
        ],
        [
            'Hole' => 3,
            'Latitude' => 42.8519920380286,
            'Longitude' => -86.14626417135547,
            'Usage' => 1,
            'X' => 348,
            'Y' => 270,
        ],
        [
            'Hole' => 4,
            'Latitude' => 42.85338776385044,
            'Longitude' => -86.1403528081958,
            'Usage' => 1,
            'X' => 347,
            'Y' => 274,
        ],
        [
            'Hole' => 5,
            'Latitude' => 42.85223577736954,
            'Longitude' => -86.14520579408692,
            'Usage' => 1,
            'X' => 385,
            'Y' => 293,
        ],
        [
            'Hole' => 6,
            'Latitude' => 42.85438619085423,
            'Longitude' => -86.14255676440652,
            'Usage' => 1,
            'X' => 352,
            'Y' => 255,
        ],
        [
            'Hole' => 7,
            'Latitude' => 42.8543256219172,
            'Longitude' => -86.14095365451745,
            'Usage' => 1,
            'X' => 377,
            'Y' => 278,
        ],
        [
            'Hole' => 8,
            'Latitude' => 42.85044211943275,
            'Longitude' => -86.13971509604367,
            'Usage' => 1,
            'X' => 341,
            'Y' => 275,
        ],
        [
            'Hole' => 9,
            'Latitude' => 42.85166973216144,
            'Longitude' => -86.13254309399949,
            'Usage' => 1,
            'X' => 365,
            'Y' => 271,
        ],
        [
            'Hole' => 10,
            'Latitude' => 42.84805919758571,
            'Longitude' => -86.12988377098235,
            'Usage' => 1,
            'X' => 380,
            'Y' => 277,
        ],
        [
            'Hole' => 11,
            'Latitude' => 42.84749047398888,
            'Longitude' => -86.12499386149057,
            'Usage' => 1,
            'X' => 368,
            'Y' => 275,
        ],
        [
            'Hole' => 12,
            'Latitude' => 42.85103014439837,
            'Longitude' => -86.12250610430165,
            'Usage' => 1,
            'X' => 374,
            'Y' => 264,
        ],
        [
            'Hole' => 13,
            'Latitude' => 42.85162358075111,
            'Longitude' => -86.12180771688156,
            'Usage' => 1,
            'X' => 338,
            'Y' => 253,
        ],
        [
            'Hole' => 14,
            'Latitude' => 42.847972995437864,
            'Longitude' => -86.12305412132396,
            'Usage' => 1,
            'X' => 354,
            'Y' => 279,
        ],
        [
            'Hole' => 15,
            'Latitude' => 42.8512859580338,
            'Longitude' => -86.12649451736819,
            'Usage' => 1,
            'X' => 346,
            'Y' => 272,
        ],
        [
            'Hole' => 16,
            'Latitude' => 42.84829376296919,
            'Longitude' => -86.12622259083498,
            'Usage' => 1,
            'X' => 364,
            'Y' => 273,
        ],
        [
            'Hole' => 17,
            'Latitude' => 42.84816987639959,
            'Longitude' => -86.1288551054399,
            'Usage' => 1,
            'X' => 357,
            'Y' => 281,
        ],
        [
            'Hole' => 18,
            'Latitude' => 42.85176714864015,
            'Longitude' => -86.1315712338331,
            'Usage' => 1,
            'X' => 341,
            'Y' => 271,
        ]
    ];
    /********************************************************
     * @ SaveCourseRotationSets
     * @ params (pin_number, rotation, latitude, longitude)
    ********************************************************/
    public function SaveCourseRotationSets(Request $request) // Macatawa Golf Club
    {
        //
        $validator = Validator::make($request->all(), [
            'rotation' => 'required',
            'pin_number' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        $pinNumber = $request->pin_number;
        $rotationNumber = $request->rotation;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $up = $request->up ? $request->up : 0;
        $down = $request->down ? $request->down : 0;
        $left = $request->left ? $request->left : 0;
        $right = $request->right ? $request->right : 0;
        //
        $loginApi = "https://vcc.visagenet.com/api/Account/JsonLogin";
        $credentials = [
            'UserName' => 'macatawalegends',
            'Password' => 'Mac2021!'
        ];
        //
        $response = Http::post($loginApi, $credentials);
        $headers = $response->headers();
        $cookie = $headers["Set-Cookie"][0];
        // dd('=== login response === ', $cookie);
        if($response["success"]) {
            // get
            $getApi = "https://vcc.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
            $getParams = [
                'theSiteId' => 704,
                'theCourseId' => 1
            ];
            $response1 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->get($getApi, $getParams);
            // $response1 = Http::get($getApi);
            $orgData = $response1->json();
            // dd('=== get response === ', $orgData, $orgData["RotationSets"][0]["Rotations"][0]["RotationNumber"]);
            // save
            $saveApi = "https://vcc.visagenet.com/api/VisageSpa/SaveCourseRotationSets";
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                    $PinPlacementsArr = [];
                    foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                        // dd('===', $iindex, count($ApproachView["PinPlacements"]));
                        if ($iindex < count($ApproachView["PinPlacements"])) {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                            ];
                            // updated param
                            if($Rotation["RotationNumber"] == $rotationNumber && $ApproachView["Hole"] == $pinNumber) {
                                $_PinPlacement = [
                                    "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                    "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                    "Hole" => $ApproachView["Hole"],
                                    "Latitude" => $latitude, // input param
                                    "Longitude" => $longitude, // input param
                                    "X" => $this->calcX($iiindex, $up, $down, $left, $right),
                                    "Y" => $this->calcY($iiindex, $up, $down, $left, $right)
                                    // "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                    // "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                    // "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                    // "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                                ];
                                // dd('===', $Rotation["RotationNumber"], $ApproachView["Hole"], $_PinPlacement);
                            }
                        } else {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][0]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => 0, // input param
                                "Longitude" => 0, // input param
                                "X" => 0,
                                "Y" => 0
                            ];
                        }
                        array_push($PinPlacementsArr, $_PinPlacement);
                    }
                    $_Rotation = [
                        "RotationNumber" => $Rotation["RotationNumber"],
                        "RotationType" => $Rotation["RotationType"],
                        "PinPlacements" => $PinPlacementsArr
                    ];
                    array_push($RotationsArr, $_Rotation);
                }
                //
                $_RotationSet = [
                    "Course" => $orgData["Course"],
                    "CurrentRotation" => $orgData["RotationSets"][0]["CurrentRotation"],
                    "FirstHole" => $orgData["RotationSets"][0]["FirstHole"],
                    "IsActive" => $orgData["RotationSets"][0]["ActiveSet"],
                    "LastHole" => $orgData["RotationSets"][0]["LastHole"],
                    "Name" => $orgData["RotationSets"][0]["Name"],
                    "RotationSetType" => $orgData["RotationSets"][0]["RotationSetType"],
                    "PinCharacter" => $orgData["RotationSets"][0]["UserFigure"],
                    "Rotations" => $RotationsArr
                ];
                array_push($RotationSetsArr, $_RotationSet);
            }
            
            //
            $params = [
                "SiteId" => 704,
                "CourseRotationSetsDto" => [
                    "Version" => $orgData["Version"],
                    "Course" => $orgData["Course"],
                    "IsOneDayOverride" => $orgData["IsOneDayOverride"],
                    "RotationSets" => $RotationSetsArr
                ],
                "Log" => "Move pins"
            ];
            // dd("=== generated params === ", $orgData, $params);
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->post($saveApi, $params);

            // $response2 = Http::withBody(
            //     json_encode($params), 'application/json'
            // )->post($saveApi);
            // dd('== Response == ', $response2->status(), $response2->ok());
            return response()->json(['result' => $response2->json(), 'success' => $response2->successful(), 'status' => $response2->status()]);
        } else {
            return response()->json(['message' => 'An error has occured', 'success' => false, 'status' => 400]);
        }
    }

    private function calcX($index, $up, $down, $left, $right) {
        $unitsLeft = [5.8, 6.2, 5.6, 6.5, 6.5, 6.8, 5.8, 6.2, 4.8, 6.2, 6, 6.4, 5.7, 5.7, 5.4, 7, 5.6, 6.8];
        $unitsRight = [6, 6.5, 5.6, 6.8, 6.5, 6.6, 5.3, 6.1, 4.6, 6.2, 6, 6.4, 5.8, 5.9, 5.2, 6.9, 5.6, 6.4];
        $unit = $left ? $unitsLeft[$index] : $unitsRight[$index];
        $orgX = $this->centers[$index]['X'];
        return round($orgX - ($left * $unit) + ($right * $unit));
    }

    private function calcY($index, $up, $down, $left, $right) {
        $unitsUp = [6.5, 6.2, 6.1, 5.2, 5.8, 5.2, 5.4, 6.2, 5.4, 5.6, 5.7, 7.4, 6.2, 5.2, 5.4, 6.6, 6.6, 6.4];
        $unitsDown = [6.6, 5.8, 6.2, 5.0, 5.6, 5.3, 5.5, 6.3, 5.45, 5.75, 5.8, 7.2, 6.65, 5.1, 5.4, 6.6, 6.6, 6.5];
        $unit = $up ? $unitsUp[$index] : $unitsDown[$index];
        $orgY = $this->centers[$index]['Y'];
        return round($orgY - ($up * $unit) + ($down * $unit));
    }

    /********************************************************
     * @ SaveCourseRotationSets
     * @ params (course_name, pin_number, rotation, latitude, longitude)
    ********************************************************/
    public function SaveCourseRotationSetsByClub(Request $request) // All Clubs
    {
        //
        $validator = Validator::make($request->all(), [
            'course_name' => 'required',
            'rotation' => 'required',
            'pin_number' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => "Required params not passed.", 'status' => 400]);
        }
        $courseName = $request->course_name;
        $pinNumber = $request->pin_number;
        $rotationNumber = $request->rotation;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $up = $request->up ? $request->up : 0;
        $down = $request->down ? $request->down : 0;
        $left = $request->left ? $request->left : 0;
        $right = $request->right ? $request->right : 0;
        //
        $loginApi = "https://vcc.visagenet.com/api/Account/JsonLogin";
        $cred = VccCredential::where('site_name', $courseName)->first();
        if(!$cred) {
            return response()->json(['message' => "There is no credential matched with the same club name.", 'status' => 400]);
        }
        $credentials = [
            'UserName' => $cred ? $cred->username : '',
            'Password' => $cred ? $cred->password : ''
        ];
        //
        $response = Http::post($loginApi, $credentials);
        $headers = $response->headers();
        $cookie = $headers["Set-Cookie"][0];
        // dd('=== login response === ', $cookie);
        if($response["success"]) {
            // get
            $getApi = "https://vcc.visagenet.com/api/VisageSpa/GreensWithPinPlacements";
            $getParams = [
                'theSiteId' => $cred->site_id,
                'theCourseId' => 1
            ];
            $response1 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->get($getApi, $getParams);
            // $response1 = Http::get($getApi);
            $orgData = $response1->json();
            // dd('=== get response === ', $orgData, $orgData["RotationSets"][0]["Rotations"][0]["RotationNumber"]);
            // save
            $saveApi = "https://vcc.visagenet.com/api/VisageSpa/SaveCourseRotationSets";
            // Rotations
            $RotationSets = $orgData["RotationSets"]; // size = 1
            $RotationSetsArr = [];
            $ApproachViews = $orgData["ApproachViews"]; // size = 18
            foreach($RotationSets as $index => $RotationsSet) {
                // 
                $Rotations = $RotationsSet["Rotations"]; // size = 3
                $RotationsArr = [];
                foreach($Rotations as $iindex => $Rotation) { // *********************** rotation
                    $PinPlacementsArr = [];
                    foreach($ApproachViews as $iiindex => $ApproachView) { // *********************** pin_number * hole
                        // dd('===', $iindex, count($ApproachView["PinPlacements"]));
                        if ($iindex < count($ApproachView["PinPlacements"])) {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                            ];
                            // updated param
                            if($Rotation["RotationNumber"] == $rotationNumber && $ApproachView["Hole"] == $pinNumber) {
                                $_PinPlacement = [
                                    "Usage" => $ApproachView["PinPlacements"][$iindex]["Usage"],
                                    "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                    "Hole" => $ApproachView["Hole"],
                                    "Latitude" => $latitude, // input param
                                    "Longitude" => $longitude, // input param
                                    "X" => $this->calcX($iiindex, $up, $down, $left, $right),
                                    "Y" => $this->calcY($iiindex, $up, $down, $left, $right)
                                    // "Latitude" => $ApproachView["PinPlacements"][$iindex]["Latitude"], // input param
                                    // "Longitude" => $ApproachView["PinPlacements"][$iindex]["Longitude"], // input param
                                    // "X" => $ApproachView["PinPlacements"][$iindex]["X"],
                                    // "Y" => $ApproachView["PinPlacements"][$iindex]["Y"]
                                ];
                                // dd('===', $Rotation["RotationNumber"], $ApproachView["Hole"], $_PinPlacement);
                            }
                        } else {
                            $_PinPlacement = [
                                "Usage" => $ApproachView["PinPlacements"][0]["Usage"],
                                "DateCreatedInUtc" => $orgData["RotationLastEvaluationInUtc"], // date('c')
                                "Hole" => $ApproachView["Hole"],
                                "Latitude" => 0, // input param
                                "Longitude" => 0, // input param
                                "X" => 0,
                                "Y" => 0
                            ];
                        }
                        array_push($PinPlacementsArr, $_PinPlacement);
                    }
                    $_Rotation = [
                        "RotationNumber" => $Rotation["RotationNumber"],
                        "RotationType" => $Rotation["RotationType"],
                        "PinPlacements" => $PinPlacementsArr
                    ];
                    array_push($RotationsArr, $_Rotation);
                }
                //
                $_RotationSet = [
                    "Course" => $orgData["Course"],
                    "CurrentRotation" => $orgData["RotationSets"][0]["CurrentRotation"],
                    "FirstHole" => $orgData["RotationSets"][0]["FirstHole"],
                    "IsActive" => $orgData["RotationSets"][0]["ActiveSet"],
                    "LastHole" => $orgData["RotationSets"][0]["LastHole"],
                    "Name" => $orgData["RotationSets"][0]["Name"],
                    "RotationSetType" => $orgData["RotationSets"][0]["RotationSetType"],
                    "PinCharacter" => $orgData["RotationSets"][0]["UserFigure"],
                    "Rotations" => $RotationsArr
                ];
                array_push($RotationSetsArr, $_RotationSet);
            }
            
            //
            $params = [
                "SiteId" => $cred->site_id,
                "CourseRotationSetsDto" => [
                    "Version" => $orgData["Version"],
                    "Course" => $orgData["Course"],
                    "IsOneDayOverride" => $orgData["IsOneDayOverride"],
                    "RotationSets" => $RotationSetsArr
                ],
                "Log" => "Move pins"
            ];
            // dd("=== generated params === ", $orgData, $params);
            $response2 = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Cookie' => $cookie
            ])->post($saveApi, $params);

            // $response2 = Http::withBody(
            //     json_encode($params), 'application/json'
            // )->post($saveApi);
            // dd('== Response == ', $response2->status(), $response2->ok());
            return response()->json(['result' => $response2->json(), 'success' => $response2->successful(), 'status' => $response2->status()]);
        } else {
            return response()->json(['message' => 'An error has occured', 'success' => false, 'status' => 400]);
        }
    }

}


















// $params = [
//     "SiteId" => 704,
//     "CourseRotationSetsDto" => [
//         "Version" => $orgData["Version"],
//         "Course" => $orgData["Course"],
//         "IsOneDayOverride" => false,
//         "RotationSets" => [
//             [
//                 "Course" => $orgData["Course"],
//                 "CurrentRotation" => $orgData["RotationSets"][0]["CurrentRotation"],
//                 "FirstHole" => $orgData["RotationSets"][0]["FirstHole"],
//                 "IsActive" => $orgData["RotationSets"][0]["ActiveSet"],
//                 "LastHole" => $orgData["RotationSets"][0]["LastHole"],
//                 "Name" => $orgData["RotationSets"][0]["Name"],
//                 "RotationSetType" => $orgData["RotationSets"][0]["RotationSetType"],
//                 "PinCharacter" => $orgData["RotationSets"][0]["UserFigure"],
//                 "Rotations" => [
//                     [
//                         "RotationNumber" => $orgData["RotationSets"][0]["Rotations"][0]["RotationNumber"],
//                         "RotationType" => $orgData["RotationSets"][0]["Rotations"][0]["RotationType"],
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Usage"],
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => $orgData["ApproachViews"][0]["Hole"],
//                                 "Latitude" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Latitude"],
//                                 "Longitude" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Longitude"],
//                                 "X" => $orgData["ApproachViews"][0]["PinPlacements"][0]["X"],
//                                 "Y" => $orgData["ApproachViews"][0]["PinPlacements"][0]["Y"]
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 42.851054269743926,
//                                 "Longitude" => -86.14472478708794,
//                                 "X" => 376,
//                                 "Y" => 213
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 42.851908628373195,
//                                 "Longitude" => -86.14625937458885,
//                                 "X" => 323,
//                                 "Y" => 320
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 42.8535067984858,
//                                 "Longitude" => -86.14031553653507,
//                                 "X" => 284,
//                                 "Y" => 225
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.8522450592953,
//                                 "Longitude" => -86.14521380601002,
//                                 "X" => 394,
//                                 "Y" => 291
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.85441547017598,
//                                 "Longitude" => -86.14263659526954,
//                                 "X" => 296,
//                                 "Y" => 278
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.85435938904458,
//                                 "Longitude" => -86.14109725974502,
//                                 "X" => 423,
//                                 "Y" => 351
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85042132994082,
//                                 "Longitude" => -86.13986847197643,
//                                 "X" => 421,
//                                 "Y" => 217
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 42.85158007809387,
//                                 "Longitude" => -86.1325496345279,
//                                 "X" => 390,
//                                 "Y" => 316
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 42.84803030050658,
//                                 "Longitude" => -86.12991751694935,
//                                 "X" => 399,
//                                 "Y" => 256
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 42.847486339899845,
//                                 "Longitude" => -86.12502247041586,
//                                 "X" => 372,
//                                 "Y" => 290
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 42.85095386266865,
//                                 "Longitude" => -86.12254112415496,
//                                 "X" => 385,
//                                 "Y" => 331
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 42.85166654646634,
//                                 "Longitude" => -86.12189164491792,
//                                 "X" => 305,
//                                 "Y" => 301
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 42.84804815270008,
//                                 "Longitude" => -86.12297887338894,
//                                 "X" => 330,
//                                 "Y" => 338
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 42.85117517848783,
//                                 "Longitude" => -86.12655281632405,
//                                 "X" => 293,
//                                 "Y" => 315
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 42.848348829526365,
//                                 "Longitude" => -86.12625697833006,
//                                 "X" => 368,
//                                 "Y" => 322
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 42.84825381776732,
//                                 "Longitude" => -86.1288216658763,
//                                 "X" => 406,
//                                 "Y" => 314
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 42.85171585246361,
//                                 "Longitude" => -86.13155493286652,
//                                 "X" => 345,
//                                 "Y" => 312
//                             ]
//                         ]
//                     ],
//                     [
//                         "RotationNumber" => 2,
//                         "RotationType" => 1,
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 1,
//                                 "Latitude" => 42.850287710918735,
//                                 "Longitude" => -86.13639790894676,
//                                 "X" => 411,
//                                 "Y" => 277
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.85232065086547,
//                                 "Longitude" => -86.14531436305793,
//                                 "X" => 472,
//                                 "Y" => 250
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.85442557414013,
//                                 "Longitude" => -86.142391601058,
//                                 "X" => 412,
//                                 "Y" => 170
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.854136614564126,
//                                 "Longitude" => -86.14093219246436,
//                                 "X" => 445,
//                                 "Y" => 189
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.423Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85053695408541,
//                                 "Longitude" => -86.13967077834616,
//                                 "X" => 337,
//                                 "Y" => 353
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "1970-01-01T00:00:00.000Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 0,
//                                 "Longitude" => 0,
//                                 "X" => 0,
//                                 "Y" => 0
//                             ]
//                         ]
//                     ],
//                     [
//                         "RotationNumber" => 3,
//                         "RotationType" => 1,
//                         "PinPlacements" => [
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 1,
//                                 "Latitude" => 42.850249524892654,
//                                 "Longitude" => -86.13631449229577,
//                                 "X" => 371,
//                                 "Y" => 321
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 2,
//                                 "Latitude" => 42.851107396451546,
//                                 "Longitude" => -86.14457862278257,
//                                 "X" => 443,
//                                 "Y" => 283
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 3,
//                                 "Latitude" => 42.85189783451225,
//                                 "Longitude" => -86.1463443224249,
//                                 "X" => 281,
//                                 "Y" => 292
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 4,
//                                 "Latitude" => 42.85328381874039,
//                                 "Longitude" => -86.14032427880124,
//                                 "X" => 426,
//                                 "Y" => 289
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 5,
//                                 "Latitude" => 42.8522776089235,
//                                 "Longitude" => -86.14522252765661,
//                                 "X" => 420,
//                                 "Y" => 291
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 6,
//                                 "Latitude" => 42.854386343713195,
//                                 "Longitude" => -86.14255580369125,
//                                 "X" => 352,
//                                 "Y" => 255
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 7,
//                                 "Latitude" => 42.85437050567476,
//                                 "Longitude" => -86.14104501072781,
//                                 "X" => 397,
//                                 "Y" => 334
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 8,
//                                 "Latitude" => 42.85050143701418,
//                                 "Longitude" => -86.13970997706836,
//                                 "X" => 351,
//                                 "Y" => 317
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 9,
//                                 "Latitude" => 42.85151289832456,
//                                 "Longitude" => -86.13258330924958,
//                                 "X" => 398,
//                                 "Y" => 360
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 10,
//                                 "Latitude" => 42.848015303615696,
//                                 "Longitude" => -86.12992460875027,
//                                 "X" => 403,
//                                 "Y" => 246
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 11,
//                                 "Latitude" => 42.84743732979225,
//                                 "Longitude" => -86.12499983476067,
//                                 "X" => 407,
//                                 "Y" => 277
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 12,
//                                 "Latitude" => 42.85108291263087,
//                                 "Longitude" => -86.1224468754411,
//                                 "X" => 385,
//                                 "Y" => 200
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 13,
//                                 "Latitude" => 42.851731613563054,
//                                 "Longitude" => -86.1217702854076,
//                                 "X" => 264,
//                                 "Y" => 228
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 14,
//                                 "Latitude" => 42.84799151252665,
//                                 "Longitude" => -86.12299127412156,
//                                 "X" => 325,
//                                 "Y" => 303
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 15,
//                                 "Latitude" => 42.851164691701264,
//                                 "Longitude" => -86.12652120636257,
//                                 "X" => 304,
//                                 "Y" => 330
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 16,
//                                 "Latitude" => 42.84831546846331,
//                                 "Longitude" => -86.12621895123206,
//                                 "X" => 355,
//                                 "Y" => 286
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 17,
//                                 "Latitude" => 42.84809691651889,
//                                 "Longitude" => -86.12897306638011,
//                                 "X" => 331,
//                                 "Y" => 201
//                             ],
//                             [
//                                 "Usage" => 1,
//                                 "DateCreatedInUtc" => "2021-04-10T18:16:18.424Z",
//                                 "Hole" => 18,
//                                 "Latitude" => 42.851820158092885,
//                                 "Longitude" => -86.1315370297481,
//                                 "X" => 368,
//                                 "Y" => 236
//                             ]
//                         ]
//                     ]
//                 ]
//             ]
//         ]
//     ],
//     "Log" => "Move pins"
// ];