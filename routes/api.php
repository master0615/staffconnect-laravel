<?php

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | is assigned the "api" middleware group. Enjoy building your API!
 * |
 */

// auth
Route::post('auth/login', 'Api\Auth\LoginController@login');
Route::post('auth/loginAs', 'Api\Auth\LoginController@loginAs')->middleware('auth:api', 'role:owner|admin');
Route::post('auth/refresh', 'Api\Auth\LoginController@refreshToken')->middleware('auth:api');
Route::post('auth/logout', 'Api\Auth\LoginController@logout');
Route::post('auth/logoutAs', 'Api\Auth\LoginController@logoutAs')->middleware('auth:api');

// attributes
Route::delete('attribute/{attrId}', 'Api\AttributeController@deleteAttribute')->middleware('auth:api', 'role:owner|admin');
Route::delete('attribute/category/{catId}', 'Api\AttributeController@deleteCategory')->middleware('auth:api', 'role:owner|admin');
Route::get('attribute/category/{catId?}', 'Api\AttributeController@getCategories')->middleware('auth:api');
Route::get('attribute/{attrId?}', 'Api\AttributeController@getAttributes')->middleware('auth:api');
Route::post('attribute', 'Api\AttributeController@createAttribute')->middleware('auth:api', 'role:owner|admin');
Route::post('attribute/category', 'Api\AttributeController@createCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('attribute/{attrId}', 'Api\AttributeController@updateAttribute')->middleware('auth:api', 'role:owner|admin');
Route::put('attribute/category/{catId}', 'Api\AttributeController@updateCategory')->middleware('auth:api', 'role:owner|admin');

//autocompletes
Route::get('autocomplete/chatThread/{q?}', 'Api\FilterController@chatThreads')->middleware('auth:api', 'role:owner|admin|staff');
Route::get('autocomplete/client/{q?}', 'Api\FilterController@clients')->middleware('auth:api', 'role:owner|admin');
Route::get('autocomplete/location/{q?}', 'Api\FilterController@locations')->middleware('auth:api', 'role:owner|admin');
Route::get('autocomplete/manager/{q?}', 'Api\FilterController@managers')->middleware('auth:api', 'role:owner|admin');
Route::get('autocomplete/roleRequirement/{q?}', 'Api\FilterController@roleRequirements')->middleware('auth:api', 'role:owner');
Route::get('autocomplete/tracking/{id}/options/{q?}', 'Api\FilterController@trackingOptions')->middleware('auth:api', 'role:owner|admin');
Route::get('autocomplete/workArea/{q?}', 'Api\FilterController@workAreas')->middleware('auth:api', 'role:owner|admin');

// clients
Route::get('client/{id?}', 'Api\ClientController@get')->middleware('auth:api', 'role:owner|admin');
Route::post('client', 'Api\ClientController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('client/{id}', 'Api\ClientController@update')->middleware('auth:api', 'role:owner|admin');
Route::delete('client/{id}', 'Api\ClientController@delete')->middleware('auth:api', 'role:owner|admin');

//flags
Route::delete('flag/{id}', 'Api\FlagController@delete')->middleware('auth:api', 'role:owner|admin');
Route::post('flag', 'Api\FlagController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('flag/{id}', 'Api\FlagController@update')->middleware('auth:api', 'role:owner|admin');
Route::get('flag/{id?}', 'Api\FlagController@get')->middleware('auth:api', 'role:owner|admin');
Route::get('shift/{id}/flag', 'Api\FlagController@shiftFlags')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{shiftId}/flag/{id}/{set}', 'Api\FlagController@set')->middleware('auth:api', 'role:owner|admin');

// get any file eg profile photo, shift photo, video etc
Route::get('file/{fileType}/{id}/{ext}/{thumbnail?}', 'Api\StorageController@getFile'); // no auth check as some photos must be available public. TODO add auth check in controller
Route::delete('file/{fileType}/{id}', 'Api\StorageController@deleteFile')->middleware('auth:api');

//helpers
Route::get('helpers/timezones', 'Api\SettingsController@getTimezones')->middleware('auth:api');
Route::post('helpers/roles/edit', 'Api\ShiftRoleController@getEdit')->middleware('auth:api', 'role:owner|admin');

// outsource comapanies setup
Route::delete('outsourceCompany/{id}', 'Api\OutsourceCompanyController@delete')->middleware('auth:api', 'role:owner|admin');
Route::post('outsourceCompany', 'Api\OutsourceCompanyController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('outsourceCompany/{id}', 'Api\OutsourceCompanyController@update')->middleware('auth:api', 'role:owner|admin');
Route::get('outsourceCompany/{id?}', 'Api\OutsourceCompanyController@get')->middleware('auth:api', 'role:owner|admin');

// pay levels
Route::delete('payLevel/{id}', 'Api\PayLevelController@deleteLevel')->middleware('auth:api', 'role:owner|admin');
Route::delete('payLevel/category/{id}', 'Api\PayLevelController@deleteCategory')->middleware('auth:api', 'role:owner|admin');
Route::get('payLevel/category/{id?}', 'Api\PayLevelController@getCategories')->middleware('auth:api');
Route::get('payLevel/{catId?}/{lvlId?}', 'Api\PayLevelController@getLevels')->middleware('auth:api');
Route::post('payLevel', 'Api\PayLevelController@createLevel')->middleware('auth:api', 'role:owner|admin');
Route::post('payLevel/category', 'Api\PayLevelController@createCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('payLevel/{id}', 'Api\PayLevelController@updateLevel')->middleware('auth:api', 'role:owner|admin');
Route::put('payLevel/category/{id}', 'Api\PayLevelController@updateCategory')->middleware('auth:api', 'role:owner|admin');

// profile
Route::get('profile/{id}', 'Api\ProfileController@getProfile')->middleware('auth:api');

// profile admin notes
Route::get('profile/{id}/adminNote', 'Api\ProfileAdminNoteController@get')->middleware('auth:api', 'role:owner|admin');
Route::post('profile/{id}/adminNote', 'Api\ProfileAdminNoteController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('profile/adminNote/{id}', 'Api\ProfileAdminNoteController@update')->middleware('auth:api', 'role:owner|admin');
Route::delete('profile/adminNote/{id}', 'Api\ProfileAdminNoteController@delete')->middleware('auth:api', 'role:owner|admin');

//profile attributes
Route::get('profile/{id}/attributes', 'Api\ProfileController@getAttributes')->middleware('auth:api');
Route::put('profile/{id}/attribute', 'Api\ProfileController@setAttribute')->middleware('auth:api');

// profile setup
Route::delete('profileStructure/element/{id}', 'Api\ProfileSetupController@deleteElement')->middleware('auth:api', 'role:owner|admin');
Route::delete('profileStructure/category/{id}', 'Api\ProfileSetupController@deleteCategory')->middleware('auth:api', 'role:owner|admin');
Route::delete('profileStructure/option/{id}', 'Api\ProfileSetupController@deleteListOption')->middleware('auth:api', 'role:owner|admin');
Route::get('profileStructure/category/{id?}', 'Api\ProfileSetupController@getCategories')->middleware('auth:api');
Route::get('profileStructure/element/{id?}', 'Api\ProfileSetupController@getElements')->middleware('auth:api');
Route::post('profileStructure/element', 'Api\ProfileSetupController@createElement')->middleware('auth:api', 'role:owner|admin');
Route::post('profileStructure/element/{id}/option', 'Api\ProfileSetupController@createListOption')->middleware('auth:api', 'role:owner|admin');
Route::post('profileStructure/category', 'Api\ProfileSetupController@createCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('profileStructure/element/{id}', 'Api\ProfileSetupController@updateElement')->middleware('auth:api', 'role:owner|admin');
Route::put('profileStructure/category/{id}', 'Api\ProfileSetupController@updateCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('profileStructure/option/{id}', 'Api\ProfileSetupController@updateListOption')->middleware('auth:api', 'role:owner|admin');
Route::get('profile/structure', 'Api\ProfileController@getStructure')->middleware('auth:api', 'role:owner|admin');

// profile unavailability
Route::delete('profile/unavailability/{id}', 'Api\UnavailabilityController@deleteUnavailability')->middleware('auth:api');
Route::get('profile/{user_id}/unavailability', 'Api\UnavailabilityController@getUnavailability')->middleware('auth:api');
Route::post('profile/unavailability', 'Api\UnavailabilityController@createUnavailability')->middleware('auth:api');

// profile docs
Route::get('profile/{id}/documents', 'Api\ProfileController@getDocuments')->middleware('auth:api');
Route::post('profile/{id}/document', 'Api\ProfileDocumentController@upload')->middleware('auth:api');
Route::put('profileDocument/{id}/lock/{set}', 'Api\ProfileDocumentController@lock')->middleware('auth:api', 'role:owner|admin');
Route::put('profileDocument/{id}/adminOnly/{set}', 'Api\ProfileDocumentController@adminOnly')->middleware('auth:api', 'role:owner|admin');

// profile photos
Route::get('profile/{id}/photos', 'Api\ProfileController@getPhotos')->middleware('auth:api');
Route::post('profile/{id}/photo', 'Api\ProfilePhotoController@upload')->middleware('auth:api');
Route::put('profilePhoto/{profilePhotoId}/lock/{set}', 'Api\ProfilePhotoController@lock')->middleware('auth:api', 'role:owner|admin');
Route::put('profilePhoto/{profilePhotoId}/adminOnly/{set}', 'Api\ProfilePhotoController@adminOnly')->middleware('auth:api', 'role:owner|admin');
Route::put('profilePhoto/{profilePhotoId}/rotate/{deg}', 'Api\ProfilePhotoController@rotate')->middleware('auth:api');

// profile work areas
Route::get('profile/{id}/workAreas', 'Api\ProfileController@getWorkAreas')->middleware('auth:api');
Route::put('profile/{id}/workArea', 'Api\ProfileController@setWorkArea')->middleware('auth:api');

// profile videos
Route::get('profile/{id}/videos', 'Api\ProfileController@getVideos')->middleware('auth:api');
Route::post('profile/{id}/video', 'Api\ProfileVideoController@upload')->middleware('auth:api');
Route::put('profileVideo/{id}/lock/{set}', 'Api\StorageController@lockProfileVideo')->middleware('auth:api', 'role:owner|admin');
Route::put('profileVideo/{id}/adminOnly/{set}', 'Api\StorageController@adminOnlyProfileVideo')->middleware('auth:api', 'role:owner|admin');

// ratings setup
Route::delete('rating/{id}', 'Api\RatingController@delete')->middleware('auth:api', 'role:owner|admin');
Route::post('rating', 'Api\RatingController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('rating/{id}', 'Api\RatingController@update')->middleware('auth:api', 'role:owner|admin');
Route::get('rating/{id?}', 'Api\RatingController@get')->middleware('auth:api', 'role:owner|admin');

// settings
Route::get('setting/{id?}', 'Api\SettingsController@get')->middleware('auth:api', 'role:owner');
Route::get('setting/{id}/options', 'Api\SettingsController@options')->middleware('auth:api', 'role:owner');
Route::put('setting/{id}', 'Api\SettingsController@set')->middleware('auth:api', 'role:owner');

// Shifts
Route::get('shifts/filter/{from}/{to}/{q?}', 'Api\FilterController@shifts')->middleware('auth:api', 'role:owner|admin');
Route::get('shifts/edit', 'Api\ShiftController@getEdit')->middleware('auth:api', 'role:owner|admin');
Route::post('shifts/edit', 'Api\ShiftController@updateMultiple')->middleware('auth:api', 'role:owner|admin');
Route::get('shift/{id}', 'Api\ShiftController@get')->middleware('auth:api');
Route::get('shift/{id}/checks', 'Api\ShiftController@checks')->middleware('auth:api', 'role:owner|admin');
Route::get('/shifts/{from}/{to}/{view}/{pageSize?}/{pageNumber?}/{filters?}/{sorts?}', 'Api\ShiftController@getShifts')->middleware('auth:api');
Route::post('shift', 'Api\ShiftController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{id}/lock/{set}', 'Api\ShiftController@lock')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{id}/publish/{set}', 'Api\ShiftController@publish')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{id}', 'Api\ShiftController@update')->middleware('auth:api', 'role:owner|admin');
Route::delete('shift/{id}', 'Api\ShiftController@delete')->middleware('auth:api', 'role:owner|admin');

// shift admin notes
Route::get('shift/{id}/adminNote', 'Api\ShiftAdminNoteController@get')->middleware('auth:api', 'role:owner|admin');
Route::post('shift/{id}/adminNote', 'Api\ShiftAdminNoteController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/adminNote/{id}', 'Api\ShiftAdminNoteController@update')->middleware('auth:api', 'role:owner|admin');
Route::delete('shift/adminNote/{id}', 'Api\ShiftAdminNoteController@delete')->middleware('auth:api', 'role:owner|admin');

// shift admin note types
Route::delete('shiftAdminNoteType/{id}', 'Api\ShiftAdminNoteController@deleteType')->middleware('auth:api', 'role:owner|admin');
Route::post('shiftAdminNoteType', 'Api\ShiftAdminNoteController@createType')->middleware('auth:api', 'role:owner|admin');
Route::put('shiftAdminNoteType/{id}', 'Api\ShiftAdminNoteController@updateType')->middleware('auth:api', 'role:owner|admin');
Route::get('shiftAdminNoteType/{id?}', 'Api\ShiftAdminNoteController@getType')->middleware('auth:api', 'role:owner|admin');

// Shift Groups
Route::get('shiftGroup/{id}', 'Api\ShiftGroupController@get')->middleware('auth:api', 'role:owner|admin');
Route::post('shiftGroup', 'Api\ShiftGroupController@create')->middleware('auth:api', 'role:owner|admin');
Route::delete('shiftGroup/{id}', 'Api\ShiftGroupController@delete')->middleware('auth:api', 'role:owner|admin');

// shift managers
Route::get('shift/{id}/manager', 'Api\ShiftController@getManagers')->middleware('auth:api');
Route::delete('shift/{shiftIds}/manager/{userIds}', 'Api\ShiftController@unsetManagers')->middleware('auth:api', 'role:owner|admin');
Route::post('shift/{shiftId}/manager/{userIds}', 'Api\ShiftController@addManagers')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{shiftIds}/manager/{userIds}', 'Api\ShiftController@setManagers')->middleware('auth:api', 'role:owner|admin');

// Shift roles
Route::post('shifts/roles/edit', 'Api\ShiftRoleController@getEdit')->middleware('auth:api', 'role:owner|admin');
Route::delete('shift/role/{id}', 'Api\ShiftRoleController@delete')->middleware('auth:api', 'role:owner|admin');
Route::get('shift/role/{id}/{staff?}', 'Api\ShiftRoleController@get')->middleware('auth:api', 'role:owner|admin');
Route::post('shift/{id}/role', 'Api\ShiftRoleController@create')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/role/{id}/{direction}', 'Api\ShiftRoleController@order')->middleware('auth:api', 'role:owner|admin');

// Shift Role Pay Items
Route::delete('role/payItem/{id}', 'Api\ShiftRoleController@deletePayItem')->middleware('auth:api', 'role:owner|admin');
Route::get('role/payItem/{id}', 'Api\ShiftRoleController@getPayItem')->middleware('auth:api', 'role:owner|admin');
Route::get('role/{id}/payItem', 'Api\ShiftRoleController@getPayItems')->middleware('auth:api', 'role:owner|admin');
Route::post('role/{id}/payItem', 'Api\ShiftRoleController@createPayItem')->middleware('auth:api', 'role:owner|admin');
Route::put('role/payItem/{id}', 'Api\ShiftRoleController@updatePayItem')->middleware('auth:api', 'role:owner|admin');

// Shift Role Requirement
Route::post('role/{id}/roleRequirement', 'Api\ShiftRoleController@createRoleRequirement')->middleware('auth:api', 'role:owner|admin');
Route::get('role/{id}/roleRequirements', 'Api\ShiftRoleController@getRoleRequirements')->middleware('auth:api', 'role:owner|admin');
Route::delete('role/roleRequirement/{id}', 'Api\ShiftRoleController@deleteRoleRequirement')->middleware('auth:api', 'role:owner|admin');

// Shift Role Staff
Route::post('role/{id}/apply', 'Api\RoleStaffController@apply')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/{id}/notAvailable', 'Api\RoleStaffController@notAvailable')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/applyCancel/{id}', 'Api\RoleStaffController@applyCancel')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/{id}/assign', 'Api\RoleStaffController@assign')->middleware('auth:api', 'role:owner|admin');
Route::post('role/checkIn/{id}', 'Api\RoleStaffController@checkIn')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/checkOut/{id}', 'Api\RoleStaffController@checkOut')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/complete/{id}', 'Api\RoleStaffController@complete')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/confirm/{id}', 'Api\RoleStaffController@confirm')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/{id}/notavailable', 'Api\RoleStaffController@notavailable')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/replace/{id}', 'Api\RoleStaffController@replace')->middleware('auth:api', 'role:owner|admin|staff');
Route::post('role/replaceCancel/{id}', 'Api\RoleStaffController@replaceCancel')->middleware('auth:api', 'role:owner|admin|staff');
Route::put('role/update/{id}', 'Api\RoleStaffController@update')->middleware('auth:api', 'role:owner|admin');

// Shift Role Staff Pay Items
Route::delete('roleStaff/payItem/{id}', 'Api\RoleStaffController@deletePayItem')->middleware('auth:api', 'role:owner|admin');
Route::get('roleStaff/payItem/{id}', 'Api\RoleStaffController@getPayItem')->middleware('auth:api', 'role:owner|admin');
Route::get('roleStaff/{id}/payItem', 'Api\RoleStaffController@getPayItems')->middleware('auth:api', 'role:owner|admin');
Route::post('roleStaff/{id}/payItem', 'Api\RoleStaffController@createPayItem')->middleware('auth:api', 'role:owner|admin');
Route::put('roleStaff/payItem/{id}', 'Api\RoleStaffController@updatePayItem')->middleware('auth:api', 'role:owner|admin');

// shift work areas
Route::get('shift/{shiftId}/WorkArea', 'Api\ShiftController@getWorkAreas')->middleware('auth:api', 'role:owner|admin');
Route::delete('shift/{shiftId/workArea/workAreaIds}', 'Api\ShiftController@unsetWorkAreas')->middleware('auth:api', 'role:owner|admin');
Route::post('shift/{shiftId}/workArea{workAreaIds}', 'Api\ShiftController@addWorkAreas')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{shiftId}/workArea/{workAreaIds}', 'Api\ShiftController@setWorkAreas')->middleware('auth:api', 'role:owner|admin');

// Tracking Categories
Route::delete('tracking/option/{id}', 'Api\TrackingCategoryController@deleteOption')->middleware('auth:api', 'role:owner|admin');
Route::delete('tracking/category/{id}', 'Api\TrackingCategoryController@deleteCategory')->middleware('auth:api', 'role:owner|admin');
Route::get('tracking/category/{id?}', 'Api\TrackingCategoryController@getCategories')->middleware('auth:api');
Route::get('tracking/option/{catId?}/{id?}', 'Api\TrackingCategoryController@getOptions')->middleware('auth:api');
Route::post('tracking/option', 'Api\TrackingCategoryController@createOption')->middleware('auth:api', 'role:owner|admin');
Route::post('tracking/category', 'Api\TrackingCategoryController@createCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('tracking/option/{id}', 'Api\TrackingCategoryController@updateOption')->middleware('auth:api', 'role:owner|admin');
Route::put('tracking/category/{id}', 'Api\TrackingCategoryController@updateCategory')->middleware('auth:api', 'role:owner|admin');
// shift tracking options
Route::get('shift/{shiftId}/tracking/{trackingCatId?}', 'Api\ShiftController@getTrackingOptions')->middleware('auth:api');
Route::delete('shift/{shiftId/tracking/{trackingOptionIds}', 'Api\ShiftController@unsetTrackingOptions')->middleware('auth:api', 'role:owner|admin');
Route::post('shift/{shiftId}/tracking/{trackingOptionIds}', 'Api\ShiftController@addTrackingOptions')->middleware('auth:api', 'role:owner|admin');
Route::put('shift/{shiftId}/tracking/{trackingOptionIds}', 'Api\ShiftController@setTrackingOptions')->middleware('auth:api', 'role:owner|admin');

// Users
Route::get('users/filter/{q?}', 'Api\FilterController@users')->middleware('auth:api', 'role:owner');
Route::get('users/typeFilter/{q?}', 'Api\FilterController@userTypes')->middleware('auth:api', 'role:owner');
Route::delete('users/{userId}', 'Api\UserController@delete')->middleware('auth:api', 'role:owner');

// get users for user table
Route::get('users/{pageSize}/{pageNumber}/{filters}/{sorts?}', 'Api\UserController@index')->middleware('auth:api', 'role:owner|admin');
Route::get('users/{id}', 'Api\UserController@show')->middleware('auth:api', 'role:owner|admin');
Route::post('user', 'Api\UserController@create')->middleware('auth:api', 'role:owner|admin');
// update user profile
Route::put('profile/{userId}/{profileElementId}', 'Api\ProfileController@update')->middleware('auth:api');
// set main profile photo
Route::put('profile/{userId}/photo/{profilePhotoId}', 'Api\ProfilePhotoController@setMain')->middleware('auth:api');

// user attributes
Route::get('user/attributes/{userId}', 'Api\UserController@getAttributes')->middleware('auth:api');
Route::delete('user/attribute/{attributeId}/{userId}', 'Api\UserController@unsetAttribute')->middleware('auth:api');
Route::put('user/attribute/{attributeId}/{userId}', 'Api\UserController@setAttribute')->middleware('auth:api');
// user outsource companies
Route::get('user/outsourceCompany/{userId}', 'Api\UserController@getOutsourceCompanies')->middleware('auth:api');
Route::delete('user/outsourceCompany/{outsourceCompanyId}/{userId}', 'Api\UserController@unsetOutsourceCompany')->middleware('auth:api');
Route::put('user/outsourceCompany/{outsourceCompanyId}/{userId}', 'Api\UserController@setOutsourceCompany')->middleware('auth:api');
// user pay levels
Route::get('user/{id}/payLevel', 'Api\UserController@getPayLevels')->middleware('auth:api');
Route::delete('user/{id}/payLevel/{payLevelId}', 'Api\UserController@unsetPayLevel')->middleware('auth:api');
Route::put('user/{id}/payLevel/{payLevelId}', 'Api\UserController@setPayLevel')->middleware('auth:api');
// user ratings
Route::get('user/{id}/rating', 'Api\UserController@getRatings')->middleware('auth:api');
Route::put('user/{id}/rating/{ratingId}/{score}', 'Api\UserController@setRating')->middleware('auth:api');

// work areas
Route::delete('workArea/{id}', 'Api\WorkAreaController@deleteWorkArea')->middleware('auth:api', 'role:owner|admin');
Route::delete('workArea/category/{id}', 'Api\WorkAreaController@deleteCategory')->middleware('auth:api', 'role:owner|admin');
Route::get('workArea/category/{id?}', 'Api\WorkAreaController@getCategories')->middleware('auth:api');
Route::get('workArea/{id?}', 'Api\WorkAreaController@getWorkAreas')->middleware('auth:api');
Route::post('workArea', 'Api\WorkAreaController@createWorkArea')->middleware('auth:api', 'role:owner|admin');
Route::post('workArea/category', 'Api\WorkAreaController@createCategory')->middleware('auth:api', 'role:owner|admin');
Route::put('workArea/{id}', 'Api\WorkAreaController@updateWorkArea')->middleware('auth:api', 'role:owner|admin');
Route::put('workArea/category/{id}', 'Api\WorkAreaController@updateCategory')->middleware('auth:api', 'role:owner|admin');
// user work areas
Route::get('user/{id}/workArea', 'Api\UserController@getWorkAreas')->middleware('auth:api');
Route::delete('user/{id}/workArea/{workAreaId}', 'Api\UserController@unsetWorkArea')->middleware('auth:api');
Route::put('user/{id}/workArea/{workAreaId}', 'Api\UserController@setWorkArea')->middleware('auth:api');

// user constant messaging
Route::post('user/device', 'Api\DeviceController@registerDevice');
Route::put('user/device/{id}', 'Api\DeviceController@updateDevice');
Route::delete('user/device/{id}', 'Api\DeviceController@removeDevice');

// chat
Route::post('chat/message', 'Api\UserChatController@sendMessage')->middleware('auth:api');
Route::post('chat/thread/{id}/user/{userId}', 'Api\UserChatController@addUser')->middleware('auth:api');
Route::get('chat/threads', 'Api\UserChatController@getThreads')->middleware('auth:api');
Route::get('chat/thread/{id}/{pageNumber?}', 'Api\UserChatController@getMessages')->middleware('auth:api');
Route::get('chat/unread/{id}', 'Api\UserChatController@getUnreadMessages')->middleware('auth:api');
Route::put('chat/thread/{id}/read', 'Api\UserChatController@threadRead')->middleware('auth:api');
Route::put('chat/thread/{id}', 'Api\UserChatController@updateThread')->middleware('auth:api');
Route::delete('chat/thread/{id}/user/{userId}', 'Api\UserChatController@removeUser')->middleware('auth:api');
