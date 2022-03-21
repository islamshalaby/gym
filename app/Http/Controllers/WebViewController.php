<?php

namespace App\Http\Controllers;
use App\Area;
use App\MainOrder;
use App\Order;
use App\OrderItem;
use App\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Setting;
use PDF;

class WebViewController extends Controller
{
    // get about
    public function getabout(Request $request, $lang){
        $setting = Setting::find(1);
        $data['text'] = $setting['aboutapp_ar'];
		$data['lang'] = $lang;
        return view('webview.about' , ['data' => $data]);
    }

    // get terms and conditions
    public function gettermsandconditions(Request $request, $lang){
        $setting = Setting::find(1);

            $data['title'] = 'الشروط و الأحكام';
            $data['text'] = $setting['termsandconditions_ar'];
		$data['lang'] = $lang;
        return view('webview.termsandconditions' , ['data' => $data]);
    }

    // get main orders reports
    public function getMainOrdersReport(Request $request) {
        if (isset($request->order_status)) {
            $statusArray = [1];
            if ($request->order_status == 'closed') {
                $statusArray = [3, 4, 9];
            }
            $data['order_status'] = $request->order_status;
            $data['orders'] = MainOrder::whereIn('status', $statusArray);
        }else{
            $data['orders'] = MainOrder::join('user_addresses', 'user_addresses.id', '=', 'main_orders.address_id');
            if(isset($request->order_status2)){
                $data['order_status2'] = $request->order_status2;
                $data['orders'] = $data['orders']->where('status', $request->order_status2);
            }
            if(isset($request->area_id)){
                $data['area'] = Area::where('id', $request->area_id)->select('id', 'title_en', 'title_ar')->first();
                $data['area_id'] = $request->area_id;
                $data['orders'] = $data['orders']->where('area_id', $request->area_id);
            }
            if(isset($request->from) && isset($request->to)){
                $data['from'] = $request->from;
                $data['to'] = $request->to;
                $data['orders'] = $data['orders']->whereBetween('main_orders.created_at', array($request->from, $request->to));
            }
            if(isset($request->method)){
                $data['method'] = $request->method;
                $data['orders'] = $data['orders']->where('main_orders.payment_method', $request->method);
            }
        }

        $data['sum_subtotal'] = $data['orders']->sum('subtotal_price');
        $data['sum_subtotal'] = number_format((float)$data['sum_subtotal'], 3, '.', '');
        $data['sum_delivery_cost'] = $data['orders']->sum('delivery_cost');
        $data['sum_delivery_cost'] = number_format((float)$data['sum_delivery_cost'], 3, '.', '');
        $data['sum_total_price'] = $data['orders']->sum('total_price');
        $data['sum_total_price'] = number_format((float)$data['sum_total_price'], 3, '.', '');
        $data['today'] = Carbon::now()->format('d-m-Y');
        $data['orders'] = $data['orders']->select('main_orders.*')->orderBy('main_orders.id', 'desc')->get();

        $data['setting'] = Setting::where('id', 1)->first();
        $pdf = PDF::loadView('shop_admin.orders.reports.main_report_admin_pdf', ['data' => $data]);

        return $pdf->stream('download.pdf');
    }

    public function getSalesReportAdmin(Request $request) {
        if (isset($request->order_status)) {
            $statusArray = [1, 2, 5];
            if ($request->order_status == 'closed') {
                $statusArray = [3, 4, 6, 7, 8, 9];
            }
            $data['order_status'] = $request->order_status;
            $data['orders'] = Order::whereIn('status', $statusArray);
        }else{
            $data['orders'] = Order::join('user_addresses', 'user_addresses.id', '=', 'orders.address_id');
            if(isset($request->area_id)){
                $data['area'] = Area::where('id', $request->area_id)->select('id', 'title_en', 'title_ar')->first();
                $data['area_id'] = $request->area_id;
                $data['orders'] = $data['orders']->where('area_id', $request->area_id);
            }
            if(isset($request->from) && isset($request->to)){
                $data['from'] = $request->from;
                $data['to'] = $request->to;
                $data['orders'] = $data['orders']->whereBetween('orders.created_at', array($request->from, $request->to));
            }
            if(isset($request->method)){
                $data['method'] = $request->method;
                $data['orders'] = $data['orders']->where('orders.payment_method', $request->method);
            }
            if(isset($request->order_status2)){
                $data['order_status2'] = $request->order_status2;
                $data['orders'] = $data['orders']->where('orders.status', $request->order_status2);
            }
            if(isset($request->shop)){
                $data['shop'] = $request->shop;
                $data['shop_name'] = Shop::where('id', $data['shop'])->select('name_ar')->first();
                $data['orders'] = $data['orders']->where('orders.store_id', $request->shop);
            }
        }


        $data['sum_subtotal'] = $data['orders']->sum('subtotal_price');
        $data['sum_subtotal'] = number_format((float)$data['sum_subtotal'], 3, '.', '');
        $data['sum_delivery_cost'] = $data['orders']->sum('delivery_cost');
        $data['sum_delivery_cost'] = number_format((float)$data['sum_delivery_cost'], 3, '.', '');
        $data['sum_total_price'] = $data['orders']->sum('total_price');
        $data['sum_total_price'] = number_format((float)$data['sum_total_price'], 3, '.', '');
        $data['today'] = Carbon::now()->format('d-m-Y');
        $data['orders'] = $data['orders']->select('orders.*')->orderBy('orders.id', 'desc')->get();


        $data['setting'] = Setting::where('id', 1)->first();
        $pdf = PDF::loadView('shop_admin.orders.reports.orders_report_admin_pdf', ['data' => $data]);
        return $pdf->stream('download.pdf');
    }

