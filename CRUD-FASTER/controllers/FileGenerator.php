<?php 

    class FileGenerator {

        protected $table_obj;

        private $table_columns_array;

        private $str_columnas_nulas;

        private $str_columnas_nulas_array;

        private $argumentosDeFuncion;

        private $argsDeFuncion_Array;

        private $argumentosDeMarcadores;

        private $argsDeMarcadores_Array;

        private $complementoUpdate;

        private $argumentosInsertController;

        private $argsFunctionInsertController;

        private $argumentosUpdateController;

        private $argsFunctionUpdateController;
        
        function __construct ($arg_tableObj) {

            $this->table_obj = $arg_tableObj;

            $this->table_columns_array = $this->table_obj->getTableColumns();

            $this->str_columnas_nulas = $this->evaluaNulo($this->table_columns_array);

            $this->str_columnas_nulas_array = explode(",", $this->str_columnas_nulas);

            $this->argumentosDeFuncion = $this->devuelveArgumentosConCaracter($this->str_columnas_nulas, "\$arg_");

            $this->argsDeFuncion_Array = explode(",", $this->argumentosDeFuncion);

            $this->argumentosDeMarcadores = $this->devuelveArgumentosConCaracter($this->str_columnas_nulas, ":arg_");

            $this->argsDeMarcadores_Array = explode(",", $this->argumentosDeMarcadores);

            $this->complementoUpdate = $this->completaUpdate($this->str_columnas_nulas, $this->argsDeMarcadores_Array);

            $this->argumentosInsertController = $this->getArgsInsertController($this->str_columnas_nulas_array);

            $this->argsFunctionInsertController = $this->devuelveArgumentosConCaracter($this->str_columnas_nulas, "\$arg_");

            $this->argumentosUpdateController = $this->getArgsUpdateController($this->table_columns_array);

            $this->argsFunctionUpdateController = $this->getArgsFunctionUpdateController($this->table_columns_array);

            $nombre_directorio = "../proyect_generated";
            
            if(!file_exists($nombre_directorio)) {

                mkdir($nombre_directorio, 0700);

            }
        }
        
        public function generate_Constants_File () {

            if (!copy('../models/Constants.php', '../proyect_generated/Constants.php')) {

                throw new Exception('No fué posible crear el fichero de Constants.');
                    
            } else if (!copy('../public/resources/libs/fontawesome/fontawesome-all.min.js', '../proyect_generated/fontawesome-all.min.js')) {

                throw new Exception('No fué posible crear el fichero de fontawesome.');
                    
            } else if (!copy('../public/resources/libs/jquery/jquery.min.js', '../proyect_generated/jquery.min.js')) {

                throw new Exception('No fué posible crear el fichero de Constants.');
                    
            } else {

                return true;
                
            }
            
        }

        public function generate_Connection_File () {

            if (!copy('../models/Conexion.php', '../proyect_generated/Conexion.php')) {

                throw new Exception('No fué posible crear el fichero de Conexion.');
                    
            } else {

                return true;
                
            }
            
        }

        public function generate_TableModel_File () {

            $nombre_tabla = ucwords($this->table_obj->getTableName(), "_");

            $tableModel_file_txt = "<?php \n\n"
                . "\trequire_once 'Conexion.php'; \n\n"
                . "\tclass ". $nombre_tabla ." { \n\n"
                . "\t\tfunction __construct() {} \n\n"
                . "\t\t/**\n"
                . "\t\t * Función para obtener todos \n"
                . "\t\t * los registros de la tabla \n"
                . "\t\t */ \n"
                . "\t\tpublic static function getAll () { \n\n"
                . "\t\t\t\$sql_sentence = \"SELECT * FROM ". $nombre_tabla ."\"; \n\n"
                . "\t\t\ttry { \n"
                . "\t\t\t\t// Preparar Sentencia \n"
                . "\t\t\t\t\$comand = Conexion::getInstance()->getDb()->prepare(\$sql_sentence); \n\n"
                . "\t\t\t\t// Ejecutar Sentencia Preparada \n"
                . "\t\t\t\t\$comand->execute(); \n\n"
                . "\t\t\t\t\$retorno = \$comand->fetchAll(PDO::FETCH_ASSOC); \n\n"
                . "\t\t\t\treturn \$retorno; \n\n"
                . "\t\t\t} catch (PDOException \$e) { \n\n"
                . "\t\t\t\treturn false; \n\n"
                . "\t\t\t} \n"
                . "\t\t} \n\n"
                . "\t\t/**\n"
                . "\t\t * Función para obtener todos los registros \n"
                . "\t\t * por la columna ". $this->table_columns_array[0]["column_name"] ." \n"
                . "\t\t */ \n"
                . "\t\tpublic static function getAll_". $this->table_columns_array[0]["column_name"] ." () { \n\n"
                . "\t\t\t\$sql_sentence = \"SELECT ". $this->table_columns_array[0]["column_name"] ." FROM ". $nombre_tabla ."\"; \n\n"
                . "\t\t\ttry { \n"
                . "\t\t\t\t// Preparar Sentencia \n"
                . "\t\t\t\t\$comand = Conexion::getInstance()->getDb()->prepare(\$sql_sentence); \n\n"
                . "\t\t\t\t// Ejecutar Sentencia Preparada \n"
                . "\t\t\t\t\$comand->execute(); \n\n"
                . "\t\t\t\t\$retorno = \$comand->fetchAll(PDO::FETCH_ASSOC); \n\n"
                . "\t\t\t\treturn \$retorno; \n\n"
                . "\t\t\t} catch (PDOException \$e) { \n\n"
                . "\t\t\t\treturn false; \n\n"
                . "\t\t\t} \n"
                . "\t\t} \n\n"
                . "\t\t/**\n"
                . "\t\t * Función para insertar un nuevo registro \n"
                . "\t\t */ \n"
                . "\t\tpublic static function insert (". $this->argumentosDeFuncion .") { \n\n"
                . "\t\t\t\$sql_sentence = \"INSERT INTO ". $this->table_obj->getTableName() ." (". $this->str_columnas_nulas .") VALUES (". $this->argumentosDeMarcadores .")\"; \n\n"
                . "\t\t\ttry { \n"
                . "\t\t\t\t// Preparar Sentencia \n"
                . "\t\t\t\t\$comand = Conexion::getInstance()->getDb()->prepare(\$sql_sentence); \n\n";
            
            for ($i=0; $i < count($this->argsDeMarcadores_Array); $i++) { 

                $str_bindParam = "\t\t\t\t\$comand->bindParam(\"". $this->argsDeMarcadores_Array[$i] ."\", ". $this->argsDeFuncion_Array[$i] ."); \n";

                $tableModel_file_txt = $tableModel_file_txt . $str_bindParam;
                
            }

            $tableModel_file_txt = $tableModel_file_txt . "\n\t\t\t\t// Ejecutar Sentencia Preparada \n"
                . "\t\t\t\t\$comand->execute(); \n\n"
                . "\t\t\t\t\$retorno = \$comand->fetchAll(PDO::FETCH_ASSOC); \n\n"
                . "\t\t\t\treturn \$retorno; \n\n"
                . "\t\t\t} catch (PDOException \$e) { \n\n"
                . "\t\t\t\treturn false; \n\n"
                . "\t\t\t} \n"
                . "\t\t} \n\n"
                . "\t\t/**\n"
                . "\t\t * Función para actualizar un registro \n"
                . "\t\t * de la base de datos \n"
                . "\t\t */ \n"
                . "\t\tpublic static function update (". $this->argumentosDeFuncion .") { \n\n"
                . "\t\t\t\$sql_sentence = \"UPDATE ". $this->table_obj->getTableName() ." SET ". $this->complementoUpdate ." WHERE ". $this->table_columns_array[0]["column_name"] ." = :arg_". $this->table_columns_array[0]["column_name"] ."\"; \n\n"
                . "\t\t\ttry { \n"
                . "\t\t\t\t// Preparar Sentencia \n"
                . "\t\t\t\t\$comand = Conexion::getInstance()->getDb()->prepare(\$sql_sentence); \n\n";

            for ($i=0; $i < count($this->argsDeMarcadores_Array); $i++) { 

                $str_bindParam = "\t\t\t\t\$comand->bindParam(\"". $this->argsDeMarcadores_Array[$i] ."\", ". $this->argsDeFuncion_Array[$i] ."); \n";

                $tableModel_file_txt = $tableModel_file_txt . $str_bindParam;
                
            }
            
            $tableModel_file_txt = $tableModel_file_txt . "\n\t\t\t\t// Ejecutar Sentencia Preparada \n"
                . "\t\t\t\t\$comand->execute(); \n\n"
                . "\t\t\t\t\$retorno = \$comand->fetchAll(PDO::FETCH_ASSOC); \n\n"
                . "\t\t\t\treturn \$retorno; \n\n"
                . "\t\t\t} catch (PDOException \$e) { \n\n"
                . "\t\t\t\treturn false; \n\n"
                . "\t\t\t} \n"
                . "\t\t} \n\n"
                . "\t\t/**\n"
                . "\t\t * Función para eliminar un registro \n"
                . "\t\t * por la columna ". $this->table_columns_array[0]["column_name"] ." \n"
                . "\t\t */ \n"
                . "\t\tpublic static function delete (\$arg_". $this->table_columns_array[0]["column_name"] .") { \n\n"
                . "\t\t\t\$sql_sentence = \"DELETE FROM ". $this->table_obj->getTableName() ." WHERE ". $this->table_columns_array[0]["column_name"] ." = :arg_". $this->table_columns_array[0]["column_name"] ."\"; \n\n"
                . "\t\t\ttry { \n"
                . "\t\t\t\t// Preparar Sentencia \n"
                . "\t\t\t\t\$comand = Conexion::getInstance()->getDb()->prepare(\$sql_sentence); \n\n"
                . "\t\t\t\t\$comand->bindParam(\":arg_". $this->table_columns_array[0]["column_name"] ."\", \$arg_". $this->table_columns_array[0]["column_name"] ."); \n\n"
                . "\t\t\t\t// Ejecutar Sentencia Preparada \n"
                . "\t\t\t\t\$comand->execute(); \n\n"
                . "\t\t\t\t\$retorno = \$comand->fetchAll(PDO::FETCH_ASSOC); \n\n"
                . "\t\t\t\treturn \$retorno; \n\n"
                . "\t\t\t} catch (PDOException \$e) { \n\n"
                . "\t\t\t\treturn false; \n\n"
                . "\t\t\t} \n"
                . "\t\t} \n\n"
                . "\t} \n"
                . "?>";

            if (!file_exists("../proyect_generated/". $nombre_tabla ."_Model.php")) {
                
                $table_model_file = fopen("../proyect_generated/". $nombre_tabla ."_Model.php", "a");

                fwrite($table_model_file, $tableModel_file_txt);
                fclose($table_model_file);

            } else {

                $table_model_file = fopen("../proyect_generated/". $nombre_tabla ."_Model.php", "w;");

                fwrite($table_model_file, $tableModel_file_txt);
                fclose($table_model_file);
                
            }

            return true;
            
        }

        public function generate_Controller_File () {

            $nombre_clase_tabla = ucwords($this->table_obj->getTableName(), "_");

            $controller_file_txt = "<?php\n\n"
                . "\t\$operation = \$_POST['operation'];\n\n"
                . "\tswitch (\$operation) {\n"
                . "\t\tcase '1':\n"
                . "\t\t\tgetAll();\n"
                . "\t\t\tbreak;\n\n"
                . "\t\tcase '2':\n"
                . "\t\t\tinsertNew(". $this->argumentosInsertController .");\n"
                . "\t\t\tbreak;\n\n"
                . "\t\tcase '3':\n"
                . "\t\t\tupdateData(". $this->argumentosUpdateController .");\n"
                . "\t\t\tbreak;\n\n"
                . "\t\tcase '4':\n"
                . "\t\t\tdelete(\$_POST['". $this->table_columns_array[0]['column_name'] ."']);\n"
                . "\t\t\tbreak;\n\n"
                . "\t\tdefault:\n"
                . "\t\t\tbreak;\n"
                . "\t}\n\n"
                . "\tfunction getAll() {\n\n"
                . "\t\tinclude_once '". $nombre_clase_tabla ."_Model.php';\n\n"
                . "\t\t\$model_response = ". $nombre_clase_tabla ."::getAll();\n\n"
                . "\t\tif (\$model_response['response'] === \"ok\") {\n\n"
                . "\t\t\t\$html_array = array();\n\n"
                . "\t\t\t\$html_code = \"<tr>\"\n";

            for ($i=0; $i < count($this->table_columns_array); $i++) { 

                $controller_file_txt = $controller_file_txt . "\t\t\t\t\t\t. \"<th>". $this->table_columns_array[$i]['column_name'] ."</th>\"\n";
                
            }
                
            $controller_file_txt = $controller_file_txt . "\t\t\t\t\t\t. \"<th>Options</th>\"\n"
                . "\t\t\t\t\t. \"</tr>\";\n\n"
                . "\t\t\tarray_push(\$html_array, \$html_code);\n\n"
                . "\t\t\tforeach (\$model_response['content'] as \$bd_row) {\n\n"
                . "\t\t\t\t\$html_code = \"<tr id=\\\"\". \$bd_row['". $this->table_columns_array[0]['column_name'] ."'] .\"\\\">\"\n";
            
            for ($i=0; $i < count($this->table_columns_array); $i++) { 

                $controller_file_txt = $controller_file_txt . "\t\t\t\t\t\t. \"<td>\". \$bd_row['". $this->table_columns_array[$i]['column_name'] . "'] .\"</td>\"\n";
                
            }
            
            $controller_file_txt = $controller_file_txt . "\t\t\t\t\t\t. \"<td>\"\n"
                . "\t\t\t\t\t\t\t. \"<div class=\\\"options-container\\\">\"\n"
                . "\t\t\t\t\t\t\t\t. \"<div class=\\\"btn-put-to-update\\\" onclick=\\\"setToUpdate(\". \$bd_row['". $this->table_columns_array[0]['column_name'] ."'] .\")\\\">\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"<span>\"\n"
                . "\t\t\t\t\t\t\t\t\t\t. \"<i class=\\\"fas fa-edit\\\"></i>\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"</span>\"\n"
                . "\t\t\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t\t\t\t. \"<div class=\\\"btn-delete\\\" onclick=\\\"deleteData(\". \$bd_row['". $this->table_columns_array[0]['column_name'] ."'] .\")\\\">\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"<span>\"\n"
                . "\t\t\t\t\t\t\t\t\t\t. \"<i class=\\\"far fa-trash-alt\\\"></i>\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"</span>\"\n"
                . "\t\t\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t\t. \"</td>\"\n"
                . "\t\t\t\t\t. \"</tr>\";\n\n"
                . "\t\t\t\tarray_push(\$html_array, \$html_code);\n"
                . "\t\t\t}\n\n"
                . "\t\t\t\$server_response = array('response' => \"ok\", 'content' => \$html_array);\n\n"
                . "\t\t\tprint_r(json_encode(\$server_response));\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\tprint_r(json_encode(array('response' => \"error\", 'content' => \"Se ha generado un error al realizar la consulta.\")));\n\n"
                . "\t\t}\n"
                . "\t}\n\n"
                . "\tfunction insertNew (". $this->argsFunctionInsertController .") {\n\n"
                . "\t\tinclude_once '". $nombre_clase_tabla ."_Model.php';\n\n"
                . "\t\t\$model_response = ". $nombre_clase_tabla ."::insert(". $this->argsFunctionInsertController .");\n\n"
                . "\t\tif (\$model_response['response'] === \"ok\") {\n\n"
                . "\t\t\t\$second_model_response = ". $nombre_clase_tabla ."::getAll();\n\n"
                . "\t\t\tif (\$second_model_response['response'] === \"ok\") {\n\n"
                . "\t\t\t\t\$row = count(\$second_model_response['content']) - 1;\n\n"
                . "\t\t\t\t\$html_code = \"<tr id=\\\"\". \$second_model_response['content'][\$row]['". $this->table_columns_array[0]['column_name'] ."'] .\"\\\">\"\n";

            for ($i=0; $i < count($this->table_columns_array); $i++) { 

                $controller_file_txt = $controller_file_txt . "\t\t\t\t\t. \"<td>\". \$second_model_response['content'][\$row]['". $this->table_columns_array[$i]['column_name'] ."'] .\"</td>\"\n";
                
            }
            
            $controller_file_txt = $controller_file_txt . "\t\t\t\t\t. \"<td>\"\n"
                . "\t\t\t\t\t\t. \"<div class=\\\"options-container\\\">\"\n"
                . "\t\t\t\t\t\t\t. \"<div class=\\\"btn-put-to-update\\\" onclick=\\\"setToUpdate(\". \$second_model_response['content'][\$row]['". $this->table_columns_array[0]['column_name'] ."'] .\")\\\">\"\n"
                . "\t\t\t\t\t\t\t\t. \"<span>\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"<i class=\\\"fas fa-edit\\\"></i>\"\n"
                . "\t\t\t\t\t\t\t\t. \"</span>\"\n"
                . "\t\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t\t\t. \"<div class=\\\"btn-delete\\\" onclick=\\\"deleteData(\". \$second_model_response['content'][\$row]['". $this->table_columns_array[0]['column_name'] ."'] .\")\\\">\"\n"
                . "\t\t\t\t\t\t\t\t. \"<span>\"\n"
                . "\t\t\t\t\t\t\t\t\t. \"<i class=\\\"far fa-trash-alt\\\"></i>\"\n"
                . "\t\t\t\t\t\t\t\t. \"</span>\"\n"
                . "\t\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t\t. \"</div>\"\n"
                . "\t\t\t\t\t. \"</td>\"\n"
                . "\t\t\t\t. \"</tr>\";\n\n"
                . "\t\t\t\tprint_r(json_encode(array('response' => \"ok\", 'content' => \$html_code)));\n\n"
                . "\t\t\t} else {\n\n"
                . "\t\t\t\tprint_r(json_encode(array('response' => \"error\", 'content' => \"No se pudo obtener el campo\")));\n\n"
                . "\t\t\t}\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\tprint_r(json_encode(array('response' => \"error\", 'content' => \$model_response['content'])));\n\n"
                . "\t\t}\n\n"
                . "\t}\n\n"
                . "\tfunction updateData(". $this->argsFunctionUpdateController .") {\n\n"
                . "\t\tinclude_once '". $nombre_clase_tabla ."_Model.php';\n\n"
                . "\t\t\$model_response = ". $nombre_clase_tabla ."::update(". $this->argsFunctionUpdateController .");\n\n"
                . "\t\tif (\$model_response['response'] === \"ok\") {\n\n"
                . "\t\t\tgetAll();\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\tprint_r(json_encode(array('response' => \"error\", 'content' => \$model_response['content'])));\n\n"
                . "\t\t}\n\n"
                . "\t}\n\n"
                . "\tfunction delete(\$arg_". $this->table_columns_array[0]['column_name'] .") {\n\n"
                . "\t\tinclude_once '". $nombre_clase_tabla ."_Model.php';\n\n"
                . "\t\t\$model_response = ". $nombre_clase_tabla ."::delete(\$arg_". $this->table_columns_array[0]['column_name'] .");\n\n"
                . "\t\tif (\$model_response['response'] === \"ok\") {\n\n"
                . "\t\t\tgetAll();\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\tprint_r(json_encode(array('response' => \"error\", 'content' => \$model_response['content'])));\n\n"
                . "\t\t}\n\n"
                . "\t}\n\n"
                . "?>";

            if (!file_exists("../proyect_generated/app-controller.php")) {
                
                $app_controller_file = fopen("../proyect_generated/app-controller.php", "a");

                fwrite($app_controller_file, $controller_file_txt);
                fclose($app_controller_file);

            } else {

                $app_controller_file = fopen("../proyect_generated/app-controller.php", "w;");

                fwrite($app_controller_file, $controller_file_txt);
                fclose($app_controller_file);
                
            }

            return true;
            
        }

        public function generate_JavaScript_File () {

            $js_file_txt = "var column_id_to_update;\n\n"
                . "$(document).ready(function () {\n\n"
                . "\tgetAll();\n\n"
                . "\t$(\".btn-cancel-update\").click(function (event) {\n"
                . "\t\tevent.preventDefault();\n\n"
                . "\t\tcancelUpdate();\n\n"
                . "\t});\n\n"
                . "\t$(\"#btn-insert\").click(function (event) {\n\n";

            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 

                $js_file_txt = $js_file_txt . "\t\tvar ". $this->str_columnas_nulas_array[$i] ." = $(\"#input-". $this->str_columnas_nulas_array[$i] ."\").val();\n";
                
            }

            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) {

                if ($i == 0) {

                    $js_file_txt = $js_file_txt . "\n\t\tif (". $this->str_columnas_nulas_array[$i] ." == \"\") {\n\n"
                        . "\t\t\talert(\"Debe completar el campo.\");\n\n"
                        . "\t\t}";
                    
                } else if ($i >= 1) {

                    $js_file_txt = $js_file_txt . " else if (". $this->str_columnas_nulas_array[$i] ." == \"\") {\n\n"
                        . "\t\t\talert(\"Debe completar el campo.\");\n\n"
                        . "\t\t}";
                    
                }

                // else {

                //     $js_file_txt = $js_file_txt . " if (". $this->str_columnas_nulas_array[$i] ." == \"\") {\n\n"
                    
                // }
                
            }
            
            $js_file_txt = $js_file_txt. " else {\n\n"
                . "\t\t\tvar operation = 2;\n\n"
                . "\t\t\tvar data_send = {\n";

            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 

                $js_file_txt = $js_file_txt . "\t\t\t\t". $this->str_columnas_nulas_array[$i] .",\n";
                
            }
            
            $js_file_txt = $js_file_txt. "\t\t\t\toperation\n"
                . "\t\t\t}\n\n"
                . "\t\t\t$.ajax({\n"
                . "\t\t\t\turl: \"app-controller.php\",\n"
                . "\t\t\t\tdata: data_send,\n"
                . "\t\t\t\tmethod: 'post',\n"
                . "\t\t\t\tdataType: 'json',\n"
                . "\t\t\t\tcache: false\n"
                . "\t\t\t}).done(function (arg_serverResponse) {\n\n"
                . "\t\t\t\tif (arg_serverResponse['response'] == \"ok\") {\n\n"
                . "\t\t\t\t\t$(\"table\").append(arg_serverResponse['content']);\n\n"
                . "\t\t\t\t} else {\n\n"
                . "\t\t\t\t\talert(arg_serverResponse['content']);\n\n"
                . "\t\t\t\t}\n\n"
                . "\t\t\t}).always(function () {\n\n";
            
            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 

                $js_file_txt = $js_file_txt . "\t\t\t\t$(\"#input-". $this->str_columnas_nulas_array[$i] ."\").val(\"\");\n";
                
            }
                
            $js_file_txt = $js_file_txt . "\n"
                . "\t\t\t});\n\n"
                . "\t\t}\n\n"
                . "\t});\n\n"
                . "\t$(\"#btn-send-update\").click(function (event) {\n"
                . "\t\tevent.preventDefault();\n\n"
                . "\t\tupdate(column_id_to_update);\n"
                . "\t});\n\n"
                . "});\n\n"
                . "function getAll() {\n"
                . "\t$.ajax({\n"
                . "\t\turl: \"app-controller.php\",\n"
                . "\t\tdata: {\n"
                . "\t\t\toperation: 1\n"
                . "\t\t},\n"
                . "\t\tmethod: 'post',\n"
                . "\t\tdataType: 'json',\n"
                . "\t\tcache: false\n"
                . "\t}).done(function (arg_serverResponse) {\n\n"
                . "\t\tvar str_html = \"\";\n\n"
                . "\tif (arg_serverResponse['response'] == \"ok\") {\n\n"
                . "\t\t\targ_serverResponse['content'].forEach(function (table_row) {\n\n"
                . "\t\t\t\t$(\"table\").append(table_row);\n\n"
                . "\t\t\t});\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\talert(arg_serverResponse['content']);\n\n"
                . "\t\t}\n\n"
                . "\t});\n"
                . "}\n\n"
                . "function setToUpdate(arg_column_id) {\n\n"
                . "\tcolumn_id_to_update = arg_column_id;\n\n";
                
            for ($i=0; $i < count($this->table_columns_array); $i++) {

                $indice = $i + 1;
            
                $js_file_txt = $js_file_txt . "\t$(\"#input-". $this->table_columns_array[$i]['column_name'] ."\").val($(\"tr#\" + arg_column_id + \">td:nth-child(". $indice .")\").html());\n";
            }
                
            $js_file_txt = $js_file_txt . "\n"
                . "\t$(\"#btn-insert\").css({\n"
                . "\t\t\"display\": \"none\"\n"
                . "\t});\n\n"
                . "\t$(\"#btn-send-update\").css({\n"
                . "\t\t\"display\": \"block\"\n"
                . "\t});\n\n"
                . "\t$(\".btn-cancel-update\").css({\n"
                . "\t\t\"display\": \"flex\"\n"
                . "\t});\n\n"
                . "}\n\n"
                . "function update(arg_". $this->table_columns_array[0]['column_name'] .") {\n\n"
                . "\tvar ". $this->table_columns_array[0]['column_name'] ." = arg_". $this->table_columns_array[0]['column_name'] .";\n";
            
            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) {
            
                $js_file_txt = $js_file_txt . "\tvar ". $this->str_columnas_nulas_array[$i] ." = $(\"#input-". $this->str_columnas_nulas_array[$i] ."\").val();\n";

            }
            
            $js_file_txt = $js_file_txt . "\n"
                . "\tvar operation = 3;\n\n"
                . "\tvar data_send = {\n";
            
            for ($i=0; $i < count($this->table_columns_array); $i++) { 

                $js_file_txt = $js_file_txt ."\t\t". $this->table_columns_array[$i]['column_name'] .",\n";
                
            }
                    
            $js_file_txt = $js_file_txt . "\t\toperation\n"
                . "\t};\n\n"
                . "\t$.ajax({\n"
                . "\t\turl: \"app-controller.php\",\n"
                . "\t\tdata: data_send,\n"
                . "\t\tmethod: 'post',\n"
                . "\t\tdataType: 'json',\n"
                . "\t\tcache: false\n"
                . "\t}).done(function (arg_serverResponse) {\n\n"
                . "\t\tif (arg_serverResponse['response'] === \"ok\") {\n\n"
                . "\t\t\t$(\"table\").empty();\n\n"
                . "\t\t\tgetAll();\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\talert(arg_serverResponse['content']);\n\n"
                . "\t\t}\n\n"
                . "\t}).always(function () {\n\n";
                
            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 

                $js_file_txt = $js_file_txt ."\t\t$(\"#input-". $this->str_columnas_nulas_array[$i] ."\").val(\"\");\n";
                
            }
                
            $js_file_txt = $js_file_txt . "\n"
                . "\t\tcancelUpdate();\n\n"
                . "\t});\n\n"
                . "}\n\n"
                . "function cancelUpdate() {\n\n";
                
            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 

                $js_file_txt = $js_file_txt . "\t$(\"#input-". $this->str_columnas_nulas_array[$i] ."\").val(\"\");\n";
                
            }

            $js_file_txt = $js_file_txt . "\n"
                . "\t$(\"#btn-insert\").css({\n"
                . "\t\t\"display\": \"block\"\n"
                . "\t});\n\n"
                . "\t$(\"#btn-send-update\").css({\n"
                . "\t\t\"display\": \"none\"\n"
                . "\t});\n\n"
                . "\t$(\".btn-cancel-update\").css({\n"
                . "\t\t\"display\": \"none\"\n"
                . "\t});\n\n"
                . "}\n\n"
                . "function deleteData(arg_". $this->table_columns_array[0]['column_name'] .") {\n\n"
                . "\tvar operation = 4;\n"
                . "\tvar ". $this->table_columns_array[0]['column_name'] ." = arg_". $this->table_columns_array[0]['column_name'] .";\n\n"
                . "\tvar data_send = {\n"
                . "\t\t". $this->table_columns_array[0]['column_name'] .",\n"
                . "\t\toperation\n"
                . "\t};\n\n"
                . "\t$.ajax({\n"
                . "\t\turl: \"app-controller.php\",\n"
                . "\t\tdata: data_send,\n"
                . "\t\tmethod: 'post',\n"
                . "\t\tdataType: 'json',\n"
                . "\t\tcache: false\n"
                . "\t}).done(function (arg_serverResponse) {\n\n"
                . "\t\tif (arg_serverResponse['response'] === \"ok\") {\n\n"
                . "\t\t\t$(\"table\").empty();\n\n"
                . "\t\t\tgetAll();\n\n"
                . "\t\t} else {\n\n"
                . "\t\t\talert(arg_serverResponse['content']);\n\n"
                . "\t\t}\n\n"
                . "\t});\n\n"
                . "}";

                

            if (!file_exists("../proyect_generated/app-script.js")) {
                
                $app_js_file = fopen("../proyect_generated/app-script.js", "a");

                fwrite($app_js_file, $js_file_txt);
                fclose($app_js_file);

            } else {

                $app_js_file = fopen("../proyect_generated/app-script.js", "w;");

                fwrite($app_js_file, $js_file_txt);
                fclose($app_js_file);
                
            }

            return true;

        }

        public function generate_Html_File () {

            $html_file_txt = "<!DOCTYPE html>\n"
                . "<html lang=\"es\">\n\n"
                . "\t<head>\n"
                . "\t\t<meta charset=\"UTF-8\">\n"
                . "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n"
                . "\t\t<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n\n"
                . "\t\t<title>PROYECTO GENERADO</title>\n\n"
                . "\t\t<link rel=\"stylesheet\" href=\"app-style.css\">\n\n"
                . "\t\t<!-- Font Awesome -->\n"
                . "\t\t<script src=\"fontawesome-all.min.js\" async></script>\n\n"
                . "\t</head>\n\n"
                . "\t<body>\n\n"
                . "\t\t<main>\n"
                . "\t\t\t<div class=\"table-container\">\n"
                . "\t\t\t\t<table>\n"
                . "\t\t\t\t\t<!-- Se generan dinámicamente -->\n"
                . "\t\t\t\t</table>\n"
                . "\t\t\t</div>\n\n"
                . "\t\t\t<div class=\"insert-box-container\">\n"
                . "\t\t\t\t<div class=\"btn-cancel-update\">\n"
                . "\t\t\t\t\t<span>\n"
                . "\t\t\t\t\t\t<i class=\"fas fa-times\"></i>\n"
                . "\t\t\t\t\t</span>\n"
                . "\t\t\t\t</div>\n\n";

            for ($i=0; $i < count($this->str_columnas_nulas_array); $i++) { 
                
                $html_file_txt = $html_file_txt . "\t\t\t\t<div class=\"input-container\">\n"
                . "\t\t\t\t\t<label>". $this->str_columnas_nulas_array[$i] .":</label>\n\n"
                . "\t\t\t\t\t<input type=\"text\" placeholder=\"Ingresa dato\" id=\"input-". $this->str_columnas_nulas_array[$i] ."\">\n"
                . "\t\t\t\t</div>\n\n";
            }

            $html_file_txt = $html_file_txt . "\t\t\t\t\t<div class=\"input-container\">\n"
                . "\t\t\t\t\t<button id=\"btn-insert\">Insertar</button>\n\n"
                . "\t\t\t\t\t<button id=\"btn-send-update\">Actualizar</button>\n"
                . "\t\t\t\t</div>\n"
                . "\t\t\t</div>\n"
                . "\t\t</main>\n\n\n"
                . "\t\t<script src=\"jquery.min.js\"></script>\n"
                . "\t\t<script src=\"app-script.js\"></script>\n"
                . "\t</body>\n\n"
                . "</html>";

            if (!file_exists("../proyect_generated/index.html")) {
                
                $app_html_file = fopen("../proyect_generated/index.html", "a");

                fwrite($app_html_file, $html_file_txt);
                fclose($app_html_file);

            } else {

                $app_html_file = fopen("../proyect_generated/index.html", "w;");

                fwrite($app_html_file, $html_file_txt);
                fclose($app_html_file);
                
            }

            return true;
        }

        public function generate_CSS_File () {

            if (!copy('../public/resources/css/app-generated-style.css', '../proyect_generated/app-style.css')) {

                throw new Exception('No fué posible crear el fichero de Constants.');
                    
            } else {

                return true;
                
            }
            
        }

        /**
         * FUNCIONES QUE AYUDAN A CONSTRUIR LAS CADENA
         */

        private function evaluaNulo ($arg_tableColumns) {

            $str_needed_columns = "";

            foreach ($arg_tableColumns as $column) {

                if (($column['nulo'] == "NO")) {

                    if ($column['extra'] != "auto_increment") {

                        $str_needed_columns = "". $str_needed_columns ."". $column['column_name'] . ",";
                    }
                }
                
            }

            $str_needed_columns = substr($str_needed_columns, 0, -1);

            return $str_needed_columns;
            
        }

        private function devuelveArgumentosConCaracter ($arg_strColumnasNulas, $arg_caracter) {

            $argsStringArray = explode(",", $arg_strColumnasNulas);

            for ($i=0; $i <= count($argsStringArray)-1; $i++) { 

                $argsStringArray[$i] = $arg_caracter . $argsStringArray[$i];
                
            }

            $argsString = implode(",", $argsStringArray);
            
            return $argsString;
            
        }

        private function completaUpdate ($arg_stringColumnasNulas, $arg_argumentosDeMarcadoresArray) {

            $argsStringColumnasArray = explode(",", $arg_stringColumnasNulas);
            
            $strCompletaUpdate = "";

            for ($i=0; $i < count($argsStringColumnasArray); $i++) { 

                $strCompletaUpdate = "". $strCompletaUpdate . $argsStringColumnasArray[$i] ."=". $arg_argumentosDeMarcadoresArray[$i] .", ";
                
            }

            $strCompletaUpdate = substr($strCompletaUpdate, 0, -1);

            return $strCompletaUpdate;
            
        }

        private function getArgsInsertController($arg_strColumnasNulas_array) {

            $str_args_insertController = "";

            foreach ($arg_strColumnasNulas_array as $column_null) {

                $str_args_insertController = $str_args_insertController . "\$_POST['". $column_null . "'], ";
                
            }

            $str_args_insertController = substr($str_args_insertController, 0, -2);

            return $str_args_insertController;

        }

        private function getArgsUpdateController($arg_tableColumns_array) {

            $str_args_updateController = "";

            foreach ($arg_tableColumns_array as $tColumn) {

                $str_args_updateController = $str_args_updateController . "\$_POST['". $tColumn['column_name'] . "'], ";
                
            }

            $str_args_updateController = substr($str_args_updateController, 0, -2);

            return $str_args_updateController;
            
        }

        private function getArgsFunctionUpdateController($arg_tableColumns_array) {

            $str_args_funcUpdateController = "";

            foreach ($arg_tableColumns_array as $tColumn) {

                $str_args_funcUpdateController = $str_args_funcUpdateController . "\$arg_" . $tColumn['column_name'] . ", ";
                
            }

            $str_args_funcUpdateController = substr($str_args_funcUpdateController, 0, -2);

            return $str_args_funcUpdateController;
            
        }
        
    }
    

?>