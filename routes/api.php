<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Advertisements
    Route::post('advertisements/media', 'AdvertisementsApiController@storeMedia')->name('advertisements.storeMedia');
    Route::apiResource('advertisements', 'AdvertisementsApiController');

    // Cities
    Route::apiResource('cities', 'CitiesApiController');

    // Departments
    Route::post('departments/media', 'DepartmentsApiController@storeMedia')->name('departments.storeMedia');
    Route::apiResource('departments', 'DepartmentsApiController');

    // Categories
    Route::apiResource('categories', 'CategoriesApiController');

    // Offers
    Route::apiResource('offers', 'OffersApiController');

    // Specializations
    Route::apiResource('specializations', 'SpecializationsApiController');

    // Jobs
    Route::post('jobs/media', 'JobsApiController@storeMedia')->name('jobs.storeMedia');
    Route::apiResource('jobs', 'JobsApiController');

    // News
    Route::post('news/media', 'NewsApiController@storeMedia')->name('news.storeMedia');
    Route::apiResource('news', 'NewsApiController');

    // Notifications
    Route::apiResource('notifications', 'NotificationsApiController');

    // Job Offers
    Route::post('job-offers/media', 'JobOfferApiController@storeMedia')->name('job-offers.storeMedia');
    Route::apiResource('job-offers', 'JobOfferApiController');
});
