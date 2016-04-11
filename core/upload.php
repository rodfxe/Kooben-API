<?php

/**
* Upload file class
* @author edmsamuel<edmsamuel@icloud.com>
*/
class Upload
{
	public $name;
	public $size;
	public $type;
	public $tempName;
	public $extension;
	public $saved;

	private $maxSize;
	private $rules;

	function __construct( $file, $options )
	{
		$this->name = $file[ 'name' ];
		$this->size = $file[ 'size' ];
		$this->type = $file[ 'type' ];
		$this->tempName = $file[ 'tmp_name' ];
		$this->extension = pathinfo( $this->name )[ 'extension' ];
		$this->rules = $options[ 'rules' ];
		$this->maxSize = $options[ 'maxSize' ];
	}

	public function sizeValid() {
		return ( $this->size <= $this->maxSize );
	}

	public function typeValid() {
		return ( array_search( $this->type, $this->rules ) > -1 );
	}

	public function isValid() {
		return ( $this->typeValid() && $this->sizeValid() );
	}

	public function getResume() {
		return [
			'isValid' => $this->isValid(),
			'typeValid' => $this->typeValid(),
			'sizeValid' => $this->sizeValid()
		];
	}

	public function save( $name )
	{
		global $kooben;

		$name = ( "$name.$this->extension" );
		$newPath = ( $kooben->uploadFolder . $name );
		$this->saved = ( move_uploaded_file( $this->tempName, $newPath ) === TRUE );
		if ( $this->saved ) { $this->name = $name; }
		return $this->saved;
	}

	public function saveAs( $name ) {
		return $this->save( $name );
	}
}

?>
