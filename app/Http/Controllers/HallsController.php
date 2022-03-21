<?php

namespace App\Http\Controllers;

use App\Coach;
use App\Reservation_options_test;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Reservation_option;
use App\Helpers\APIHelpers;
use App\Reservation_type;
use App\Hole_time_work;
use App\Coach_booking;
use App\Hole_booking;
use App\Hole_branch;
use App\Reservation;
use App\Hole_media;
use Carbon\Carbon;
use App\Favorite;
use App\Setting;
use App\Income;
use App\Admin;
use App\Hole;
use App\Rate;
use App\User;

class HallsController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['excute_store_reservation', 'store_reservation', 'all_halls', 'details', 'rates', 'store_rate']]);
        //        --------------------------------------------- begin scheduled functions --------------------------------------------------------
        $expired = Reservation::where('status', 'start')->whereDate('expire_date', '<', Carbon::now())->get();
        if ($expired != null) {
            foreach ($expired as $row) {
                $product = Reservation::find($row->id);
                $product->status = 'ended';
                $product->save();
            }
        }
        Carbon::parse('Fri Sep 20 2019 00:00:00 GMT+0700');
        //        --------------------------------------------- end scheduled functions --------------------------------------------------------
    }

    public function all_halls(Request $request)
    {
        $lang = $request->lang;
        $user = auth()->user();
        $result = Hole_time_work::query();
        if ($request->male == 1) {
            $result = $result->orWhere('type', 'male');
        }
        if ($request->femal == 1) {
            $result = $result->orWhere('type', 'female');
        }
        if ($request->mix == 1) {
            $result = $result->orWhere('type', 'mix');
        }
        $result = $result = $result->groupBy('hole_id')
            ->orderBy(Hole::select('sort')
                ->whereColumn('holes.id', 'hole_time_works.hole_id')
            )->get();
        $data = null;
        foreach ($result as $key => $hall) {
            $selected_hall = Hole::findOrFail($hall->hole_id);
            if ($selected_hall->deleted == '0' && $selected_hall->status == 'active') {
                $data[$key]['id'] = $selected_hall->id;
                $data[$key]['cover'] = $selected_hall->cover;
                $data[$key]['logo'] = $selected_hall->logo;
                if ($lang == 'ar') {
                    $data[$key]['name'] = $selected_hall->name;
                    $data[$key]['description'] = $selected_hall->about_hole;
                } else {
                    $data[$key]['name'] = $selected_hall->name_en;
                    $data[$key]['description'] = $selected_hall->about_hole_en;

                }
                $data[$key]['rate'] = $selected_hall->rate;

                if ($user == null) {
                    $data[$key]['favorite'] = false;
                } else {
                    $fav = Favorite::where('user_id', $user->id)->where('product_id', $selected_hall->id)->where('type', 'hall')->first();
                    if ($fav == null) {
                        $data[$key]['favorite'] = false;
                    } else {
                        $data[$key]['favorite'] = true;
                    }
                }
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function rates(Request $request)
    {
        $lang = $request->lang;
        $id = $request->id;
        $rates = Rate::select('text', 'rate', 'user_id as user_name', 'created_at')->where('type', $request->type)
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
        if ($request->type == 'hall') {
            $rate = Hole::whereId($request->id)->first()->rate;
        } else {
            $rate = Coach::whereId($request->id)->first()->rate;
        }


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

    public function reservation_types(Request $request)
    {
        $lang = $request->lang;
        Session::put('api_lang', $lang);
        $data = null;
        if ($lang == 'ar') {
            $data = Reservation_type::with('Goals')
                ->select('id', 'title_ar as title', 'is_required')
                ->where('deleted', '0')
                ->get()
                ->map(function ($types) {
                    if (count($types->Goals) != 0) {
                        $types->type = 'select';
                    } else {
                        $types->type = 'input';
                    }
                    return $types;
                });
        } else {
            $data = Reservation_type::with('Goals')
                ->select('id', 'title_en as title', 'is_required')
                ->where('deleted', '0')
                ->get()
                ->map(function ($types) {
                    if (count($types->Goals) != 0) {
                        $types->type = 'select';
                    } else {
                        $types->type = 'input';
                    }
                    return $types;
                });
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function store_rate(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required',
            'rate' => 'required|numeric|min:1|max:5'
        ]);
        if ($type == 'hall') {
            $validator = Validator::make($request->all(), [
                'target_id' => 'required|exists:holes,id'
            ]);
        } else if ($type == 'coach') {
            $validator = Validator::make($request->all(), [
                'target_id' => 'required|exists:coaches,id'
            ]);
        }
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }
        $user = auth()->user();

        if ($user == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
        $exist_rate = Rate::where('order_id', $request->target_id)->where('type', $type)->where('user_id', $user->id)->first();
        if ($exist_rate == null) {
            $data['user_id'] = $user->id;
            $data['text'] = $request->text;
            $data['rate'] = $request->rate;
            $data['order_id'] = $request->target_id;
            $data['admin_approval'] = 2;
            $data['type'] = $type;
            $rating = Rate::create($data);

            $response = APIHelpers::createApiResponse(false, 200, 'rate added successfully', 'تم اضافة التقييم بنجاح', null, $request->lang);
            return response()->json($response, 200);
        } else {
            $response = APIHelpers::createApiResponse(true, 406, 'you make rate before , you can`t make rate again', 'لقد تم التقييم من قبل لا يمكنك التقييم مره اخرى', null, $request->lang);
            return response()->json($response, 406);
        }
    }

    public function store_reservation(Request $request, $type)
    {
        if ($type == 'hall') {
            $booking = Hole_booking::where('id', $request->booking_id)->first();
        } else {
            $booking = Coach_booking::where('id', $request->booking_id)->first();
        }

        if ($booking == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'يجب اختيار اشتراك صحيح اولا', ' You must choose a valid subscription first', (object)[], $request->lang);
            return response()->json($response, 406);
        }
        $price = null;
        if ($booking->is_discount == 1) {
            $price = $booking->discount_price;
        } else {
            $price = $booking->price;
        }
        $user = auth()->user();
        $admin_data = Admin::find(1);
        $bill_num = $admin_data->options_bill_num;

        //save options selected in database
        if ($request->option_id != null) {
            foreach ($request->option_id as $key => $row) {
                $test_data['option_id'] = $row;
                $test_data['value'] = $request->option_value[$key];
                $test_data['bill_num'] = $bill_num;
                Reservation_options_test::create($test_data);
            }
        }
        $admin_data->options_bill_num = $admin_data->options_bill_num + 1;
        $admin_data->save();

        $root_url = $request->root();
        $path = 'https://apitest.myfatoorah.com/v2/SendPayment';
        $token = "bearer rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $headers = array(
            'Authorization:' . $token,
            'Content-Type:application/json'
        );


        $call_back_url = $root_url . "/api/reservation/store/excute_pay?user_id=" . $user->id . "&booking_id=" . $request->booking_id .
            "&bill_num=" . $bill_num .
            "&price=" . $price .
            "&type=" . $type;


        $error_url = $root_url . "/api/pay/error";
        $fields = array(
            "CustomerName" => $user->name,
            "NotificationOption" => "LNK",
            "InvoiceValue" => $price,
            "CallBackUrl" => $call_back_url,
            "ErrorUrl" => $error_url,
            "Language" => "AR",
            "CustomerEmail" => $user->email
        );

        $payload = json_encode($fields);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $path);
        curl_setopt($curl_session, CURLOPT_POST, true);
        curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE);
        curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        $result = json_decode($result);
        $data['url'] = $result->Data->InvoiceURL;
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function excute_store_reservation(Request $request)
    {
        if ($request->type == 'hall') {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:hole_bookings,id',
            ]);
        } else if ($request->type == 'coach') {
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:coach_bookings,id',
            ]);
        }
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->errors()->first(), $validator->errors()->first(), null, $request->lang);
            return response()->json($response, 406);
        }
        $user = auth()->user();
        $data['user_id'] = $request->user_id;
        $data['booking_id'] = $request->booking_id;
        $data['price'] = $request->price;

        //get booking data to get expire date
        if ($request->type == 'hall') {
            $booking = Hole_booking::find($request->booking_id);
        } else {
            $booking = Coach_booking::find($request->booking_id);
        }

        $mytime = Carbon::now();
        $today = Carbon::parse($mytime->toDateTimeString())->format('Y-m-d H:i');
        $final_date = Carbon::createFromFormat('Y-m-d H:i', $today);
        $final_expire_date = $final_date->addMonths($booking->months_num);
        $data['expire_date'] = $final_expire_date;
        $data['type'] = $request->type;
        $reserve = Reservation::create($data);
        if ($reserve != null) {
            if ($request->bill_num != null) {
                $res_test = Reservation_options_test::where('bill_num', $request->bill_num)->get();

                foreach ($res_test as $key => $row) {
                    $otion_data['reservation_id'] = $reserve->id;
                    $otion_data['type_id'] = $row->option_id;
                    $otion_data['goal_id'] = $row->value;
                    Reservation_option::create($otion_data);
                }
                $updat_data_done['is_done'] = '1';
                Reservation_options_test::where('bill_num', $request->bill_num)->update($updat_data_done);
            }
            $income_Data['price'] = $request->price;
            $income_Data['type'] = $request->type;
            $income_Data['user_id'] = $request->user_id;
            $income_Data['reservation_id'] = $reserve->id;
            $income_Data['booking_id'] = $request->booking_id;
            Income::create($income_Data);

            // to save points after reservation ...
            $settings = Setting::where('id', 1)->first();
            $points = $settings->points * $request->price;
            $selected_user = User::find($data['user_id']);
            $selected_user->points = $selected_user->points + $points;
            $selected_user->save();
        }

        return redirect('api/pay/success');
    }

    public function details(Request $request, $id)
    {
        $lang = $request->lang;
        $user = auth()->user();
        Session::put('api_lang', $lang);
        if ($lang == 'ar') {
            $hall = Hole::select('id', 'cover', 'logo', 'name', 'story', 'about_hole', 'rate')->find($id);
        } else {
            $hall = Hole::select('id', 'cover', 'logo', 'name_en as name', 'story', 'about_hole_en as about_hole', 'rate')->find($id);
        }
        if ($hall != null) {
            if ($user == null) {
                $data['favorite'] = false;
            } else {
                $fav = Favorite::where('user_id', $user->id)->where('product_id', $id)->where('type', 'hall')->first();
                if ($fav == null) {
                    $data['favorite'] = false;
                } else {
                    $data['favorite'] = true;
                }
            }
            $data['basic'] = $hall;
            $data['work_times'] = Hole_time_work::select('id', 'time_from', 'time_to', 'type')
                ->where('hole_id', $id)
                ->get()
                ->map(function ($time) use ($lang) {
                    if ($lang == 'ar') {
                        if ($time->type == 'male') {
                            $time->type = 'الرجالية';
                        } else if ($time->type == 'female') {
                            $time->type = 'النسائية';
                        } else if ($time->type == 'mix') {
                            $time->type = 'المختلط';
                        }
                    }
                    $time->time_from = Carbon::createFromFormat('H:i:s', $time->time_from)->format('g:i a');
                    $time->time_to = Carbon::createFromFormat('H:i:s', $time->time_to)->format('g:i a');
                    return $time;
                });

            $data['media'] = Hole_media::select('id', 'image', 'type', 'thumbnail')
                ->where('hole_id', $id)
                ->get();
            if ($lang == 'ar') {
                $data['branches'] = Hole_branch::select('id', 'title_ar as title', 'latitude', 'longitude')->where('hole_id', $id)->get();
            } else {
                $data['branches'] = Hole_branch::select('id', 'title_en as title', 'latitude', 'longitude')->where('hole_id', $id)->get();
            }

            $rates_one = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 1)->get()->count();
            $rates_tow = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 2)->get()->count();
            $rates_three = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 3)->get()->count();
            $rates_four = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 4)->get()->count();
            $rates_five = Rate::where('type', 'hall')->where('admin_approval', 1)->where('order_id', $id)->where('rate', 5)->get()->count();

            $data['stars_count']['one'] = $rates_one;
            $data['stars_count']['tow'] = $rates_tow;
            $data['stars_count']['three'] = $rates_three;
            $data['stars_count']['four'] = $rates_four;
            $data['stars_count']['five'] = $rates_five;

            $data['all_rates'] = Rate::select('text', 'rate', 'user_id as user_name', 'created_at')->where('type', 'hall')
                ->where('order_id', $id)
                ->where('admin_approval', 1)
                ->get()
                ->map(function ($rate) use ($lang) {
                    $user = User::where('id', $rate->user_name)->first();
                    $rate->user = $user->name;
                    //                $rate->created_at = APIHelpers::get_month_year($rate->created_at, $lang);
                    return $rate;
                });
            $data['rates_count'] = count($data['all_rates']);
            if ($lang == 'ar') {
                $data['reservations'] = Hole_booking::with('Details')
                    ->select('id', 'name_ar as name', 'title_ar as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                    ->where('hole_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get();
            } else {
                $data['reservations'] = Hole_booking::with('Details')
                    ->select('id', 'name_en as name', 'title_en as description', 'price', 'is_discount', 'discount', 'discount_price', 'common')
                    ->where('hole_id', $id)
                    ->where('deleted', '0')
                    ->orderBy('common', 'desc')
                    ->get();
            }
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
}
