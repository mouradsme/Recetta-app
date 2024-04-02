<?php
    class Actions {
        protected $actions = [];
        protected $Actions = [];
        public function __construct() {
            return $this;
        }
        public function add($a, $function = null, $vals = []) {
            $this->actions[$a] = [$function, $vals];
            $this->Actions[] = $a;
            return $this;
        }
        public function create($v) {
            $Result = ['error', 'Unknown!'];
            if (in_array($v, $this->Actions)) {
                $action         = $this->actions[$v];
                $Result = $action[0](...$action[1]);
                if (!isset($Result[1]))
                    $Result[1] = "";
            }
            echo json_encode(array("status" => $Result[0], "message" => $Result[1]), JSON_INVALID_UTF8_IGNORE );
        }
    }
    class Routes {
        protected $routes = [];
        protected $Routes = [];
        protected $Main;
        protected $Content;
        protected $Passed;
        protected $Default = 'home';
        protected $red;
        public function getDefault() {
            return $this->Default;
        }
        public function __construct($red, $default = 'home') {
            $this->red = $red;
            $this->Default = $default;
            return $this;
        }
        public function add($p, $Main, $content, $function = null, $vals = []) {
            $this->routes[$p] = [$Main, $content, $function, $vals];
            $this->Routes[] = $p;
            return $this;
        }
        public function create($v) {
            $red = $this->red;

            if ($red[0] && $v !== $red[1]) {
                $this->create($red[1]);
                return $this;
            }
            if (!in_array($v, $this->Routes)) $v = $this->Default;   
            $route          = $this->routes[$v];
            $this->Main     = $route[0];
            $this->Content  = $route[1]; 
            $route[2](...$route[3]);
            return;
            
        }
        public function Pass($key, $value) {
            $this->Passed[$key] = $value;
            return $this;
        }
        public function getMain() {
            return $this->Main;
        }
        public function getContent() {
            return $this->Content;
        }
        public function getPassed() {
            return $this->Passed;
        }
    }
    class Links {
        protected $links = [];
        protected $id;
        public function __construct() { 
            global $links;
            $this->links = $links;
        }
        public function select($id) {
            $this->id = $id;
            return $this;
        }
        public function create( $link ) {
            $Link = [
                "id"    => $link[0],
                "title" => $link[1],
                "icon"  => $link[2],
                "perm"  => $link[3]
            ];
            if ($this->id != null)
                $this->links[$this->id][] = $Link;
            else $this->links[] = $Link;
            return $this;
        }

        public function getLinks() { 
            return $this->links;
        }
    }
    class Generate {
        protected $prefix = '';
        protected $characters = '';
        public function __construct() {
            $this->characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            return $this;
        }
        public function prefix($prefix) {
            $this->prefix = $prefix;
            return $this;
        }
        public function characters($characters) {
            $this->characters = $characters;
        }
        public function generate($length) {
            $chars = $this->characters; 
            $str = '';
            for ($i=0; $i<$length; $i++)
                $str .= $chars[rand(0, strlen($chars)-1)];
            return $this->prefix . $str;
        }

    }
    class Models {
        protected $models;
        public function add($table, $columns, $prefix = '') {
            $this->models[$table] = [$columns, $prefix];
            return $this;
        }
        public function getModels() {
            return $this->models;
        }
    }
    class Database {
        protected $database;
        protected $models;
        protected $extra;
        protected $data = [];
        protected $RequestReady = false;
        protected $RequestQuery = "";
        public function __construct(PDO $database, Models $models) {
            $this->database = $database;
            $this->models = $models;
        }
        protected $table;
        public function select($table) {
            $this->table = $table;
            $this->RequestReady = false;
            return $this;
        }
        public function lastInsertId() {
            $db = $this->database;
            return $db->lastInsertId();

        }
        public function insert($data) {
            $db = $this->database;
            $table = $this->table;
            $model = $this->models->getModels()[$table]; 
            $prefix = $model[1];
            $model = $model[0];
        
            $keys = [];
            $values = [];      
            foreach ($model as $c) {
                $keys[] =  $prefix."_".$c;
                $values[] = "?";
            }
            $Keys = join(",", $keys);
            $Values = join(",", $values);
            $query = "INSERT INTO $table ($Keys) VALUES ($Values);";
            $request = $db->prepare($query);
            if ($request->execute($data))
                return true;
            return false;
        }
        public function deleteMultipleKeys($cols, $vals) {
            $db = $this->database;
            $table = $this->table;
            $model = $this->models->getModels()[$table]; 
            $prefix = $model[1];
            $model = $model[0];
            foreach ($cols as $c) 
                $values[] = $prefix."_".$c. " = ?";
            $Values = join(" AND ", $values);
            $query = "DELETE FROM $table WHERE $Values;";
            $request = $db->prepare($query);
            if ($request->execute($vals))
                return $this;
            return null;

        }
        public function delete($val, $col) {
            $db = $this->database;
            $table = $this->table;
            $query = "DELETE FROM $table WHERE $col = ?;";
            $request = $db->prepare($query);
            if ($request->execute([$val]))
                return true;
            return false;

        }
        public function e() {
            $db = $this->database;
            return $db->prepare($this->RequestQuery)->execute($this->data);
        }
        public function extra($extra) {
            $this->extra = $extra;
            return $this;
        }
        public function bindData($data) {
            $this->data = $data;
            return $this;
        }
        public function join($with, $col1 = '', $col2 = '') {
            $db = $this->database;
            $table = $this->table;
            $pref1 = $this->models->getModels()[$table][1];
            $pref2 = $this->models->getModels()[$with][1];
            if ($col1 == '' || $col2 == '') {
                $col1 = 'id';
                $col2 = $pref1."_id";
            }
            $col1 = $pref1."_".$col1;
            $col2 = $pref2."_".$col2;
            $extra = $this->extra;
            if ($extra) {
                $extra = " AND $extra";
            }
            $query = "SELECT T1.*, T2.* 
            FROM $table T1, $with T2 
            WHERE T1.$col1 = T2.$col2 $extra;";
            $this->RequestReady = true;
            $this->RequestQuery = $query;
            return $this;
        }
        public function query($query) {
            $this->RequestReady = true;
            $this->RequestQuery = $query;
            return $this;
        }

        public function fetchAll() {
            $db = $this->database;
            if (!$this->RequestReady) {
                $table = $this->table;
                $extra = $this->extra;
                if ($table != null) {
                    $query = "SELECT * FROM $table $extra;";
                }
            } else {
                $query = $this->RequestQuery;
            }
            $request = $db->prepare($query);
            $request->execute($this->data);
            $result = $request->fetchAll(PDO::FETCH_ASSOC);
            return $result;
            
        }
        public function userExists($username) {
            $db = $this->database;
            $username = trim(strtolower($username));
            $query = "SELECT count(*) as c FROM users WHERE user_username = ?;";
            $request = $db->prepare($query);
            $request->execute([$username]);
            $count = $request->fetch(PDO::FETCH_ASSOC)['c'];
            return ($count > 0) ;
        }
        public function allowLogin($username, $password) {
            $db = $this->database;
            $username = trim(strtolower($username));
            $query = "SELECT count(*) as c FROM users WHERE user_username = ? AND user_password = ?;";
            $request = $db->prepare($query);
            $request->execute([$username, md5($password)]);
            $count = $request->fetch(PDO::FETCH_ASSOC)['c'];
            return ($count > 0) ;

        }
    }
    class Settings {
        protected $settings;
        public function __construct() {
                $this->settings = array(
                    "links" => array(
                        "icons" => false,
                        "text" => true
                    )
                );
            return $this;
        }
        public function select($option) {
            $this->option = $option;
            return $this;
        }
        public function set($parameter, $value) {
            $option = $this->option;
            $this->settings[$option][$parameter] = $value;
            return $this;
        }
        public function getSettings() {
            return $this->settings;
        }
    }
    class Utility { 
        function cmp($a, $b)
        {
            return strcmp(sizeof($a), sizeof($b));
        }
        public function comb($array) {
            // initialize by adding the empty set
            $results = array(array( ));
        
            foreach ($array as $element)
                foreach ($results as $combination)
                    array_push($results, array_merge(array($element), $combination));
        
            return $results;
        }
        public function sort_by_count($arr) {
            usort($arr, "Utility::cmp");
            return array_reverse($arr);
        }
        public function reformatDate($date, $del = '/' ) {
            $date = preg_split("#\s#", $date);
            $o = @$date[1]?@" ".$date[1]:"";
            $date = preg_split("#\-#", $date[0]);
            return join($del, [$date[2], $date[1], $date[0]]). $o; 
        }
        public function getLang($str) {
            global $Lang; 
            $str = preg_split("#\.#", $str);
            $expression = "";
            foreach ($str as $k => $v) {
                $expression .= "['$v']";
            }
            $expression = '$Lang' . $expression . ";";
            eval('$result = '.$expression);
            return $result;
        }
        public function uploadFile($filename, $valid_extensions = [], $prefix = '', $location = "uploads/") {
            $location = ( $location ) ?  $location : "uploads/";
            $Location = $location;
            $location = $location . $filename;
            $fileType = pathinfo($location,PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);
            $response = null;
            $Generate = new Generate();
            $imageId = $Generate->prefix($prefix)->generate(20);
            if(in_array(strtolower($fileType), $valid_extensions)) {
                $newLocation = $Location .  $imageId . "." . $fileType;
               if(move_uploaded_file($_FILES['file']['tmp_name'], $newLocation)){
                  $response = $newLocation;
               }
            }
            return $response;
        } 
        public function deleteFile($file) {
            unset($file);
            return $this;
        }
    }
?>