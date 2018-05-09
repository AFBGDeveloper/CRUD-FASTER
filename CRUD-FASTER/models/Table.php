<?php 

    class Table {

        private $tableName;
        private $tableColumns;

        function Table ($arg_tableName) {

            $this->tableName = $arg_tableName;
            
        }

        /**
         * ==============================
         *  GETTERS
         * ==============================
         */
        public function getTableName () {

            return $this->tableName;
            
        }

        public function getTableColumn ($arg_columnIndex) {

            return $this->tableColumns[$arg_columnIndex]["column_name"];
            
        }

        public function getTableColumns () {

            return $this->tableColumns;
            
        }

        /**
         * ==============================
         *  SETTERS
         * ==============================
         */
        
        public function setTableColumns ($arg_tableColumns) {

            $this->tableColumns = $arg_tableColumns;
            
        }
           
    }    

?>