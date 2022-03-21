<?php

namespace App\Http\Controllers;

use App\Coach_booking;
use App\Reservation;
use App\User_caoch_ask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Conversation;
use App\Participant;
use Carbon\Carbon;
use App\Message;
use Cloudinary;
use App\Coach;
use App\User;

class CoachChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['end_consultation','make_read', 'my_messages', 'store', 'get_ad_message', 'index', 'test_exists_conversation', 'search_conversation']]);
    }

    public function test_exists_conversation(Request $request)
    {
        $coach_id = auth()->guard('coach')->user()->id;
        $user = User::find($request->id);

        $exist_part_one = Participant::where('coach_id', $coach_id)
            ->where('user_id', $coach_id)
            ->where('other_user_id', $user->id)
            ->first();
        if ($exist_part_one == null) {
            $exist_part_one = Participant::where('coach_id', $coach_id)
                ->where('user_id', $user->id)
                ->where('other_user_id', $coach_id)
                ->first();
        }
        if ($exist_part_one != null) {
            $data['exist'] = 'true';
            $data['conversation_id'] = $exist_part_one->conversation_id;
        } else {
            $data['exist'] = 'false';
            $data['conversation_id'] = 0;
        }
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function my_messages(Request $request)
    {
        $coach = auth()->guard('coach')->user();
        if ($coach == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
        $lang = $request->lang;
        $data['conversations'] = Participant::where('user_id', $coach->id)->where('deleted', '0')
            ->get()
            ->map(function ($convs) use ($coach) {
                $other_user = Participant::where('conversation_id', $convs->conversation_id)->where('user_id', '!=', $coach->id)->first();
                $convs->user_name = $other_user->User->name;
                $convs->image = $other_user->User->image;
                $convs->last_message = $other_user->Conversation->Message->message;
                $convs->last_message_time = $other_user->Conversation->Message->updated_at->format('g:i a');
                $convs->un_read_num = Message::where('conversation_id', $convs->conversation_id)->where('user_id', '!=', $coach->id)->where('is_read', '0')->count();

                //if user have free ask
                $free_ask = User_caoch_ask::where('user_id', $convs->other_user_id)->where('caoch_id', $coach->id)->first();

                $booking_ids = Coach_booking::where('coach_id', $coach->id)->select('id')->get()->toArray();
                $pay_ask = Reservation::where('user_id', $convs->other_user_id)
                    ->whereIn('booking_id', $booking_ids)
                    ->where('type', 'coach')
                    ->where('status', 'start')
                    ->get();
                if ($free_ask->ask_num_free > 0) {
                    if (count($pay_ask) > 0) {
                        $convs->conversation_type = 'available_now';
                    } else {
                        $convs->conversation_type = 'visitor';
                    }
                     $convs->free_ask = true;
                } else {
                    if (count($pay_ask) > 0) {
                        $convs->conversation_type = 'available_now';
                    } else {
                        $pay_ended_ask = Reservation::where('user_id', $convs->other_user_id)
                            ->whereIn('booking_id', $booking_ids)
                            ->where('type', 'coach')
                            ->where('status', 'ended')
                            ->get();
                        if (count($pay_ended_ask) > 0) {
                            $convs->conversation_type = 'ended';
                        } else {
                            $convs->conversation_type = 'empty';
                        }
                    }
                     $convs->free_ask = false;
                }
                return $convs;
            });
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $coach = auth()->guard('coach')->user();
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required|exists:users,id',
            'message' => 'required',
            'type' => 'required',
            'conversation_id' => ''
        ]);
        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true, 406, $validator->messages()->first(), $validator->messages()->first(), $validator->messages()->first(), $request->lang);
            return response()->json($response, 406);
        } else {
            if ($coach != null) {
                //test exists message
                $conversation_id = $request->conversation_id;

                $user = User::find($request->user_id);

                if ($conversation_id == 0) {
                    $response = APIHelpers::createApiResponse(true, 406, 'you can`t make conversation to your self', 'لا يمكنك اجراء محادثة ', null, $request->lang);
                    return response()->json($response, 406);
                } else {
                    $conv = Conversation::where('id', $conversation_id)->first();
                    $input['conversation_id'] = $conversation_id;
                    $data = $conv->id;
                }
                $other_user = Participant::where('conversation_id', $input['conversation_id'])->where('user_id', '!=', $coach->id)->first();
                $input['user_id'] = $coach->id;
                $input['user_type'] = 'coach';
                $input['coach_id'] = $coach->id;
                if ($request->type == 'text') {
                    $message = Message::create($input);
                } else if ($request->type == 'image') {
                    $image = $request->message;
                    $imagereturned = Cloudinary::upload("data:image/jpeg;base64," . $image);
                    $image_id = $imagereturned->getPublicId();
                    $image_format = $imagereturned->getExtension();
                    $thumbnail_new_name = $image_id . '.' . $image_format;
                    $input['message'] = $thumbnail_new_name;
                    $message = Message::create($input);
                } else if ($request->type == 'video') {
                    $uploadedFileUrl = $this->upload($request->file('message'));
                    $image_id2 = $uploadedFileUrl->getPublicId();
                    $image_format2 = $uploadedFileUrl->getExtension();
                    $image_new_story = $image_id2 . '.' . $image_format2;
                    $input['message'] = $image_new_story;
                    $message = Message::create($input);
                } else if ($request->type == 'file') {
                    // dd($request->file('message')->getRealPath());
                    $uploadedFileUrl = $this->upload_file($request->file('message'));
                    $image_id2 = $uploadedFileUrl->getPublicId();
                    $image_format2 = $uploadedFileUrl->getExtension();
                    $image_new_story = $image_id2 . '.' . $image_format2;
                    $input['message'] = $image_new_story;
                    $message = Message::create($input);
                }
                if ($message != null) {
                    Participant::where('user_type', 'user')
                        ->where('conversation_id', $conversation_id)
                        ->update(['deleted'=>'0']);
                    $conv_data['last_message_id'] = $message->id;
                    Conversation::find($input['conversation_id'])->update($conv_data);
                    Participant::where('conversation_id',$input['conversation_id'])->update(['updated_at'=>Carbon::now()]);
                }
                //begin use firebase to send message
                $fb_token = $other_user->User->fcm_token;
                if ($request->lang == 'ar') {
                    $title = 'رسالة من تطبيق الجيم والصالات';
                } else {
                    $title = 'message  from gym app';
                }
                $sub_message = substr($message->message, 0, 50);
                $link = env('APP_URL') . '/api/chat/get_ad_message/' . $message->coach_id . '/en/v1';
//                $fb_token = 'fWhAQ1jMQ4iivvh3Qrnzlo:APA91bF8qD2dspOk8ASLmhO1Q3-mS7HFzcCwSoevdHNtv1JaL3Ps2-u1H6Uy_ASyBXmgpDq2VD_0rw5frliggpMIWnZNmlo-GNGI6tSf7m4Vd6mTPHKgA9sXUrC9Xqc_TbyjtN-xcU_F';
                $result = APIHelpers::send_chat_notification($fb_token, $title, $sub_message, $message->type, $message, $link);
                //end firebase
                $response = APIHelpers::createApiResponse(false, 200, 'message sent successfully', 'تم ارسال الرسالة بنجاح', $data, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
                return response()->json($response, 406);
            }
        }
    }

    public function get_ad_message(Request $request)
    {

        $coach = auth()->guard('coach')->user();
        if ($coach == null) {
            $response = APIHelpers::createApiResponse(true, 406, 'you should login first', 'يجب تسجيل الدخول اولا', null, $request->lang);
            return response()->json($response, 406);
        }
        $partic = Participant::where('conversation_id', $request->conversation_id)->where('coach_id', $coach->id)->first();

        if ($partic != null) {
            $other_user = Participant::where('user_id', $request->id)->where('conversation_id', $request->conversation_id)->first();
            $input['is_read'] = '1';
            Message::where('coach_id', $request->id)->where('conversation_id', $request->conversation_id)->update($input);

            $ad_pro_user_Data = User::findOrFail($request->id);
            if ($ad_pro_user_Data->user_id == $coach->id) {
                $user_other_data = User::where('id', $other_user->user_id)->first();
                $data['ad_user_data']['name'] = $user_other_data->name;
                $data['ad_user_data']['email'] = $user_other_data->email;
                $data['ad_user_data']['image'] = $user_other_data->image;
                $data['ad_user_data']['phone'] = $user_other_data->phone;
            } else {
                $data['ad_user_data']['name'] = $ad_pro_user_Data->name;
                $data['ad_user_data']['email'] = $ad_pro_user_Data->email;
                $data['ad_user_data']['image'] = $ad_pro_user_Data->image;
                $data['ad_user_data']['phone'] = $ad_pro_user_Data->phone;
            }

            //End Generate conversation_type

            $free_ask = User_caoch_ask::where('user_id', $request->id)->where('caoch_id', $coach->id)->first();
            $booking_ids = Coach_booking::where('coach_id', $coach->id)->select('id')->get()->toArray();
            $pay_ask = Reservation::where('user_id', $request->id)
                ->whereIn('booking_id', $booking_ids)
                ->where('type', 'coach')
                ->where('status', 'start')
                ->get();
            if ($free_ask->ask_num_free > 0) {
                if (count($pay_ask) > 0) {
                    $data['ad_user_data']['conversation_type'] = 'available_now';
                } else {
                    $data['ad_user_data']['conversation_type'] = 'visitor';
                }
            } else {
                if (count($pay_ask) > 0) {
                    $data['ad_user_data']['conversation_type'] = 'available_now';
                } else {
                    $pay_ended_ask = Reservation::where('user_id', $request->id)
                        ->whereIn('booking_id', $booking_ids)
                        ->where('type', 'coach')
                        ->where('status', 'ended')
                        ->get();
                    if (count($pay_ended_ask) > 0) {
                        $data['ad_user_data']['conversation_type'] = 'ended';
                    } else {
                        $data['ad_user_data']['conversation_type'] = 'empty';
                    }
                }
            }
            //End Generate conversation_type
            $days = Message::where('coach_id', $coach->id)
                ->where('conversation_id', $request->conversation_id)
                ->select('id', 'message', 'type', 'user_id', 'user_type', 'conversation_id', 'coach_id', 'created_at')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($messages) use ($coach) {
                    $messages->time = $messages->created_at->format('g:i a');
                    if ($messages->user_id == $coach->id) {
                        $messages->position = 'right';
                    } else {
                        $messages->position = 'left';
                    }
                    return $messages;
                })
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('Y-m-d');   // grouping by date
                });
            $i = 0;
            foreach ($days as $key => $row) {
                $message[$i]['day'] = $key;
                $message[$i]['day_messages'] = $row;
                $i = $i + 1;
            }
            $data['messages'] = $message;
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        } else {
            $coach = Coach::findOrFail($request->id);
            $data['ad_user_data']['name'] = $coach->name;
            $data['ad_user_data']['email'] = $coach->email;
            $data['ad_user_data']['image'] = $coach->image;
            $data['ad_user_data']['phone'] = $coach->phone;
            $data['messages'] = [];
            $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
            return response()->json($response, 200);
        }
    }

    public function make_read(Request $request)
    {
        $input['is_read'] = '1';
        Message::where('id', $request->message_id)->update($input);
        $response = APIHelpers::createApiResponse(false, 200, 'message seen successfuly', 'تم رؤية الرسالة بنجاح', null, $request->lang);
        return response()->json($response, 200);
    }


    public function end_consultation(Request $request)
    {
        $coach = auth()->guard('coach')->user();
        $free_ask = User_caoch_ask::where('user_id', $request->user_id)->where('caoch_id', $coach->id)->first();
        if ($free_ask) {
            $free_ask->ask_num_free = 0;
            $free_ask->save();
        }

        $response = APIHelpers::createApiResponse(false, 200, 'Conversation ended successfuly', 'تم انهاء المحادثه بنجاح', null, $request->lang);
        return response()->json($response, 200);
    }

    public function search_conversation(Request $request)
    {
        $coach_id = auth()->guard('coach')->user()->id;
        $users = User::where('name', 'like', '%' . $request->search . '%')->get();
        $users_arr = null;
        $convs_arr = null;
        $searched_convs = null;
        if (count($users) == 0) {
            if ($request->type == 'ios') {
                $data['conversations'] = [];
                $response = APIHelpers::createApiResponse(false, 200, 'no chat for inserted search key', 'لا يوجد دردشة باسم المستخدم المختار', $data, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'no chat for inserted search key', 'لا يوجد دردشة باسم المستخدم المختار', null, $request->lang);
                return response()->json($response, 406);
            }
        }
        foreach ($users as $key => $user) {
            $users_arr[$key] = $user->id;
        }
        $data_partics = Participant::where('user_id', $coach_id)->get();
        foreach ($data_partics as $key => $row) {
            $convs_arr[$key] = $row->conversation_id;
        }
        $others = Participant::wherein('user_id', $users_arr)->wherein('conversation_id', $convs_arr)->get();
        if (count($others) == 0) {

            if ($request->type == 'ios') {
                $data['conversations'] = [];
                $response = APIHelpers::createApiResponse(false, 200, 'no chat for inserted search key', 'لا يوجد دردشة باسم المستخدم المختار', $data, $request->lang);
                return response()->json($response, 200);
            } else {
                $response = APIHelpers::createApiResponse(true, 406, 'no chat for inserted search key', 'لا يوجد دردشة باسم المستخدم المختار', null, $request->lang);
                return response()->json($response, 406);
            }
        }
        foreach ($others as $key => $row) {
            $searched_convs[$key] = $row->conversation_id;
        }
        $data['conversations'] = Participant::where('user_id', $coach_id)->wherein('conversation_id', $searched_convs)
            ->get()
            ->map(function ($convs) use ($coach_id) {
                $other_user = Participant::where('conversation_id', $convs->conversation_id)->where('user_id', '!=', $coach_id)->first();
                $convs->other_user_id = $other_user->User->id;
                $convs->user_name = $other_user->User->name;
                $convs->image = $other_user->User->image;
                $convs->last_message = $other_user->Conversation->Message->message;
                $convs->last_message_time = $other_user->Conversation->Message->updated_at->format('g:i a');
                $convs->un_read_num = Message::where('conversation_id', $convs->conversation_id)->where('user_id', '!=', $coach_id)->where('is_read', '0')->count();
                return $convs;
            });
        $response = APIHelpers::createApiResponse(false, 200, '', '', $data, $request->lang);
        return response()->json($response, 200);
    }
}
