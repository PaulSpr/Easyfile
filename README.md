#EasyFile
#### Centralised and easy file handling

#### Enable downloads
To handle downloads you must do several things:

1) Create a download controller. Something like this will suffice, but you can built in any authorisation in there as well
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use InteractiveStudios\EasyFile\EasyFile;

class EasyfileController extends Controller
{
    public function download( Request $request, $token, $id )
    {
        return EasyFile::respondWithDownload( $id, $token );
    }

}

```

2) Create a download route. Something like this would work
```
Route::get('download/{token}/{id}', 'EasyfileController@download');
```

3) If you want another route, than you should set that route in EasyFile in a way like this:
```
EasyFile::$downloadUrl = '/download/{token}/{id}'; // this is the default
```

Any download url that is generated will have this format.


## Using it

### 1-to-1
Ideal for avatars or other attributes that have a relation to a single unique file.

Create a simple Model subclass that extends EasyFile like this:
```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \InteractiveStudios\EasyFile\Easyfile;

class Festivallogo extends Easyfile
{
	// Optional use of the EasyImageTrait which helps with resizing images
	use \InteractiveStudios\EasyImage\EasyImageTrait;
}
```

Make sure that there is a foreign key on the model that has the relation. For example:
Festival should have a festivallogo_id

On the Festival model simplu declare the relation like this:
```
public function festivallogo()
{
	return $this->belongsTo('App\Festivallogo');
}
```

After an upload you can associate the file with the model ($festival in this case) like this:
```
if ($request->hasFile('logo')) {
	$logo = Festivallogo::newWithFile($request->file('logo'));
	$logo->save();

	$festival->festivallogo()->associate($logo);
}
```

Now offering a download is as easy as:
```
<a href="{{ $festival->festivallogo->downloadUrl() }}">Download</a>
```