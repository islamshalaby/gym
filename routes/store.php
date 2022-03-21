<?php

// admin  Routes
Route::group(['middleware'=>'language','prefix' => "admin-panel",'namespace' => "Admin\Store"] , function($router){

    Route::get('store/home' , 'HomeController@home')->name('store.home_panel');

    // Shops Route
    Route::group([
        "prefix" => "shops"
    ] , function($router){
        Route::get('/show' , 'ShopController@index')->name('shops.show');
        Route::get('add' , 'ShopController@AddGet')->name('shops.create');
        Route::post('add' , 'ShopController@AddPost');
        Route::get('make_famous/{id}' , 'ShopController@make_famous')->name('shop.make_famous');
        Route::get('edit/{store}' , 'ShopController@EditGet')->name('shops.edit');
        Route::post('edit/{store}' , 'ShopController@EditPost');
        Route::get('details/{store}' , 'ShopController@details')->name('shops.details');
        Route::get('action/{store}/{status}' , 'ShopController@action')->name('shops.action');
    });
    Route::get('shops_famous' , 'ShopController@famous')->name('shops_famous');


    // Categories Route
    Route::group([
        "prefix" => "categories"
    ], function ($router) {
        Route::get('add', 'CategoryController@AddGet')->name('shop.categories.create');
        Route::post('add', 'CategoryController@AddPost')->name('shop.categories.store');
        Route::get('show', 'CategoryController@show')->name('shop.categories.show');
        Route::get('edit/{id}', 'CategoryController@EditGet')->name('shop.categories.edit');
        Route::post('edit/{id}', 'CategoryController@EditPost')->name('shop.categories.update');
        Route::get('delete/{id}', 'CategoryController@delete')->name('shop.categories.delete');
        Route::get('details/{category}' , 'CategoryController@details')->name('categories.details');
    });

    // Home Properties Route
    Route::group([
        "prefix" => "properties"
    ], function($router){
        Route::get('show' , 'OptionsController@show')->name('options.index');
        Route::get('add' , 'OptionsController@AddGet')->name('options.add');
        Route::post('add' , 'OptionsController@AddPost');
        Route::get('edit/{option}' , 'OptionsController@EditGet')->name('options.edit');
        Route::post('edit/{option}' , 'OptionsController@EditPost');
        Route::get('delete/{option}' , 'OptionsController@delete')->name('options.delete');
    });

    // Areas Route
    Route::group([
        "prefix" => "areas"
    ], function($router){
        Route::get('show' , 'AreasController@show')->name('areas.index');
        Route::get('show-governorates' , 'AreasController@getGovernorates')->name('areas.governorates.index');
        Route::get('add' , 'AreasController@AddGet')->name('areas.add');
        Route::post('add' , 'AreasController@AddPost');
        Route::get('add-governorates' , 'AreasController@getAddGovernorate')->name('areas.governorates.add');
        Route::post('add-governorates' , 'AreasController@postAddGovernorate');
        Route::get('edit/{area}' , 'AreasController@EditGet')->name('areas.edit');
        Route::post('edit/{area}' , 'AreasController@EditPost');
        Route::get('edit-governorates/{governorate}' , 'AreasController@getEditGovernorate')->name('areas.governorates.edit');
        Route::post('edit-governorates/{governorate}' , 'AreasController@postEditGovernorate');
        Route::get('delete/{area}' , 'AreasController@delete')->name('areas.delete');
        Route::get('delete-governorates/{governorate}' , 'AreasController@getDeleteGovernorate')->name('areas.governorates.delete');
        Route::get('details/{area}' , 'AreasController@details')->name('areas.details');
        Route::get('delete-delivery-area/{cost}' , 'AreasController@deleteDeliveryArea')->name('areas.delivery.delete');
        Route::get('details-governorates/{governorate}' , 'AreasController@getGovernorateDetails')->name('areas.governorates.details');
    });

    // Areas Route
    Route::group([
        "prefix" => "deliver-cost"
    ], function($router){
        Route::get('show/deliverycost/{area}' , 'AreasController@show_delivery_costs')->name('areas.show.delivercost');
        Route::get('deliverycost' , 'AreasController@deliver_cost_areas')->name('areas.byArea.delivercost');
        Route::get('add/deliverycost/{area}' , 'AreasController@getAddDeliveryByArea')->name('areas.add.byArea.delivercost');
        Route::post('add/deliverycost/{area}' , 'AreasController@add_deliver_cost_post');
        Route::get('edit/deliverycost/{area}/{cost}' , 'AreasController@edit_delivery_cost_get')->name('edit_delivery_cost_get');
        Route::post('edit/deliverycost/{area}/{cost}' , 'AreasController@edit_delivery_cost_post');
        Route::get('delete/deliverycost/{cost}' , 'AreasController@deleteDeliveryCost')->name('delete_delivery_cost_get');
        Route::get('fetch-stores-by-area/{area}' , 'AreasController@fetchStoresByArea');
        Route::get('add-by-governorate' , 'AreasController@addDeliveryCostByGovernorate')->name('areas.add.deliveryCostByGovernorate');
        Route::get('adddelivery-by-governorate/{governorate}' , 'AreasController@getAddDeliveryByGovernorate')->name('areas.adddelivery.deliveryCostByGovernorate');
        Route::post('adddelivery-by-governorate/{governorate}' , 'AreasController@add_deliver_cost_post_by_governorate');
    });
});

