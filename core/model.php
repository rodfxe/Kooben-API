<?php
	const MODEL_ITEM_INT = 0;
	const MODEL_ITEM_STR = 1;
	const MODEL_ITEM_ENUM = 2;
	const MODEL_ITEM_DATE = 3;
	const MODEL_SQL_PATH = 'queries/';

	const RETURN_JSON = true;
	const RETURN_DATA = false;

	/**
	 * Base class for Model
	 */
	class StdModel
	{
		# return self in json
		public function toJson()
		{
			return json_encode( $this );
		}
	}


	/**
	 * Class for server reponses
	 * @author Martin Samuel Esteban Diaz <edmsamuel@icloud.com>
	*/
	class KoobenResponse extends StdModel
	{
		public $status;
	}


	/**
	 * Status class for GET operations
	 */
	class GetModelStatus extends StdModel
	{
		public $found;
		public $valid;
		function __construct( $found = false, $valid = false )
		{
			$this->found = $found;
			$this->valid = $valid;
		}
	}


	/**
	 * Status class for POST operations
	 */
	class PostModelStatus extends StdModel
	{
		public $created;
		public $valid;

		function __construct( $created = false, $valid = false )
		{
			$this->created = $created;
			$this->valid = $valid;
		}
	}


	/**
	 * Status class for PUT operations
	 */
	class PutModelStatus extends StdModel
	{
		public $updated;
		public $found;

		function __construct( $found = false, $updated = false )
		{
			$this->updated = $updated;
			$this->found = $found;
		}
	}


	/**
	 * Status class for PUT operations
	 */
	class DeleteModelStatus extends StdModel
	{
		public $deleted;
		public $found;

		function __construct( $deleted = false )
		{
			$this->deleted = $deleted;
			$this->found = $this->deleted;
		}
	}



	/**
	 * Single item class
	 */
	class ModelItem extends StdModel
	{
		public $status;
		public $id;

		function __construct()
		{
			$this->id = -1;
		}
	}



	/**
	 * Class for array of items
	 */
	class ModelItems extends StdModel
	{

		public $status;
		public $items;

		function __construct()
		{
			$this->status = new GetModelStatus();
			unset( $this->status->valid );
		}

	}




	/**
	* Base class for models
	*/

	class Model extends StdModel
	{
		private $_properties;
		private $modelName;
		private $query;
		private $privateProperties;
		private $queryname;
		private $connection;


		public $id;
		private $active;

		function __construct( $name, &$mysqlConnection )
		{
			$this->id = -1;
			$this->modelName = $name;
			$this->connection = &$mysqlConnection;
			$this->query = new Query( $mysqlConnection );
			$this->queryname = empty_str;
			$this->privateProperties = array(
				'id',
				'createdAt',
				'updatedAt',
				'active',
				'__primary_key__',
				'__table_name__'
			);
		}




		/***********************************
		*          PUBLIC METHODS          *
		***********************************/




		# set properties to model
		public function setProperties( $properties )
		{
			$this->_properties = $properties;

			foreach( $properties as $property => $conf ){
				$internalProperties = array(
					'__primary_key__',
					'__table_name__'
				);

				$isInternalProperty = ( array_search( $property, $internalProperties ) !== false );
				if ( $isInternalProperty ){ continue; }

				switch( $conf->type ){
					case 'INT':
						$this->$property = -1;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_INT;
						break;

					case 'FLOAT':
						$this->$property = 0.0;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_INT;
						break;

					case 'DATETIME':
					case 'DATE':
						$this->$property = -1;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_STR;

					case 'STR':
						$this->$property = empty_str;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_STR;
						break;

					case 'BOOLEAN':
						$this->$property = $conf->default;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_STR;
						break;

					case 'ENUM':
						$this->$property = $conf->default;
						$this->_properties->$property->typeValue = QueryParamItem::TYPE_STR;
						break;
				}
			}
		}





		# set name of .sql query file
		public function useQuery( $queryname )
		{
			$this->queryname = $queryname;
		}





		# return self for another use.
		public function newItem()
		{
			$model = $this;
			return $model;
		}





		# find a record filtered by id
		public function &findById( $paramname, $id )
		{
			$found = false;
			$valid = is_numeric( $id );
			$isNumeric = $valid;
			
			$paramname = ( $paramname == '__default__' ? $this->_properties->__primary_key__ : $paramname );
			$queryname = ( (strlen( $this->queryname ) > 0) ? $this->queryname : 'get' );
			$path = MODEL_SQL_PATH.$this->modelName.'/'.$queryname.'.sql';
			$sql = file_get_contents( $path );

			$this->query->clear();
			$this->query->setSql( $sql );
			$this->query->setParam( $paramname, $id, ( $isNumeric ? QueryParamItem::TYPE_INT : QueryParamItem::TYPE_STR ) );
			$this->query->execQuery();

			if( $this->query->hasError ){ $this->error = $this->query->errorMessage; }
			$found = ( $this->query->recordCount > 0 );

			if( $found ){
				foreach( $this->query->rows[0] as $property => $value ){

					if( !isset( $this->_properties->$property ) ){
						$this->_properties->$property = new stdClass();
						$this->_properties->$property->type = ( !is_numeric( $value ) ? 'STR' : 'FLOAT' );
						$this->_properties->$property->typeValue = ( !is_numeric( $value ) ? '-1' : -1 );
					}

					switch( $this->_properties->$property->type ){
						case 'FLOAT':
							$value = floatval( $value );
							break;
						case 'BOOLEAN':
							$value = ( $value == 'true' );
							break;
					}

					$this->$property = $value;
				}
			}

			$this->id = $id;

			$this->status = new GetModelStatus( $found, $valid );
			return $this;
		}





		# find record with filters
		public function find( $paramsAdditionals = null )
		{
			$queryname = ( strlen( $this->queryname ) > 0 ? $this->queryname : 'get' );
			$path = ($this->getSQLPath() . $queryname. '.sql') ;
			$sql = file_get_contents( $path );
			$this->query->params->clear();
			$this->query->setSql( $sql );

			if( !is_null( $paramsAdditionals ) ){
				foreach( $paramsAdditionals->items as $paramname => $config ){
					$this->query->setParam( $paramname, $config->value, $config->type );
				}
			}

			$this->query->execQuery();
			$this->samuel = $this->getQuery();
			$this->setRealValuesToRecords();

			$items = new ModelItems();
			if( $this->query->hasError ){ $items->status->error = $this->query->errorMessage; }
			$items->status->found = $this->query->recordCount > 0;
			$items->status->count = $this->query->recordCount;
			$items->items = $this->query->rows;
			unset($this->query->rows);
			$this->query->rows = array();

			return $items;
		}






		public function findBy($Options)
		{
			$options = array(
				'params' => new QueryParams(),
				'queryName' => 'get',
				'devMode' => false
			);

			foreach ( $Options as $optionName => $optionValue ){
				$options[ $optionName ] = $optionValue;
			}

			$query = new Query( $this->connection );
			$query->setSql( $this->getContentFromSQLFile( $this->getSQLPath() . $options['queryName'] ) );
			foreach ( $options['params']->items as $paramName => $param ){
				$query->setParam( $paramName, $param->value, $param->type );
			}
			$query->execQuery();
			$query->checkTypesOfRows();

			$result = new StdModel();
			$result->status = new GetModelStatus( $query->recordCount > 0 );
			$result->status->count = $query->recordCount;
			$result->items = $query->rows;


			if ( $query->hasError ){ $result->status->error = $query->errorMessage; }
			if ( $query->hasWarnings() ){ $result->status->error = $query->getWarnings(); }

			if ( $options['devMode'] ){
				$result->dev = array(
					'sql' => $query->getQuery(false)
				);
			}

			return $result;
		}





		# get all records
		public function findAll()
		{
			$queryname = ( strlen( $this->queryname ) > 0 ? $this->queryname : 'get' );
			$path =  ($this->getSQLPath() . $queryname . '.sql');
			$sql = file_get_contents( $path );

			$this->query->params->clear();
			$this->query->setSql( $sql );
			$this->query->execQuery();
			$this->setRealValuesToRecords();

			$items = new ModelItems();
			$items->status->found = $this->query->recordCount > 0;
			$items->status->count = $this->query->recordCount;
			$items->items = $this->query->rows;

			if( $this->query->hasError ){ $items->status->error = $this->query->errorMessage; }

			return $items;
		}





		# create a new record un the database
		public function create()
		{
			global $kooben;

			$allOk = $this->validate();
			$model = $this->createModelItem();
			$model->status = new PostModelStatus();

			if( $allOk )
			{
				$queryname = ( strlen( $this->queryname ) > 0 ? $this->queryname : 'post' );
				$path = MODEL_SQL_PATH.$this->modelName.'/'.$queryname.'.sql';
				$sql = file_get_contents( $path );

				$this->query->type = Query::TYPE_INSERT;
				$this->query->params->clear();
				$this->query->setSql( $sql );
				$this->setQueryValues();
				$this->query->execQuery();
				if ( $kooben->config->mysql->profile != 'production' ) {
					$sql = $this->getQuery();
				}

				if( $this->query->hasError ){ $this->error = $this->query->errorMessage; }

				$id = $this->query->lastId();
				$this->id = $id;
				$model = $this->findById( $this->_properties->__primary_key__, $id );
				$model->status = new PostModelStatus();
				$model->status->valid = $allOk;
				$model->status->created = ( $id > 0 );
				if ( $kooben->config->mysql->profile != 'production' ) {
					$model->dev = $sql;
				}
			} else {
				$model->error = 'Valores incorrectos';
				$model->fieldError = $this->fieldError;
				$model->status = new PostModelStatus();
			}

			return $model;
		}




		# create multiples records in the database
		public function multiCreate( $options )
		{
			$Options = array(
				'items' => array(),
				'rules' => array(),
				'postQueryName' => 'post-multiple',
				'getQueryName' => 'get',
				'getQueryParams' => new QueryParams(),
				'devMode' => false
			);

			# override options.
			foreach ( $options as $optionName => $optionValue ){
				$Options[ $optionName ] = $optionValue;
			}

			if( count( $Options['items'] ) == 0 ){ return createEmptyModelWithStatus( 'Post' ); return; }

			$sqlItems = empty_str;
			foreach ( $Options['items'] as $row_idx => $row ){ # loop for rows.
				$sqlItems .= '('; # open a new item.
				
				foreach ( $row as $field_idx => $fieldValue ){ # loop for fields.
					if ( $Options['rules'][$field_idx] == QueryParamItem::TYPE_STR ){
						$sqlItems .= " '$fieldValue',";
					} else {
						$sqlItems .= ' ' . $fieldValue . ',';
					}
				}

				$sqlItems[ $this->lastStrIndex($sqlItems) ] = ' '; # replace the last character in string by an empty string.
				$sqlItems .= " ),\n"; # close the current item.
			}

			$sqlItems = trim($sqlItems);
			$sqlItems[ $this->lastStrIndex($sqlItems) ] = ' ';
			$sql = $this->getContentFromSQLFile( ($this->getSQLPath() . $Options['postQueryName']) );
			$this->replaceStrParamIn( $sql, 'items', $sqlItems );

			$result = new StdModel();
			$result->status = new PostModelStatus();

			$query = new Query( $this->connection, Query::TYPE_INSERT );
			$query->setSql( $sql );
			$query->execQuery();
			if ( $query->hasError ){ $result->error = $query->errorMessage; }

			if ( $query->affectedRows > 0 ){
				$result->status->created = true;

				$query = new Query( $this->connection );
				$query->setSql( $this->getContentFromSQLFile( $this->getSQLPath() . $Options['getQueryName'] ) );
				$query->params = $Options['getQueryParams'];
				$query->refreshParams();
				if ( $Options['devMode'] ){ $getQuery = $query->getQuery(false); }
				$query->execQuery();
				if ( $query->recordCount > 0 ){
					$result->items = $query->rows;
				}
			}

			if ( $Options['devMode'] ){
				$result->dev = array(
					'sql' => $sql,
					'get' => $getQuery
				);
			}

			return $result;
		}






		# save|update the model in the database
		public function save()
		{
			$queryname = ( strlen( $this->queryname ) > 0 ? $this->queryname : 'put' );
			$path = MODEL_SQL_PATH.$this->modelName.'/'.$queryname.'.sql';
			$sql = file_get_contents( $path );
			$fieldId = $this->_properties->__primary_key__;

			$this->query->type = Query::TYPE_UPDATE;
			$this->query->params->clear();
			$this->query->setSql( $sql );
			$this->setQueryValues();
			$this->query->setParam( $fieldId, $this->$fieldId, QueryParamItem::TYPE_INT );
			#$sql = $this->getQuery();
			$this->query->execQuery();

			$this->status = new PutModelStatus();
			$this->status->updated = ( $this->query->affectedRows > 0 );
			#$this->status->found = $this->recordExists( $this->$fieldId );
			$this->status->found = $this->recordExists( $this->$fieldId );
			#$this->status->sql = $sql;

			if( $this->query->hasError ){ $this->status->msg = $this->query->errorMessage; }
			if( $this->query->hasWarnings() ){ $this->status->messages = $this->query->getWarnings(); }

			return $this->status;
		}





		# logic delete for a record in the database
		public function delete( $id = -1 )
		{
			$this->id = ( $id == -1 ? $this->id : $id );

			$queryname = ( strlen( $this->queryname ) > 0 ? $this->queryname : 'delete' );
			$path = MODEL_SQL_PATH.$this->modelName.'/'.$queryname.'.sql';
			$sql = file_get_contents( $path );

			$this->query->type = Query::TYPE_DELETE;
			$this->query->setSql( $sql );
			$this->query->params->clear();
			$this->query->setParam( $this->_properties->__primary_key__, $this->id, QueryParamItem::TYPE_INT );
			$this->query->execQuery();
			$status = new DeleteModelStatus( $this->query->affectedRows > 0 );

			return $status;
		}








		 # logic delete for a record in the database
		public function customDelete( $Options )
		{
			# initialize default options.
			$options = array(
				'queryName' => 'delete-custom',
				'params' => new QueryParams(),
				'devMode' => false
			);

			# override options.
			foreach ( $Options as $optionName => $optionValue ){
				$options[ $optionName ] = $optionValue;
			}

			# create instance of Query class.
			$query = new Query( $this->connection, Query::TYPE_DELETE );
			$query->setSql( $this->getContentFromSQLFile( $this->getSQLPath() . $options['queryName'] ) );
			$query->params = $options['params'];
			$query->execQuery();

			# create instance of StdModel for the return.
			$result = new StdModel();
			$result->status = new DeleteModelStatus( $this->query->affectedRows > 0 );

			# check if occurred an error.
			if( $query->hasError ){ $result->status->error = $query->errorMessage; }

			if ( $options['devMode'] ){
				$result->dev = array(
					'sql' => $query->getQuery(false),
					'deleted' => $query->affectedRows
				);
			}

			return $result;
		}









		# check if exists the record on the table.
		public function recordExists( $id )
		{
			if ( $id <= -1 ){ return false; }
			$pkField = $this->_properties->__primary_key__;
			$query = new Query( $this->query->connection );
			$query->setSql( file_get_contents( ( MODEL_SQL_PATH.$this->modelName.'/get.sql' ) ) );
			$query->setParam( $pkField, $this->$pkField, QueryParamItem::TYPE_INT );
			$query->execQuery();
			return ( $query->recordCount > 0 );
		}










		# get the sql query.
		public function getQuery()
		{
			return $this->query->getQuery( false );
		}







		# set values from an array to properties in the model.
		public function &setValuesFromArray( $values )
		{
			foreach ( $values as $field => $value ){
				$this->$field = $value;
			}

			return $this;
		}






		public function tableName()
		{
			return $this->_properties->__table_name__;
		}









		public function &setCustomTypeForProperty( $propertyName, $newType )
		{
			$this->_properties->$propertyName->type = $newType;
			if ( $newType != 'INT' ){
				$this->_properties->$propertyName->typeValue = QueryParamItem::TYPE_STR;
			}
			return $this;
		}




		# start mysql transaction
		public function startTransaction()
		{
			$this->query->startTransaction();
		}


		public function confirmTransaction()
		{
			$this->query->confirmTransaction();
		}


		public function cancelTransaction()
		{
			$this->query->cancelTransaction();
		}





		/***********************************
		*         PRIVATE METHODS          *
		***********************************/








		# get las index of a string.
		private function lastStrIndex( &$str )
		{
			return ( strlen( $str ) - 1 );
		}





		# get las index of an array.
		private function lastArrayIndex( &$array )
		{
			return ( count( $array ) - 1 );
		}







		private function replaceStrParamIn( &$str, $paramname, $newval )
		{
			$str = str_replace( ":$paramname", $newval, $str );
		}







		# get path of model
		private function getSQLPath()
		{
			return ( MODEL_SQL_PATH . $this->modelName . '/' );
		}




		# get content from sql file - note: the path cannot contain the sql extension
		private function getContentFromSQLFile( $path )
		{
			return file_get_contents( $path . '.sql' );
		}




		# check if $propertyName is a private property
		private function isPrivateProperty( $propertyName )
		{
			return ( array_search( $propertyName, $this->privateProperties ) !== false );
		}





		# set properties to params in query
		private function setQueryValues()
		{
			$this->query->params->clear();
			foreach( $this->_properties as $property => $config ){
				if( $this->isPrivateProperty( $property ) ){ continue; }

				$value = $this->$property;
				switch ( $config->type ){
					case 'STR':
						if ( (trim($value) == empty_str) && isset($config->allow_null) && $config->allow_null ){
							$value = null;
						}
						break;
					case 'ENUM':
						if ( ( trim($value) == empty_str) && (array_search( $value, $config->values ) === false) ){
							$value = $config->default;
						}
						break;
				}

				$this->query->setParam( $property, $value, $config->typeValue );
			}
		}





		# create object with model properties
		private function createModelItem()
		{
			$model = new ModelItem();
			foreach( $this->_properties as $property => $config ){
				if(  array_search( $property, array( 'active', '__primary_key__', '__table_name__' ) ) !== false ){ continue; }

				$model->$property = $this->$property;
			}
			return $model;
		}





		# validate values before 'create'|'update'|'delete'
		private function validate()
		{
			$valid = true;
			foreach( $this->_properties as $property => $conf ){
				$invalid = ( $this->isPrivateProperty( $property ) || ( $property == $this->_properties->__primary_key__ ) || ( isset($conf->optional) && $conf->optional ) );
				if( $invalid ){ continue; }

				switch( $conf->type ){
					case 'INT':
						$valid = $this->$property > -1;
						break;

					case 'STR':
						$valid = strlen( $this->$property ) > 0;
						break;

					case 'ENUM':
						$valid = array_search( $this->$property, $conf->values ) !== false;
						break;

					case 'FLOAT':
						$valid = true;
						break;
				}

				if( !$valid ){
					$this->fieldError = $property;
					break;
				}
			}
			return $valid;
		}





		# check types
		private function setRealValuesToRecords()
		{
			foreach ( $this->query->rows as $row => $values ){

				foreach( $values as $property => $value ){

					if( !isset( $this->_properties->$property ) ){
						$this->_properties->$property = new stdClass();
						$type = ( !is_numeric( $value ) ? 'STR' : 'INT' );
						$this->_properties->$property->type = $type;
						if( $type != 'STR' ){ $this->_properties->$property->type = ( (strpos( $value, '.') !== FALSE) ? 'FLOAT' : 'INT' ); }
					}

					switch( $this->_properties->$property->type ){
						case 'FLOAT':
							$this->query->rows[$row][$property] = number_format( floatval( $value ), 2 );
							break;

						case 'INT':
							$this->query->rows[$row][$property] = intval( $value );
							break;

						case 'BOOLEAN':
							$this->query->rows[$row][$property] = ( $value == 'true' );
							break;
					}
				}
			}
		}
	}

?>
