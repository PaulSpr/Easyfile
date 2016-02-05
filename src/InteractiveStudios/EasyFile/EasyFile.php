<?php
namespace InteractiveStudios\EasyFile;

use Illuminate\Database\Eloquent\Model;

use File;

class Easyfile extends Model
{

	protected $table = 'easyfiles';

	// public setable statics
	public static $storageLocation = '/app/{instance_id}/{id}/{filename}'; // within storage/app

	public static $downloadUrl = '/download/{token}/{id}';

	private $tempFile;

	public static $tokenLength		= 8; // if the offset is 0, maxlength can be 32
	public static $tokenOffset		= 8;

	// defaults
	protected $attributes = [
		'instance_id' => 0,
		'public' => true
	];

	protected static function boot(){
		parent::boot();

		// handler after save so all info to store the file
		Easyfile::created(function($file){
			if( $file->hasTempFile()) {
				$location = self::buildLocation(storage_path(self::$storageLocation), $file->attributes);
				$location = str_replace('//', '/', $location);

				$location = $file->checkAndCreateDir($location);
				$file->tempFile->move($location, $file->filename);
			}
		});
	}



	public function setFile( \Symfony\Component\HttpFoundation\File\UploadedFile $file )
	{
		// temporarily save file for further handling
		$this->tempFile		= $file;

		$this->filename		= $file->getClientOriginalName();
		$this->size			= $file->getClientSize();
		$this->extension	= $file->getClientOriginalExtension();
		$this->mimetype		= $file->getMimeType();
	}


	public static function newWithFile( \Symfony\Component\HttpFoundation\File\UploadedFile $file )
	{
		$easyfile = new self();
		$easyfile->setFile($file);
		return $easyfile;
	}



	public static function arrayWithFiles( array $files )
	{
		$filesArray = [];
		foreach( $files as $file ){
			$newFile =  new self();
			$newFile->setFile($file);
			$filesArray[] = $newFile;
		}
		return $filesArray;
	}


	public function hasTempFile(){
		return ( $this->tempFile ? true : false );
	}


	private static function buildLocation( $location, $params )
	{
		foreach( $params as $name => $value ){
			$location = str_ireplace('{'.$name.'}', $value, $location);
		}
		return $location;
	}

	private function checkAndCreateDir( $location )
	{
		// first check if exists
		$writeDir = pathinfo($location)['dirname'];

		if (!File::exists($writeDir)) {
			// make dir
			File::makeDirectory($writeDir, 0777, true);
		}

		return $writeDir;
	}


	public function downloadUrl(){
		$params = $this->attributes;
		$params['token'] = self::generateToken($this->id, $this->filename, $this->created_at);

		$url = self::buildLocation(self::$downloadUrl, $params);

		return $url;
	}


	public static function respondWithDownload( $id, $token, $name=null, $header=[] ){
		$file = self::find($id);

		if( $token != self::generateToken( $file->id,$file->filename, $file->created_at) ){
			abort(404);
		}

		$pathToFile = self::buildLocation(storage_path(self::$storageLocation), $file->attributes);
		$pathToFile = str_replace('//', '/', $pathToFile);
		//dd($pathToFile);
		return response()->download($pathToFile);
	}

	private static function generateToken($id, $filename, $filesize){
		$fullToken = md5($filename.$filesize.$id.$_ENV['APP_KEY']);
		return substr($fullToken, self::$tokenOffset, self::$tokenLength);
	}



}