// admin  Routes
Route::group(['middleware'=>['language','shop'],'prefix' => "shop-panel",'namespace' => "Shop_admin"] , function($router){

    Route::get('shop/home' , 'HomeController@home')->name('shop.home');

    Route::get('/logout' , 'HomeController@logout')->name('shop.logout');
    Route::get('/profile' , 'HomeController@profile')->name('shop.profile');
    Route::post('/profile' , 'HomeController@updateprofile');

    // Products Route
    Route::group([
        "prefix" => "products"
    ], function($router){
        Route::get('show' , 'ProductController@show')->name('shops.products.index');
        Route::get('show' , 'ProductController@show')->name('shops.products.show');
        Route::get('make_free_gift/{id}' , 'ProductController@make_free_gift')->name('shops.products.make_free_gift');
        Route::get('fetchbrands/{category}' , 'ProductController@fetch_category_brands');
        Route::get('fetchsubcategories/{brand}' , 'ProductController@fetch_brand_sub_categories');
        Route::get('fetchproducts/{subCategory}' , 'ProductController@sub_category_products');
        Route::get('fetchcategoryproducts/{category}' , 'ProductController@fetch_category_products');
        Route::get('fetchproductsbystore/{store}' , 'ProductController@fetch_products_by_store');
        Route::get('fetchbrandproducts/{brand}' , 'ProductController@fetch_brand_products');
        Route::get('fetchcategoryoptions/{category}' , 'ProductController@fetch_category_options');
        Route::get('fetchsubcategorymultioptions/{category}' , 'ProductController@fetch_sub_category_multi_options');
        Route::get('validatebarcodeunique/{type}/{text}' , 'ProductController@validate_barcode_unique');
        Route::get('edit/{product}' , 'ProductController@EditGet')->name('products.edit');
        Route::post('edit/{product}' , 'ProductController@EditPost');
        Route::get('delete/productimage/{productImage}' , 'ProductController@delete_product_image')->name("productImage.delete");
        Route::get('details/{product}' , 'ProductController@details')->name('products.details');
        Route::get('delete/{product}' , 'ProductController@delete')->name('products.delete');
        Route::get('search' , 'ProductController@product_search')->name('products.search');
        Route::get('searched' , 'ProductController@product_search');
        Route::post('update/quantity/{product}' , 'ProductController@update_quantity')->name('update.quantity');
        Route::post('update/quantity/option/{option}' , 'ProductController@update_quantity_m_option')->name('option.update.quantity');
        Route::get('add' , 'ProductController@AddGet')->name('products.add');
        Route::post('add' , 'ProductController@AddPost');
        Route::get('hide/{product}/{status}' , 'ProductController@visibility_status_product')->name('products.visibility.status');
        Route::get('getbysubcat' , 'ProductController@get_product_by_sub_cat')->name('products.getbysubcat');
        Route::get('fetchsubcategorybycategory/{category}' , 'ProductController@fetch_sub_categories_by_category');
        Route::get('review/{product}/{status}' , 'ProductController@review_product')->name('products.review');
        Route::get('action-offer/{product}/{status}' , 'ProductController@actionFreeProduct')->name('products.action.offer');
        Route::get('action-offer' , 'ProductController@getOffers')->name('products.offers');
        Route::get('/offers' , 'ShopController@getProductOffers')->name('shops.products.offers');
        Route::post('/offers' , 'ShopController@updateOfferImage')->name('shops.products.update_image');
    });


    // Offer Control Route
    Route::group([
        "prefix" => "offers_control"
    ], function($router){
        Route::get('add' , 'OffersControlController@AddGet')->name('offers_control.add');
        Route::post('add' , 'OffersControlController@AddPost');
        Route::post('sort' , 'OffersControlController@updateOffersSorting')->name('offers_control.sort');
        Route::get('edit/{section}' , 'OffersControlController@EditGet')->name('offers_control.edit');
        Route::post('edit/{section}' , 'OffersControlController@EditPost');
        Route::get('show' , 'OffersControlController@show')->name('offers_control.index');
        Route::get('details/{section}' , 'OffersControlController@details')->name('offers_control.details');
        Route::get('delete/{section}' , 'OffersControlController@delete')->name('offers_control.delete');
    });





    // Orders Route
    Route::group([
        "prefix" => "orders"
    ], function($router){
        Route::get('show' , 'OrderController@show')->name('orders.index');
        Route::get('sub-orders' , 'OrderController@showSubOrders')->name('orders.subOrders.index');
        Route::get('delivery-reports' , 'OrderController@showDeliveryReports')->name('orders.deliveryReports.index');
        Route::get('products-orders' , 'OrderController@showProductsOrders')->name('orders.productsOrders.index');
        Route::get('action/items/{item}' , 'OrderController@order_items_actions')->name('orders.items.action');
        Route::get('action/order/{item}' , 'OrderController@order_actions')->name('orders.subo.action');
        Route::get('action/sub/{order}' , 'OrderController@action_sub_order')->name('orders.sub.action');
        Route::get('action/{order}/{status}' , 'OrderController@action_order')->name('orders.action');
        Route::get('cancel/{type}/{orderId}' , 'OrderController@cancelOrder')->name('orders.cancel');
        Route::get('details/current/now/{id}' , 'OrderController@order_details')->name('current.orders.details.now');
        Route::get('details-sub-order/{order}' , 'OrderController@subOrdersDetails')->name('orders.sub_order.details');
        Route::get('filter/{status}' , 'OrderController@filter_orders')->name('orders.filter');
        Route::get('fetchbyarea' , 'OrderController@fetch_orders_by_area')->name('orders.fetchbyarea');
        Route::get('fetchbydate' , 'OrderController@fetch_orders_date')->name('orders.fetchbydate');
        Route::get('fetchbypayment' , 'OrderController@fetch_order_payment_method')->name('orders.fetchbypayment');
        Route::get('fetchbysubnumber' , 'OrderController@fetch_order_by_sub_order_number')->name('orders.fetchbysubnumber');
        Route::get('invoice/{order}' , 'OrderController@getInvoice')->name('orders.invoice');
        Route::get('size/details/{item}' , 'OrderController@order_size_details')->name('orders.size.details');
    });

    // Areas Route
    Route::group([
        "prefix" => "deliver-cost"
    ], function($router){
        Route::get('show/deliverycost/{area}' , 'AreasController@show_delivery_costs')->name('areas.show.delivercost');
        Route::get('shop/deliverycost' , 'AreasController@deliver_cost_areas')->name('shop.areas.byArea.delivercost');
        Route::get('shop/add/deliverycost/{area}' , 'AreasController@getAddDeliveryByArea')->name('shop.areas.add.byArea.delivercost');
        Route::post('shop/add/deliverycost/{area}' , 'AreasController@add_deliver_cost_post')->name('shop.areas.make.byArea.delivercost');;
        Route::get('edit/deliverycost/{area}/{cost}' , 'AreasController@edit_delivery_cost_get')->name('edit_delivery_cost_get');
        Route::post('edit/deliverycost/{area}/{cost}' , 'AreasController@edit_delivery_cost_post');
        Route::get('delete/deliverycost/{cost}' , 'AreasController@deleteDeliveryCost')->name('delete_delivery_cost_get');
        Route::get('fetch-stores-by-area/{area}' , 'AreasController@fetchStoresByArea');
        Route::get('shop/add-by-governorate' , 'AreasController@addDeliveryCostByGovernorate')->name('shop.areas.add.deliveryCostByGovernorate');
        Route::get('shop/adddelivery-by-governorate/{governorate}' , 'AreasController@getAddDeliveryByGovernorate')->name('shop.areas.adddelivery.deliveryCostByGovernorate');
        Route::post('shop/adddelivery-by-governorate/{governorate}' , 'AreasController@add_deliver_cost_post_by_governorate');
    });


});
