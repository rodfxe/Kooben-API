<?php
    const PARAM_INT = 0;
    const PARAM_STRING = 1;
    const empty_str = '';
    const PARAM_BEGIN = ':';
    const SINGLE_STR_DELIMITER = '\'';
    const DOUBLE_STR_DELIMITER = '\"';

    const QUERY_SELECT = 0;
    const QUERY_INSERT = 1;
    const QUERY_UPDATE = 2;
    const QUERY_DELETE = 3;

    /**
     * Param item class
     */
    class QueryParamItem
    {
        public $value; # represent the value of the param in the sql.
        public $type;  # it represents the value in sql if string will be 'value' if it is not value.

        function __construct( $value = -1, $type = PARAM_INT )
        {
            $this->value = $value;
            $this->type = $type;
        }
    }




    /**
     * Parameters class
     */
    class QueryParams
    {
        public $items; # it represents an array of QueryParamItem class, save here all parameters.
        public $count; # it represent the count of all parameters.

        function __construct( $params = array() )
        {
            $this->items = array();
            $this->count = count( $params );

            foreach ( $params as $paramName => $paramValues ){
                $this->items[$paramName] = $paramValues;
            }
        }

        public function setParam( $name, $value = -1, $type = PARAM_INT )
        {
            # if the parameter already exists create a new parameter and increment count
            # else only set the values.
            $this->count += ( !isset( $this->items[ $name ] ) ? 1 : 0 );
            $this->items[ $name ] = new QueryParamItem( $value, $type );
        }

        public function clear()
        {
            $this->items = array();
        }


        public function getFromArray($items)
        {
            $end = count( $items );
            $param = 0;

            if( ( $end % 2 ) == 0 )
            {
                while( $param < $end )
                {
                    $name = $items[$param];
                    $value = $items[$param + 1];
                    $type = ( is_numeric($value) ? PARAM_INT : PARAM_STRING );

                    $this->setParam( $name, $value, $type );
                    $param += 2;
                }
            }
        }
    }



    /**
     * Query class for mysql statements
     */
    class Query
    {
        public $params;         # it represents the QueryParams class.
        public $connection;     # it will be the mysql connection.
        public $sql;            # save here the sql statement
        public $result;         # it will be the mysqli result after execute mysql.query().
        public $rows;           # save here the rows of result.
        public $affectedRows;   # number of rows affected by the sql.
        public $type;           # type of query.
        public $recordCount;    # number of rows.
        public $hasError;       # query executed with errors.
        public $errorMessage;   # error message.

        function __construct( &$mysql_connection, $type = QUERY_SELECT )
        {
            $this->connection =& $mysql_connection;
            $this->params = new QueryParams();
            $this->sql = empty_str;
            $this->rows = array();
            $this->type = $type;
        }

        function __destruct()
        {
            if( isset( $this->result ) ){ unset( $this->result ); }

            unset( $this->params );
            unset( $this->sql );
            unset( $this->rows );
        }




        /***********************************
        *          PUBLIC METHODS          *
        ***********************************/

        public function setSql( $sql )
        {
            $this->sql = $sql;
            $this->setParams();
        }








        public function execQuery()
        {
            $sql = $this->parseSql();
            $this->reset();

            if( $this->type == QUERY_SELECT ){
                if( $this->result = $this->connection->query( $sql ) ){
                    $this->recordCount = $this->result->num_rows;
                    $this->rows = ( $this->recordCount > 0 ? $this->fetchAll() : array() );
                    $this->result->close();
                    $this->hasError = false;
                    $this->errorMessage = empty_str;
                } else {
                    $this->hasError = true;
                    $this->errorMessage = $this->connection->error.'\n'.$sql;
                }
            } else {
                if( $this->connection->query( $sql ) === true ){
                    $this->affectedRows = $this->connection->affected_rows;
                } else {
                    $this->errorMessage = $this->connection->error;
                }
            }
        }





        public function lastId()
        {
            return $this->connection->insert_id;
        }


        public function getJson()
        {
            return json_encode( $this->rows );
        }


        public function setParam( $name, $value, $type )
        {
            $this->params->setParam( $name, $value, $type );
        }



        public function getQuery( $original = true )
        {
            return ( $original ? $this->sql : $this->parseSql() );
        }



        public function clear()
        {
            $this->params->clear();
            $this->sql = empty_str;
            $this->reset();
        }



        public function hasWarnings()
        {
            return ( $this->connection->warning_count > 0 );
        }



        public function getWarnings()
        {
            return $this->connection->get_warnings();
        }



        public function toJson()
        {
            return json_encode( $this );
        }


        public function startTransaction()
        {
            $this->connection->begin_transaction();
        }


        public function confirmTransaction()
        {
            $this->connection->commit();
        }


        public function cancelTransaction()
        {
            $this->connection->rollback();
        }




        public function checkTypesOfRows()
        {
            foreach ( $this->rows as $row_idx => $fields ){

                foreach ( $fields as $field_idx => $fieldValue ){

                    $type = ( !is_numeric( $fieldValue ) ? 'STR' : 'INT' );
                    if( $type != 'STR' ){ $type = ( (strpos( $fieldValue, '.') !== FALSE) ? 'FLOAT' : 'INT' ); }
                    if( $type == 'STR' && array_search( $fieldValue, array( 'true', 'false' ) ) != FALSE ){ $type = 'BOOLEAN'; }
                    
                    switch ( $type ) {
                        case 'FLOAT':
                            $this->rows[$row_idx][$field_idx] = number_format( floatval( $fieldValue ), 2 );
                            break;

                        case 'INT':
                            $this->rows[$row_idx][$field_idx] = intval( $fieldValue );
                            break;

                        case 'BOOLEAN':
                            $this->rows[$row_idx][$field_idx] = ( $fieldValue == 'true' );
                            break;
                    }

                }

            }

        }        




        /***********************************
        *         PRIVATE METHODS          *
        ***********************************/

        private function concat( $items )
        {
            $str = empty_str;
            foreach( $items as $index => $value ){
                $str .= $value;
            }
            return $str;
        }





        private function parseSql()
        {
            $sql = $this->sql;
            foreach( $this->params->items as $paramname => $conf ){
                $name = PARAM_BEGIN . $paramname;
                $value = ( $conf->type == PARAM_STRING ? $this->concat(array(SINGLE_STR_DELIMITER, $conf->value, SINGLE_STR_DELIMITER)) : $conf->value );

                $sql = str_replace( $name, $value, $sql );
            }
            return $sql;
        }






        private function setParams()
        {
            # /(?:^|[ ]):([a-zA-Z0-9]+)/gm
            # /:([a-zA-Z0-9]+)/gm
            $exp = '/(?:^|[ ]):([a-zA-Z0-9]+)/m';
            preg_match_all( $exp, $this->sql, $matches );
            foreach( $matches[1] as $index => $paramName ){
                $this->params->setParam( $paramName );
            }
        }





        private function reset()
        {
            $this->recordCount = 0;
            $this->rows = array();
            $this->affectedRows = 0;
            $this->hasError = false;
            $this->errorMessage = empty_str;
        }





        private function fetchAll()
        {
            if( !method_exists( $this->result, 'fetch_all' ) ){
                # get list of fields
                /*$fields = $this->result->fetch_fields();

                foreach( $fields as $field ){

                }
                */

                $rows = array();
                $index = 0;
                while( $row = $this->result->fetch_assoc( ) )
                {
                    $rows[$index++] = $row;
                }

                return $rows;
            } else {
                return $this->result->fetch_all( MYSQLI_ASSOC );
            }
        }

    }


?>
