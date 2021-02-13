<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();
//Route::group(['middleware' => ['web','auth']], function () {

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'
    , 'middleware' => ['web','auth']
], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // MainPageImages
    Route::delete('mainpageimages/destroy', 'MainPageImagesController@massDestroy')->name('mainpageimages.massDestroy');
    Route::post('mainpageimages/media', 'MainPageImagesController@storeMedia')->name('mainpageimages.storeMedia');
    Route::post('mainpageimages/ckmedia', 'MainPageImagesController@storeCKEditorImages')->name('mainpageimages.storeCKEditorImages');
    Route::resource('mainpageimages', 'MainPageImagesController');

    // ItemAdvertisements
    Route::delete('item_advertisements/destroy', 'ItemAdvertisementsController@massDestroy')->name('item_advertisements.massDestroy');
    Route::post('item_advertisements/media', 'ItemAdvertisementsController@storeMedia')->name('item_advertisements.storeMedia');
    Route::post('item_advertisements/ckmedia', 'ItemAdvertisementsController@storeCKEditorImages')->name('item_advertisements.storeCKEditorImages');
    Route::resource('item_advertisements', 'ItemAdvertisementsController');

    // Cities
    Route::delete('cities/destroy', 'CitiesController@massDestroy')->name('cities.massDestroy');
    Route::resource('cities', 'CitiesController');

    // Colors
    Route::delete('colors/destroy', 'ColorsController@massDestroy')->name('colors.massDestroy');
    Route::resource('colors', 'ColorsController');

    // Colors
    Route::delete('sizes/destroy', 'SizesController@massDestroy')->name('sizes.massDestroy');
    Route::resource('sizes', 'SizesController');

    // Colors
    Route::delete('brands/destroy', 'BrandsController@massDestroy')->name('brands.massDestroy');
    Route::resource('brands', 'BrandsController');

    // Departments
    Route::delete('departments/destroy', 'DepartmentsController@massDestroy')->name('departments.massDestroy');
    Route::post('departments/media', 'DepartmentsController@storeMedia')->name('departments.storeMedia');
    Route::post('departments/ckmedia', 'DepartmentsController@storeCKEditorImages')->name('departments.storeCKEditorImages');
    Route::resource('departments', 'DepartmentsController');
    Route::post('/upload_departments_excel', 'DepartmentsController@uploadExcel')->name('upload_departments_excel');



    //Department Excel
    Route::delete('department_excels/destroy', 'DepartmentExcelController@massDestroy')->name('department_excels.massDestroy');
    Route::resource('department_excels', 'DepartmentExcelController');
    Route::get('/department_excels/approve/{id}', 'DepartmentsController@uploadExcel')->name('department_excels.approve');


    // Categories
    Route::delete('categories/destroy', 'CategoriesController@massDestroy')->name('categories.massDestroy');
    Route::resource('categories', 'CategoriesController');

    // sub_categories
    Route::delete('sub_categories/destroy', 'SubCategoriesController@massDestroy')->name('sub_categories.massDestroy');
    Route::resource('sub_categories', 'SubCategoriesController');

    // MainProductType
    Route::delete('main_product_types/destroy', 'MainProductTypesController@massDestroy')->name('main_product_types.massDestroy');
    Route::resource('main_product_types', 'MainProductTypesController');

    // sub_product_types
    Route::delete('sub_product_types/destroy', 'SubProductTypesController@massDestroy')->name('sub_product_types.massDestroy');
    Route::resource('sub_product_types', 'SubProductTypesController');

    // MainProductServiceType
    Route::delete('main_product_service_types/destroy', 'MainProductServiceTypesController@massDestroy')->name('main_product_service_types.massDestroy');
    Route::resource('main_product_service_types', 'MainProductServiceTypesController');


    // sub_product_service_types
    Route::delete('sub_product_service_types/destroy', 'SubProductServiceTypesController@massDestroy')->name('sub_product_service_types.massDestroy');
    Route::resource('sub_product_service_types', 'SubProductServiceTypesController');


    // NewsCategories
    Route::delete('news_categories/destroy', 'NewsCategoriesController@massDestroy')->name('news_categories.massDestroy');
    Route::resource('news_categories', 'NewsCategoriesController');

    // news_sub_categories
    Route::delete('news_sub_categories/destroy', 'NewsSubCategoriesController@massDestroy')->name('news_sub_categories.massDestroy');
    Route::resource('news_sub_categories', 'NewsSubCategoriesController');

    // Coupon
    Route::delete('coupons/destroy', 'CouponsController@massDestroy')->name('coupons.massDestroy');
    Route::resource('coupons', 'CouponsController');

    // CustomField
    Route::delete('custom_fields/destroy', 'CustomFieldsController@massDestroy')->name('custom_fields.massDestroy');
    Route::resource('custom_fields', 'CustomFieldsController');

    // CustomField
    Route::delete('custom_field_options/destroy', 'CustomFieldOptionsController@massDestroy')->name('custom_field_options.massDestroy');
    Route::resource('custom_field_options', 'CustomFieldOptionsController');

    // Offers
    Route::delete('offers/destroy', 'OffersController@massDestroy')->name('offers.massDestroy');
    Route::post('offers/media', 'OffersController@storeMedia')->name('offers.storeMedia');
    Route::post('offers/ckmedia', 'OffersController@storeCKEditorImages')->name('offers.storeCKEditorImages');
    Route::resource('offers', 'OffersController');
    Route::post('/upload_offers_excel', 'OffersController@uploadExcel')->name('upload_offers_excel');


    //Offer Excel
    Route::delete('offer_excels/destroy', 'OfferExcelController@massDestroy')->name('offer_excels.massDestroy');
    Route::resource('offer_excels', 'OfferExcelController');
    Route::get('/offer_excels/approve/{id}', 'OffersController@uploadExcel')->name('offer_excels.approve');



    // Specializations
    Route::delete('specializations/destroy', 'SpecializationsController@massDestroy')->name('specializations.massDestroy');
    Route::resource('specializations', 'SpecializationsController');

    // Jobs
    Route::delete('jobs/destroy', 'JobsController@massDestroy')->name('jobs.massDestroy');
    Route::post('jobs/media', 'JobsController@storeMedia')->name('jobs.storeMedia');
    Route::post('jobs/ckmedia', 'JobsController@storeCKEditorImages')->name('jobs.storeCKEditorImages');
    Route::resource('jobs', 'JobsController');
    Route::post('/upload_jobs_excel', 'JobsController@uploadExcel')->name('upload_jobs_excel');

    // News
    Route::delete('news/destroy', 'NewsController@massDestroy')->name('news.massDestroy');
    Route::post('news/media', 'NewsController@storeMedia')->name('news.storeMedia');
    Route::post('news/ckmedia', 'NewsController@storeCKEditorImages')->name('news.storeCKEditorImages');
    Route::resource('news', 'NewsController');
    Route::post('/upload_news_excel', 'NewsController@uploadExcel')->name('upload_news_excel');



    //News Excel
    Route::delete('news_excels/destroy', 'NewsExcelController@massDestroy')->name('news_excels.massDestroy');
    Route::resource('news_excels', 'NewsExcelController');
    Route::get('/news_excels/approve/{id}', 'NewsController@uploadExcel')->name('news_excels.approve');

    // Notifications
    Route::delete('notifications/destroy', 'NotificationsController@massDestroy')->name('notifications.massDestroy');
    Route::resource('notifications', 'NotificationsController');

    // Job Offers
    Route::delete('job-offers/destroy', 'JobOfferController@massDestroy')->name('job-offers.massDestroy');
    Route::post('job-offers/media', 'JobOfferController@storeMedia')->name('job-offers.storeMedia');
    Route::post('job-offers/ckmedia', 'JobOfferController@storeCKEditorImages')->name('job-offers.storeCKEditorImages');
    Route::resource('job-offers', 'JobOfferController');

    // Traders
    Route::delete('traders/destroy', 'TraderController@massDestroy')->name('traders.massDestroy');
    Route::post('traders/media', 'TraderController@storeMedia')->name('traders.storeMedia');
    Route::post('traders/ckmedia', 'TraderController@storeCKEditorImages')->name('traders.storeCKEditorImages');
    Route::resource('traders', 'TraderController');
    Route::post('/upload_traders_excel', 'TraderController@uploadExcel')->name('upload_traders_excel');



    //Trader Excel
    Route::delete('trader_excels/destroy', 'TraderExcelController@massDestroy')->name('trader_excels.massDestroy');
    Route::resource('trader_excels', 'TraderExcelController');
    Route::get('/trader_excels/approve/{id}', 'TraderController@uploadExcel')->name('trader_excels.approve');


    // Traders
    Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    Route::resource('orders', 'OrdersController');
    Route::get('orders/download_pdf/{id}', 'OrdersController@download_pdf')->name('orders.download_pdf');

    // Products
    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');
    Route::post('products/media', 'ProductsController@storeMedia')->name('products.storeMedia');
    Route::post('products/ckmedia', 'ProductsController@storeCKEditorImages')->name('products.storeCKEditorImages');
    Route::resource('products', 'ProductsController');
    Route::post('/upload_products_excel', 'ProductsController@uploadExcel')->name('upload_products_excel');

    //Product Excel
    Route::delete('product_excels/destroy', 'ProductExcelController@massDestroy')->name('product_excels.massDestroy');
    Route::resource('product_excels', 'ProductExcelController');
    Route::get('/product_excels/approve/{id}', 'ProductsController@uploadExcel')->name('product_excels.approve');

    // products.variants
    Route::delete('variants/destroy', 'VariantsController@massDestroy')->name('variants.massDestroy');
    Route::post('variants/media', 'VariantsController@storeMedia')->name('variants.storeMedia');
    Route::post('variants/ckmedia', 'VariantsController@storeCKEditorImages')->name('variants.storeCKEditorImages');
    Route::resource('products.variants', 'VariantsController');
});
//});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