    // get delivery report
    public function getDeliveryReport(Request $request) {
        if (isset($request->order_status)) {
            $statusArray = [1, 2, 5];
            if ($request->order_status == 'delivered') {
                $statusArray = [3, 6, 7];
            }
            $data['order_status'] = $request->order_status;
            $data['orders'] = Order::whereIn('status', $statusArray)->orderBy('id' , 'desc');
        }else{
            $data['orders'] = Order::join('user_addresses', 'user_addresses.id', '=', 'orders.address_id')->whereIn('status', [1, 2, 5, 3 ,6, 7]);
            if(isset($request->area_id)){
                $data['area'] = Area::where('id', $request->area_id)->select('id', 'title_en', 'title_ar')->first();
                $data['area_id'] = $request->area_id;
                $data['orders'] = $data['orders']
                    ->where('area_id', $request->area_id);
            }
            if(isset($request->from) && isset($request->to)){
                $data['from'] = $request->from;
                $data['to'] = $request->to;
                $data['orders'] = $data['orders']->whereBetween('orders.created_at', array($request->from, $request->to));
            }
            if(isset($request->method)){
                $data['method'] = $request->method;
                $data['orders'] = $data['orders']->where('orders.payment_method', $request->method);
            }
            if(isset($request->order_status2)){
                $data['order_status2'] = $request->order_status2;
                $data['orders'] = $data['orders']->where('status', $request->order_status2);
            }
            if(isset($request->shop)){
                $data['shop'] = $request->shop;
                $data['shop_name'] = Shop::where('id', $data['shop'])->select('name')->first();
                $data['orders'] = $data['orders']->where('orders.store_id', $request->shop);
            }
        }

        $data['sum_subtotal'] = $data['orders']->sum('subtotal_price');
        $data['sum_subtotal'] = number_format((float)$data['sum_subtotal'], 3, '.', '');
        $data['sum_delivery_cost'] = $data['orders']->sum('delivery_cost');
        $data['sum_delivery_cost'] = number_format((float)$data['sum_delivery_cost'], 3, '.', '');
        $data['sum_total_price'] = $data['orders']->sum('total_price');
        $data['sum_total_price'] = number_format((float)$data['sum_total_price'], 3, '.', '');
        $data['today'] = Carbon::now()->format('d-m-Y');
        $data['orders'] = $data['orders']->select('orders.*')->orderBy('orders.id', 'desc')->get();

        $data['setting'] = Setting::where('id', 1)->first();
        $pdf = PDF::loadView('shop_admin.orders.reports.delivery_report_admin_pdf', ['data' => $data]);

        return $pdf->stream('download.pdf');
    }

    // get sales report admin
    public function getSalesReport2Admin(Request $request) {
        $data['orders'] = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id');
        if(isset($request->area_id)){
            $data['area'] = Area::where('id', $request->area_id)->select('id', 'title_en', 'title_ar')->first();
            $data['area_id'] = $request->area_id;
            $data['orders'] = $data['orders']
                ->leftjoin('user_addresses', function($join) {
                    $join->on('user_addresses.id', '=', 'orders.address_id');
                })
                ->where('area_id', $request->area_id);
        }
        if(isset($request->from) && isset($request->to)){
            $data['from'] = $request->from;
            $data['to'] = $request->to;
            $data['orders'] = $data['orders']->whereBetween('order_items.created_at', array($request->from, $request->to));
        }
        if(isset($request->method)){
            $data['method'] = $request->method;
            $data['orders'] = $data['orders']
                ->where('orders.payment_method', $request->method);
        }
        if(isset($request->order_status2)){
            $data['order_status2'] = $request->order_status2;
            $data['orders'] = $data['orders']->where('order_items.status', $request->order_status2);
        }
        if(isset($request->shop)){
            $data['shop'] = $request->shop;
            $data['shop_name'] = Shop::where('id', $data['shop'])->select('name')->first();
            $data['orders'] = $data['orders']
                ->where('orders.store_id', $request->shop);
        }

        $data['sum_final_price'] = $data['orders']->sum('final_price');
        $data['sum_final_price'] = number_format((float)$data['sum_final_price'], 3, '.', '');
        $data['today'] = Carbon::now()->format('d-m-Y');
        $data['orders'] = $data['orders']->select('order_items.*')->orderBy('id', 'desc')->get();
        $data['sum_total'] = 0;
        for ($i = 0; $i < count($data['orders']); $i ++) {
            $data['sum_total'] = $data['sum_total'] + ($data['orders'][$i]['final_price'] * $data['orders'][$i]['count']);
        }
        $data['sum_total'] = number_format((float)$data['sum_total'], 3, '.', '');
        $data['setting'] = Setting::where('id', 1)->first();
        $pdf = PDF::loadView('shop_admin.orders.reports.sales_report_admin_pdf', ['data' => $data]);

        return $pdf->stream('download.pdf');
    }

    // get store invoice
    public function getStoreInvoice(Request $request, Order $order) {
        $data['order'] = $order;
        $data['setting'] = Setting::where('id', 1)->first();
        $pdf = PDF::loadView('shop_admin.orders.invoice_store_pdf', ['data' => $data]);

        return $pdf->stream('download.pdf');
    }
}
