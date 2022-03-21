<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = [
        'barcode',
        'stored_number',
        'title_en',
        'title_ar',
        'offer',
        'description_ar',
        'description_en',
        'final_price',
        'price_before_offer',
        'offer_percentage',
        'category_id',
        'brand_id',
        'sub_category_id',
        'deleted',
        'total_quatity',
        'remaining_quantity',
        'hidden',
        'multi_options',
        'sold_count',
        'refund_count',
        'store_id',
        'order_period',
        'video',
        'reviewed', // 0 => reviewed // 1 => under review
        'free'  // 1 => offer 1 more // default = 0
    ];

    protected $hidden = ['pivot'];


    public function images() {
        return $this->hasMany('App\ProductImage', 'product_id')->select('id','image','main','product_id');
    }

    public function mainImage() {
        return $this->hasOne('App\ProductImage')->where('main', 1)->select('id','image','main','product_id');
    }

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }
    public function category_name () {
        $lang = session('lang');
        return $this->belongsTo('App\Category', 'category_id')->select('id','title_'.$lang.' as title');
    }

    public function brand() {
        return $this->belongsTo('App\Brand', 'brand_id');
    }

    public function subCategory() {
        return $this->belongsTo('App\SubCategory', 'sub_category_id');
    }

    public function options() {
        return $this->hasMany('App\ProductOption', 'product_id');
    }

    public function orderItems() {
        return $this->hasMany('App\OrderItem', 'product_id');
    }

    public function orders() {
        return $this->belongsToMany('App\Order', 'order_items', 'product_id','order_id')->withPivot('count');
    }

    public function properties() {
        return $this->belongsToMany('App\Option', 'product_properties', 'product_id', 'option_id');
    }

    public function propertiesEn() {
        return $this->belongsToMany('App\Option', 'product_properties', 'product_id', 'option_id')->select('options.id as option_id', 'options.title_en as title', 'product_properties.value_id');
    }

    public function propertiesAr() {
        return $this->belongsToMany('App\Option', 'product_properties', 'product_id', 'option_id')->select('options.id as option_id', 'options.title_ar as title', 'product_properties.value_id');
    }

    public function values() {
        return $this->belongsToMany('App\OptionValue', 'product_properties', 'product_id', 'value_id');
    }

    public function specValues() {
        return $this->belongsToMany('App\OptionValue', 'product_properties', 'product_id', 'value_id')->select('value_en as value', 'value_ar as value');
    }

    public function mOptions() {
        return $this->belongsToMany('App\MultiOption', 'product_multi_options', 'product_id', 'multi_option_id');
    }

    public function mOptionsValuesEn() {
        return $this->belongsToMany('App\MultiOptionValue', 'product_multi_options', 'product_id', 'multi_option_value_id')->select('value_en as value', 'multi_option_values.id as option_value_id');
    }

    public function mOptionsValuesAr() {
        return $this->belongsToMany('App\MultiOptionValue', 'product_multi_options', 'product_id', 'multi_option_value_id')->select('value_ar as value', 'multi_option_values.id as option_value_id');
    }

    public function multiOptions() {
        return $this->hasMany('App\ProductMultiOption', 'product_id');
    }

    public function multiOptionss() {
        return $this->hasMany('App\ProductMultiOption', 'product_id');
    }

    public function mOptionsWhere($id) {
        return $this->multiOptions()->with('multiOption', 'multiOptionValue')->where('product_multi_options.id', $id)->first();
    }

    public function productProperties() {
        return $this->hasMany('App\ProductProperty', 'product_id');
    }

    public function store() {
        return $this->belongsTo('App\Shop', 'store_id');
    }

    public function storeWithLogoNameOnly() {
        return $this->belongsTo('App\Shop', 'store_id')->select('id', 'name', 'logo');
    }


}
