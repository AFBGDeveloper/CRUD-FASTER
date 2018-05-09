<?php 

    /**
     * operation = 1 => encontrar la tabla en la base de datos
     * operation = 2 => ejecutar sentencias SQL elegidas por el usuario
     * operation = 3 => generar codigo
     */

    // $operation = $_POST['operation'];
    $operation = 2;

    switch ($operation) {
        case '1':
            step_1($_POST['db_name'], $_POST['db_user'], $_POST['db_pass']);
            step_1("framework_test_uno", "root", "");
            break;

        case '2':
            // step_2($_POST['table_name']);
            step_2('tbl_estudiantes');
            break;
        
        default:
            break;
    }


    function step_1 ($arg_dbName, $arg_dbUser, $arg_dbPass) {

        /**
         * ===================================
         *  Se crea el fichero Constants.php
         * ===================================
         */
        // $constants_txt = "<?php \n"
        //     . "\tif ( (!defined('HOSTNAME')) && (!defined('DATABASE')) && (!defined('USERNAME')) && (!defined('PASSWORD')) ) { \n"
        //     . "\t\t define('HOSTNAME', 'localhost'); \n"
        //     . "\t\t define('DATABASE', 'framework_test_uno'); \n"
        //     . "\t\t define('USERNAME', 'root'); \n"
        //     . "\t\t define('PASSWORD', ''); \n"
        //     . "\t} \n"
        //     . "";

        $constants_txt = "<?php \n"
            . "\tif ( (!defined('HOSTNAME')) && (!defined('DATABASE')) && (!defined('USERNAME')) && (!defined('PASSWORD')) ) { \n"
            . "\t\t define('HOSTNAME', 'localhost'); \n"
            . "\t\t define('DATABASE', '". $arg_dbName ."'); \n"
            . "\t\t define('USERNAME', '". $arg_dbUser ."'); \n"
            . "\t\t define('PASSWORD', '". $arg_dbPass ."'); \n"
            . "\t} \n"
            . "?>";
         
        if (!file_exists("../models/Constants.php")) {
            
            $constants_file = fopen("../models/Constants.php", "a");

            fwrite($constants_file, $constants_txt);
            fclose($constants_file);

        } else {

            $constants_file = fopen("../models/Constants.php", "w;");

            fwrite($constants_file, $constants_txt);
            fclose($constants_file);
            
        }

        require_once '../models/Mapper.php';

        $respuesta = Mapper::findTables($arg_dbName);

        if ($respuesta['response'] == 'ok') {

            // Se valida que no existan más de 2 tablas

            $tablas_en_bd = count($respuesta['content']);

            if ($tablas_en_bd < 2) {

                print_r(json_encode(array('rta' => 'ok', 'msg' => "" . $respuesta['content'][0]['Tables_in_'. $arg_dbName])));
                
            } else {

                print_r(json_encode(array('rta' => 'error', 'msg' => "Lo sentimos, este framework solo funciona para bases de datos que contienen solo una tabla.")));

            }

            
        } else {

            print_r(json_encode(array('rta' => 'error', 'msg' => $respuesta['content'])));

        }
        
    }
    
    function step_2 ($arg_tableName) {

        require_once '../models/Table.php';

        $objTable = new Table($arg_tableName);

        require_once '../models/Mapper.php';

        $respuesta = Mapper::findColumns($arg_tableName);

        // print_r(json_encode($respuesta['content']));

        if ($respuesta['response'] == 'ok') {
            
            $tableColumns_array = array();
            
            foreach ($respuesta['content'] as $columna) {
                
                $column_element = array(
                    'column_name' => $columna['Field'],
                    'column_data_type' => $columna['Type'],
                    'column_key' => $columna['Key'],
                    'nulo' => $columna['Null'],
                    'extra' => $columna['Extra']
                );
                
                array_push($tableColumns_array, $column_element);
                
            }
            
            $objTable->setTableColumns($tableColumns_array);

            // Se crean los ficheros
            require_once '../controllers/FileGenerator.php';

            $objFileGenerator = new FileGenerator($objTable);
            
            try {
                
                $objFileGenerator->generate_Constants_File();

                $objFileGenerator->generate_Connection_File();

                $objFileGenerator->generate_TableModel_File();

                $objFileGenerator->generate_Controller_File();
                
                $objFileGenerator->generate_JavaScript_File();

                $objFileGenerator->generate_Html_File();

                $objFileGenerator->generate_CSS_File();

                print_r(json_encode(array('rta' => 'ok', 'msg' => "Se generaron los ficheros")));

            } catch (Exception $e) {

                print_r(json_encode(array('rta' => 'error', 'msg' => $e->getMessage() ."\n")));
                
            }

            
        } else {

            print_r(json_encode(array('rta' => 'error', 'msg' => $respuesta['content'] ."\n")));

        }
        
    }

?>