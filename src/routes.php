<?php
// download route
Route::get('download/{token}/{id}', 'App\Http\Controllers\EasyfileController@download');
