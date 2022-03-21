<?php

namespace App\Http\Controllers;

use App\CoachNotification;
use App\Day;
use App\Hole;
use App\Notification;
use App\Reservation_goal;
use App\Reservation_option;
use App\UserNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Coach_booking_detail;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Coach_time_work;
use App\User_caoch_ask;
use App\Coach_booking;
use App\Reservation;
use App\Coach_media;
use Carbon\Carbon;
use App\Favorite;
use App\Coach;
use App\Rate;
use App\User;
use Cloudinary;

class CoachesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['notifications', 'media_filter', 'rates', 'subscriber_user_info', 'subscriber_bill', 'register', 'update_time', 'delete_time', 'store_time', 'times', 'subscribers', 'make_common', 'delete_media', 'media', 'store_media', 'store_plan_detail', 'select_plan_data', 'all_coaches', 'details', 'login', 'delete_plan', 'update_plan', 'update_coach_data', 'delete_plan_detail', 'my_data', 'my_plans', 'plan_details', 'store_plan']]);

        //        --------------------------------------------- begin scheduled functions --------------------------------------------------------
        $expired = Reservation::where('status', 'start')->whereDate('expire_date', '<', Carbon::now())->get();
        foreach ($expired as $row) {
            $product = Reservation::find($row->id);
            $product->status = 'ended';
            $product->save();
        }
        //        --------------------------------------------- end scheduled functions --------------------------------------------------------

    }

    public function all_coaches(Request $request)
    {
        $lang = $request->lang;
        $user = auth()->user();
        if ($lang == 'ar') {
            $coaches = Coach::select('id', 'name', 'rate', 'image', 'about_coach', 'about_coach_en')
                ->where('deleted', '0')
                ->where('status', 'active')
                ->where('is_confirm', 'accepted')
                ->get()
                ->map(function ($coaches) use ($user, $lang) {
                    if ($user == null) {
                        $coaches->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $coaches->id)->where('type', 'coach')->first();
                        if ($fav == null) {
                            $coaches->favorite = false;
                        } else {
                            $coaches->favorite = true;
                        }
                    }
                    $plan_count = Coach_booking::where('coach_id', $coaches->id)->where('deleted', '0')->get()->count();
                    if ($lang == 'ar') {
                        $coaches->description = ' خطط وباقات ' . $plan_count;
                    } else {
                        $coaches->description = $plan_count . ' plans and packages';
                    }
                    return $coaches;
                });
        } else {
            $coaches = Coach::select('id', 'name_en as name', 'rate', 'image', 'about_coach', 'about_coach_en')
                ->where('deleted', '0')
                ->where('status', 'active')
                ->where('is_confirm', 'accepted')
                ->get()
                ->map(function ($coaches) use ($user, $lang) {
                    if ($user == null) {
                        $coaches->favorite = false;
                    } else {
                        $fav = Favorite::where('user_id', $user->id)->where('product_id', $coaches->id)->where('type', 'coach')->first();
                        if ($fav == null) {
                            $coaches->favorite = false;
                        } else {
                            $coaches->favorite = true;
                        }
                    }
                    $plan_count = Coach_booking::where('coach_id', $coaches->id)->where('deleted', '0')->get()->count();
                    if ($lang == 'ar') {
                        $coaches->description = ' خطط وباقات ' . $plan_count;
                    } else {
                        $coaches->description = $plan_count . ' plans and packages';
                    }
                    return $coaches;
                });
        }

        //expict favorite and plans num ...
        $response = APIHelpers::createApiResponse(false, 200, '', '', $coaches, $request->lang);
        return response()->json($response, 200);
    }

    public function details(Request $request, $id)
    {
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $user = auth()->user();
        if ($lang == 'ar') {
            $coach = Coach::select('id', 'image', 'thumbnail', 'name', 'about_coach', 'story', 'rate', 'work_day_from', 'work_day_to')->with(['Day_from', 'Day_to'])->find($id);
        } else {
            $coach = Coach::select('id', 'image', 'name_en', 'thumbnail', 'story', 'about_coach_en as about_coach', 'rate', 'work_day_from', 'work_day_to')->with(['Day_from', 'Day_to'])->find($id);
        }
        $coach['plan_count'] = Coach_booking::where('coach_id', $id)->where('deleted', '0')->get()->count();
        if ($coach != null) {
            if ($user == null) {
                $data['favorite'] = false;
                $data['free_ask'] = false;
                $data['payed_ask'] = false;
                $booking_num = Coach_booking::where('coach_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get()
                    ->count();
                $data['plans_num'] = $booking_num;
            } else {
                $fav = Favorite::where('user_id', $user->id)->where('product_id', $id)->where('type', 'coach')->first();
                if ($fav == null) {
                    $data['favorite'] = false;
                } else {
                    $data['favorite'] = true;
                }
                //if user have free ask
                $free_ask = User_caoch_ask::where('user_id', $user->id)->where('caoch_id', $id)->first();
                if ($free_ask->ask_num_free > 0) {
                    $data['free_ask'] = true;
                } else {
                    $data['free_ask'] = false;
                }
                $booking_ids = Coach_booking::where('coach_id', $id)->select('id')->get()->toArray();
                $pay_ask = Reservation::where('user_id', $user->id)
                    ->whereIn('booking_id', $booking_ids)
                    ->where('type', 'coach')
                    ->where('status', 'start')
                    ->get();
                if (count($pay_ask) > 0) {
                    $data['payed_ask'] = true;
                } else {
                    $data['payed_ask'] = false;
                }
            }
            $data['basic'] = $coach;
            $data['work_times'] = Coach_time_work::select('id', 'time_from', 'time_to')
                ->where('coach_id', $id)
                ->get();
            $data['media'] = Coach_media::select('id', 'image', 'type')
                ->where('coach_id', $id)
                ->get();
//                ->map(function ($media) {
//                    if ($media->type == 'video') {
//                        $media->image = env('APP_URL') . '/public/uploads/coach_media' . $media->image;
//                    }
//                    return $media;
//
//                });
            $rates_one = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 1)->get()->count();
            $rates_tow = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 2)->get()->count();
            $rates_three = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 3)->get()->count();
            $rates_four = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 4)->get()->count();
            $rates_five = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 5)->get()->count();
            $data['stars_count']['one'] = $rates_one;
            $data['stars_count']['tow'] = $rates_tow;
            $data['stars_count']['three'] = $rates_three;
            $data['stars_count']['four'] = $rates_four;
            $data['stars_count']['five'] = $rates_five;
            $data['all_rates'] = Rate::select('text', 'rate', 'user_id as user_name', 'created_at')->where('type', 'coach')
                ->where('order_id', $id)
                ->where('admin_approval', 1)
                ->take(5)->get()
                ->map(function ($rate) use ($lang) {
                    $user = User::where('id', $rate->user_name)->first();
                    $rate->user = $user->name;
//                $rate->created_at = APIHelpers::get_month_year($rate->created_at, $lang);
                    return $rate;
                });
            $data['rates_count'] = count($data['all_rates']);

            if ($lang == 'ar') {
                $data['reservations'] = Coach_booking::with('Details')
                    ->select('id', 'name_ar as name', 'title_ar as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                    ->where('coach_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get();
            } else {
                $data['reservations'] = Coach_booking::with('Details')
                    ->select('id', 'name_en as name', 'title_en as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                    ->where('coach_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get();
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

//    coach-panel ------------------------------------------------------------------
    public function login(Request $request)
    {
        $credentials = request(['phone', 'password']);
        $input = $request->all();
        $validate = Validator::make($input, [
            'phone' => 'required',
            'password' => 'required',
        ]);
        if (!is_array($validate)) {
            if (Auth::guard('coach')->attempt($credentials)) {
                $user = auth()->guard('coach')->user();
                $user->fcm_token = $request->fcm_token;
                $user->save();

                if ($user->is_confirm == 'new') {
                    $response = APIHelpers::createApiResponse(true, 406, 'please wait confirmation your account', 'يرجى انتظار موافقه الادارة', null, $request->lang);
                    return response()->json($response, 406);
                }
                if ($user->is_confirm == 'rejected') {
                    $response = APIHelpers::createApiResponse(true, 406, 'your account rejected', 'قد تم رفض حسابك', null, $request->lang);
                    return response()->json($response, 406);
                }
                $token = auth()->login($user);
                $user->token = $this->respondWithToken($token);
                $response = APIHelpers::createApiResponse(false, 200, 'login successfully', 'تم تسجيل الدخول', $user, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'Missing Required Fields', 'بعض الحقول مفقودة', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function rates(Request $request)
    {
        $lang = $request->lang;
        $id = auth()->guard('coach')->user()->id;
        $rates = Rate::select('text', 'rate', 'user_id as user_name', 'created_at')->where('type', 'coach')
            ->where('order_id', $id)
            ->where('admin_approval', 1)
            ->get()
            ->map(function ($rate) use ($lang) {
                $user = User::where('id', $rate->user_name)->first();
                $rate->user = $user->name;
//                $rate->created_at = APIHelpers::get_month_year($rate->created_at, $lang);
                return $rate;
            });
        $rates_count = count($rates);
        $rate = Coach::whereId($id)->first()->rate;


        $rates_one = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 1)->get()->count();
        $rates_tow = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 2)->get()->count();
        $rates_three = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 3)->get()->count();
        $rates_four = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 4)->get()->count();
        $rates_five = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 5)->get()->count();

        $stars_count['one'] = $rates_one;
        $stars_count['tow'] = $rates_tow;
        $stars_count['three'] = $rates_three;
        $stars_count['four'] = $rates_four;
        $stars_count['five'] = $rates_five;

        $response = APIHelpers::createApiResponse(false, 200, '', '', array('rate' => $rate, 'stars_count' => $stars_count, 'rates_count' => $rates_count, 'rates' => $rates), $request->lang);
        return response()->json($response, 200);
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'name_en' => 'required',
            "age" => "required",
            "exp" => "required",
            "about_coach" => "",
            "about_coach_en" => "",
            "gender" => "required",
            "image" => "required",
            "email" => 'required|email|unique:coaches,email',
            "phone" => 'required|unique:coaches,phone',
            "password" => 'required',
            "fcm_token" => 'required',
            "type" => "required", // 1 -> iphone , 2 -> android
            "unique_id" => "required",
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }
        if ($request->password != null) {
            $input['password'] = Hash::make($request->password);
            $input['is_confirm'] = 'new';
            $input['sort'] = 10000;
            if ($request->image != null) {
                $image = $request->image;
                
                $imagereturned = Cloudinary::upload("data:image/jpeg;base64," . $image);
                $image_id = $imagereturned->getPublicId();
                $image_format = $imagereturned->getExtension();
                $image_new_name = $image_id . '.' . $image_format;
                $input['image'] = $image_new_name;
            }
            $coach = Coach::create($input);

            // add free coach ask to every user
            $users = User::all();
            foreach ($users as $row) {
                $data['user_id'] = $row->id;
                $data['caoch_id'] = $coach->id;
                User_caoch_ask::create($data);
            }
            $coach = Coach::find($coach->id);

        }

        $response = APIHelpers::createApiResponse(false, 200, 'Registered success wait admin to accept your account', 'تم انشاء الحساب ويرجى الانتظار لموافقه الادارة', $coach, $request->lang);
        return response()->json($response, 200);
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 432000
        ];
    }

    //update my profile ------------------------
    public function my_data(Request $request)
    {
        $user = auth()->guard('coach')->user();
        $id = $user->id;
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $data['week_days'] = Day::select('id', 'name_' . $lang . ' as name')->get();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $coach = Coach::with(['Day_from', 'Day_to'])
                ->select('id', 'image', 'thumbnail', 'name', 'name_en', 'about_coach', 'about_coach_en',
                    'story', 'rate', 'work_day_from', 'work_day_to')->find($id);
            if ($coach != null) {
                $data['basic'] = $coach;
                $booking_num = Coach_booking::where('coach_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get()
                    ->count();
                $data['plans_num'] = $booking_num;
                $data['work_times'] = Coach_time_work::select('id', 'time_from', 'time_to')
                    ->where('coach_id', $id)
                    ->first();
                $data['media_image'] = Coach_media::select('id', 'image', 'type')
                    ->where('coach_id', $id)->where('type', 'image')
                    ->get();
                $data['media_video'] = Coach_media::select('id', 'image', 'type')
                    ->where('coach_id', $id)->where('type', 'video')
                    ->get();
                $rates_one = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 1)->get()->count();
                $rates_tow = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 2)->get()->count();
                $rates_three = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 3)->get()->count();
                $rates_four = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 4)->get()->count();
                $rates_five = Rate::where('type', 'coach')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 5)->get()->count();
                $data['stars_count']['one'] = $rates_one;
                $data['stars_count']['tow'] = $rates_tow;
                $data['stars_count']['three'] = $rates_three;
                $data['stars_count']['four'] = $rates_four;
                $data['stars_count']['five'] = $rates_five;
                $data['all_rates'] = Rate::select('text', 'rate', 'user_id as user_name', 'created_at')->where('type', 'coach')
                    ->where('order_id', $id)
                    ->where('admin_approval', 1)
                    ->take(5)->get()
                    ->map(function ($rate) use ($lang) {
                        $user = User::where('id', $rate->user_name)->first();
                        $rate->user = $user->name;
//                $rate->created_at = APIHelpers::get_month_year($rate->created_at, $lang);
                        return $rate;
                    });
                $data['rates_count'] = count($data['all_rates']);

                if ($lang == 'ar') {
                    $data['reservations'] = Coach_booking::with('Details')
                        ->select('id', 'name_ar as name', 'title_ar as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                        ->where('coach_id', $id)
                        ->where('deleted', '0')
                        ->orderBy('common', 'desc')
                        ->get();
                } else {
                    $data['reservations'] = Coach_booking::with('Details')
                        ->select('id', 'name_en as name', 'title_en as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                        ->where('coach_id', $id)
                        ->where('deleted', '0')
                        ->orderBy('common', 'desc')
                        ->get();
                }
            }
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function update_coach_data(Request $request)
    {
        set_time_limit(10000);
        $user = auth()->guard('coach')->user();

        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $input = $request->all();
        $validator = Validator::make($input, []);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), null, $request->lang);
            return response()->json($response, 406);
        } else {
            if ($request->image != null) {
                $image = $request->image;
                $imagereturned = Cloudinary::upload("data:image/jpeg;base64," . $image);
                $image_id = $imagereturned->getPublicId();
                $image_format = $imagereturned->getExtension();
                $image_new_name = $image_id . '.' . $image_format;
                $input['image'] = $image_new_name;
            } else {
                unset($input['image']);
            }
            if ($request->story != null) {
                $story = $request->story;
                $uploadedFileUrl = $this->uploadFromApi("data:video/mp4;base64," . $story);
                $image_id2 = $uploadedFileUrl->getPublicId();
                $image_format2 = $uploadedFileUrl->getExtension();
                $image_new_story = $image_id2 . '.' . $image_format2;
                $input['story'] = $image_new_story;
            } else {
                unset($input['story']);
            }
            $coach_time_work = Coach_time_work::where('coach_id', $user->id)->first();
            if ($coach_time_work) {
                $coach_time_work->time_from = $request->time_from;
                $coach_time_work->time_to = $request->time_to;
                $coach_time_work->save();
            } else {
                $coach_time_work_data['coach_id'] = $user->id;
                $coach_time_work_data['time_from'] = $request->time_from;
                $coach_time_work_data['time_to'] = $request->time_to;
                Coach_time_work::create($coach_time_work_data);
            }
            unset($input['time_from']);
            unset($input['time_to']);
            Coach::where('id', $user->id)->update($input);
			$coach = Coach::where('id', $user->id)->first();
            $response = APIHelpers::createApiResponse(false, 200, 'updated', 'تم التعديل بنجاح', $coach, $request->lang);
            return response()->json($response, 200);
        }
    }

    // plans
    public function my_plans(Request $request)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            if ($lang == 'ar') {
                $data = Coach_booking::select('id', 'name_ar as name', 'title_ar as title', 'price', 'discount', 'discount_price', 'months_num', 'common')->where('coach_id', $user->id)->where('deleted', '0')->get();
            } else {
                $data = Coach_booking::select('id', 'name_en as name', 'title_en as title', 'price', 'discount', 'discount_price', 'months_num', 'common')->where('coach_id', $user->id)->where('deleted', '0')->get();
            }
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }


    public function plan_details(Request $request, $id)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            if ($lang == 'ar') {
                $data = Coach_booking_detail::select('id', 'name_ar as name')->where('booking_id', $id)->get();
            } else {
                $data = Coach_booking_detail::select('id', 'name_en as name')->where('booking_id', $id)->get();
            }
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function select_plan_data(Request $request, $id)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $data = Coach_booking::select('id', 'name_ar', 'name_en', 'title_ar', 'title_en', 'price', 'is_discount', 'discount', 'discount_price', 'months_num')->find($id);
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function delete_plan_detail(Request $request)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:coach_booking_details,id'
            ]);
            if ($validator->fails()) {
                $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
                return response()->json($response, 406);
            }
            Coach_booking_detail::where('id', $request->id)->delete();
            $response = APIHelpers::createApiResponse(false, 200, 'Deteted', 'تم الحذف', null, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function delete_plan(Request $request)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:coach_bookings,id'
            ]);
            if ($validator->fails()) {
                $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
                return response()->json($response, 406);
            }
            $data['deleted'] = '1';
            Coach_booking::where('id', $request->id)->update($data);
            $plans = Coach_booking::select('id', 'name_' . $request->lang . ' as name', 'title_' . $request->lang . ' as title', 'price', 'discount', 'discount_price', 'months_num', 'common')->where('coach_id', $user->id)->where('deleted', '0')->get();
            $response = APIHelpers::createApiResponse(false, 200, 'Deleted', 'تم الحذف', $plans, $request->lang);
            return response()->json($response, 200);
        }
    }

    //make new plan
    public function store_plan(Request $request)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'name_ar' => 'required',
            'name_en' => 'required',
            'title_ar' => 'required',
            'price' => 'required',
            'months_num' => 'required|numeric|min:1',
            'is_discount' => 'required|in:1,0',
            'discount' => '',
            'discount_price' => '',
            'details' => 'required',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {
                unset($input['details']);
                if ($request->is_discount == 1) {
                    $input['is_discount'] = $request->is_discount;
                    $input['discount'] = $request->discount;
                    $input['discount_price'] = $request->discount_price;
                }
                $input['coach_id'] = $user->id;
                $booking = Coach_booking::create($input);
                if ($booking != null) {
                    foreach ($request->details as $row) {
                        $data['booking_id'] = $booking->id;
                        $data['name_ar'] = $row['name_ar'];
                        $data['name_en'] = $row['name_en'];
                        Coach_booking_detail::create($data);
                    }
                }
                $response = APIHelpers::createApiResponse(false, 200, 'plan added successfully', 'تم اضافة الخطة بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function update_plan(Request $request, $id)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'name_ar' => 'required',
            'name_en' => 'required',
            'title_ar' => 'required',
            'title_en' => 'required',
            'price' => 'required',
            'months_num' => 'required|numeric|min:1',
            'is_discount' => 'required|in:1,0',
            'discount' => '',
            'discount_price' => '',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {
                $input['is_discount'] = $request->is_discount;
                $input['discount'] = $request->discount;
                $input['discount_price'] = $request->discount_price;
                $booking = Coach_booking::where('id', $id)->update($input);
                $response = APIHelpers::createApiResponse(false, 200, 'plan updated successfully', 'تم تعديل الخطة بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function store_plan_detail(Request $request)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'booking_id' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {
                $booking = Coach_booking_detail::create($input);
                $response = APIHelpers::createApiResponse(false, 200, 'plan detail added successfully', 'تم اضافة تفصيل الخطة بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function make_common(Request $request, $id)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        if ($user != null) {
            $data['common'] = 0;
            Coach_booking::where('coach_id', $user->id)->update($data);

            $booking = Coach_booking::where('id', $id)->first();
            $booking->common = 1;
            $booking->save();
            $response = APIHelpers::createApiResponse(false, 200, 'plan common successfully', 'تم جعل الخطة شائعه بنجاح', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }

    }

    //media
    public function media(Request $request)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $data = Coach_media::select('id', 'image', 'type')
                ->where('coach_id', $user->id)
                ->get();
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function media_filter(Request $request, $type)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $data = Coach_media::select('id', 'image', 'type')
                ->where('coach_id', $user->id)->where('type', $type)
                ->get();
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function delete_media(Request $request)
    {
        $user = auth()->guard('coach')->user();
        $input = $request->all();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $validator = Validator::make($input, [
                'id' => 'required|exists:coach_media,id'
            ]);
            if ($validator->fails()) {
                $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
                return response()->json($response, 406);
            }

            $data = Coach_media::where('id', $request->id)->delete();
            $response = APIHelpers::createApiResponse(false, 200, 'deleted', 'تم الحذف', null, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function store_media(Request $request)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'image' => 'required',
            'type' => 'required|in:image,video'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {
                if ($request->image != null && $request->type == 'image') {
                    $image = $request->image;
                    $imagereturned = Cloudinary::upload("data:image/jpeg;base64," . $image);
                    $image_id = $imagereturned->getPublicId();
                    $image_format = $imagereturned->getExtension();
                    $image_new_name = $image_id . '.' . $image_format;
                    $input['image'] = $image_new_name;
                } else if ($request->image != null && $request->type == 'video') {
                    $uploadedFileUrl = $this->upload($request->file('image'));
                    $image_id2 = $uploadedFileUrl->getPublicId();
                    $image_format2 = $uploadedFileUrl->getExtension();
                    $image_new_story = $image_id2 . '.' . $image_format2;
                    $input['image'] = $image_new_story;
                    if ($request->thumbnail != null){
                        //create thumbnail
                        $thumbnail = $request->thumbnail;
                        $imagereturned = Cloudinary::upload("data:image/jpeg;base64," . $thumbnail);
                        $image_id = $imagereturned->getPublicId();
                        $image_format = $imagereturned->getExtension();
                        $thumbnail_new_name = $image_id . '.' . $image_format;
                        $input['thumbnail'] = $thumbnail_new_name;
                    }else{
                        $response = APIHelpers::createApiResponse(true, 406, 'you should choos short image to video first', 'يجب اختيار صوره مصغره للفيديو اولا ', null, $request->lang);
                        return response()->json($response, 406);
                    }
                }
                $input['coach_id'] = $user->id;
                Coach_media::create($input);
                $response = APIHelpers::createApiResponse(false, 200, 'coach media added successfully', 'تم اضافة الوسائط بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

//    for time works
    public function times(Request $request)
    {
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $data = Coach_time_work::select('id', 'time_from', 'time_to')
                ->where('coach_id', $user->id)
                ->get();
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function store_time(Request $request)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'time_from' => 'required',
            'time_to' => 'required'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {

                $input['coach_id'] = $user->id;
                $input['time_from'] = $request->time_from;
                $input['time_to'] = $request->time_to;
                Coach_time_work::create($input);
                $response = APIHelpers::createApiResponse(false, 200, 'coach time work added successfully', 'تم اضافة موعد العمل بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function update_time(Request $request, $id)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'time_from' => 'required',
            'time_to' => 'required'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {

                $input['time_from'] = $request->time_from;
                $input['time_to'] = $request->time_to;
                Coach_time_work::where('id', $id)->update($input);
                $response = APIHelpers::createApiResponse(false, 200, 'coach time work updated successfully', 'تم تعديل موعد العمل بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function delete_time(Request $request)
    {
        $input = $request->all();
        $user = auth()->guard('coach')->user();
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        }
        $validator = Validator::make($input, [
            'time_id' => 'required||exists:coach_time_works,id'
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($user != null) {
                Coach_time_work::where('id', $request->time_id)->delete();
                $response = APIHelpers::createApiResponse(false, 200, 'deleted successfully', 'تم الحذف بنجاح', null, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, '', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    //subscribers controllers
    public function subscribers(Request $request, $type)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $booking_ids = Coach_booking::where('coach_id', $user->id)->select('id')->get()->toArray();
            $data = Reservation::with('User')->with('Booking_coach')
                ->whereIn('booking_id', $booking_ids)
                ->where('type', 'coach')
                ->get()
                ->map(function ($reserv) use ($lang) {
                    $reserv->coach_name = $reserv->Booking_coach->Coach->name;
                    $reserv->coach_logo = $reserv->Booking_coach->Coach->image;
                    if ($lang == 'ar') {
//                        if ($reserv->status == 'ended') {
//                            $reserv->status = 'منتهي';
//                        } else {
//                            $reserv->status = 'ساري';
//
//                        }
                        $reserv->reserve_name = $reserv->Booking_coach->name_ar;
                    } else {
                        $reserv->reserve_name = $reserv->Booking_coach->name_en;
                    }
                    return $reserv;
                });
            $subscriptions = null;
            foreach ($data as $key => $row) {
                $subscriptions[$key]['id'] = $row->id;
                $subscriptions[$key]['booking_id'] = $row->booking_id;
                $subscriptions[$key]['user_id'] = $row->user_id;
                $subscriptions[$key]['user_logo'] = $row->User->image;
                $subscriptions[$key]['user_name'] = $row->User->name;
                $subscriptions[$key]['reserve_name'] = $row->reserve_name;
                $subscriptions[$key]['price'] = number_format((float)($row->price), 3);
                $subscriptions[$key]['status'] = $row->status;
                $subscriptions[$key]['payment'] = $row->payment;
                $subscriptions[$key]['created_at'] = $row->created_at->format('Y-m-d');
                $subscriptions[$key]['expire_date'] = date('Y-m-d', strtotime($row->expire_date));
            }
            $response = APIHelpers::createApiResponse(false, 200, '', '', $subscriptions, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function subscriber_bill(Request $request, $id)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        Session::put('lang', $lang);
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $data = Reservation::select('id', 'price', 'expire_date', 'user_id', 'created_at', 'payment', 'status', 'booking_id')
                ->with('User_info')->with('Plan_details')
                ->where('id', $id)
                ->first();
            $data->price = number_format((float)($data->price), 3);
            $data->created_at = $data->created_at->format('Y-m-d');
            $data->expire_date = date('Y-m-d', strtotime($data->expire_date));
//                ->map(function ($reserv) use ($lang) {
//                    if ($lang == 'ar') {
//                        if ($reserv->status == 'ended') {
//                            $reserv->status = 'منتهي';
//                        } else {
//                            $reserv->status = 'ساري';
//                        }
//                    }
//                    $reserv->price = number_format((float)($reserv->price), 3);
//
//                    $reserv->created_at = $reserv->created_at->format('Y-m-d');
//                    $reserv->expire_date = date('Y-m-d', strtotime($reserv->expire_date));
//                    return $reserv;
//                });
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function subscriber_user_info(Request $request, $id)
    {
        $user = auth()->guard('coach')->user();
        $lang = $request->lang;
        Session::put('lang', $lang);
        $lang = $request->lang;
        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login', 'يجب تسجيل الدخول', null, $request->lang);
            return response()->json($response, 406);
        } else {
            $user_data = Reservation::with('User')->where('id', $id)->first()->User_info;
            $data = Reservation_option::select('id', 'type_id', 'goal_id', 'reservation_id')->with('Type_data')
                ->where('reservation_id', $id)
                ->get()
                ->map(function ($reserv) use ($lang) {

                    $exists_goals = Reservation_goal::where('type_id', $reserv->type_id)->get();
                    if ($exists_goals) {

                        $goal = Reservation_goal::where('id', $reserv->goal_id)->first();
                        if ($goal) {
                            if ($lang == 'ar') {
                                $reserv->goal_id = $goal->title_ar;
                            } else {
                                $reserv->goal_id = $goal->title_en;
                            }
                        }

                    }
                    return $reserv;
                });

            $subscriptions = null;
            foreach ($data as $key => $row) {
                $subscriptions[$key]['id'] = $row->id;
                $subscriptions[$key]['name'] = $row->type_data->title;
                $subscriptions[$key]['value'] = $row->goal_id;
            }
            $response = APIHelpers::createApiResponse(false, 200, '', '', array('user_data' => $user_data, 'subscriptions' => $subscriptions), $request->lang);
            return response()->json($response, 200);
        }
    }

    // get notifications
    public function notifications(Request $request)
    {
        $user = auth()->guard('coach')->user();
        $notifications_ids = CoachNotification::where('coach_id', $user->id)->orderBy('id', 'desc')
            ->select('notification_id')->get();
        $notifications = [];
        for ($i = 0; $i < count($notifications_ids); $i++) {
            $notifications[$i] = Notification::select('id', 'title', 'body', 'image', 'created_at')->find($notifications_ids[$i]['notification_id']);
        }
        $data['notifications'] = $notifications;
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data['notifications'], $request->lang);
        return response()->json($response, 200);
    }
}
