<?php 

    require_once 'Conexion.php';
    
    class Mapper {
        
        function __construct() {}

        /**
         * funcion para encontrar las tablas
         */
        public static function findTables ($arg_dbName) {

            $sql = "SHOW TABLES FROM ". $arg_dbName;

            try {
                // Preparar sentencia
                $comand = Conexion::getInstance()->getDb()->prepare($sql);
                // Ejecutar sentencia preparada
                $comand->execute();

                $retorno = $comand->fetchAll(PDO::FETCH_ASSOC);

                $rta_return = array('response' => 'ok', 'content' => $retorno);

                return $rta_return;

            } catch (PDOException $e) {

                $rta_return = array('response' => 'error', 'content' => $e->getMessage());

                return $rta_return;

            }
            
        }

        /**
         * funcion para encontrar las columnas de la tabla
         */
        public static function findColumns ($arg_tableName) {

            $sql = "SHOW COLUMNS FROM ". $arg_tableName;

            try{

                // Preparar sentencia
                $comand = Conexion::getInstance()->getDb()->prepare($sql);
                // Ejecutar sentencia preparada
                $comand->execute();

                $retorno = $comand->fetchAll(PDO::FETCH_ASSOC);

                $rta_return = array('response' => 'ok', 'content' => $retorno);

                return $rta_return;

            } catch (PDOException $e) {

                $rta_return = array('response' => 'error', 'content' => $e->getMessage());

                return $rta_return;
                
            }

        }
        
    }

?>