<?php
    class Language{
        private $conn;
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getLanguages(){

            $sqlQuery = "SELECT * FROM languages;";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getSingleLanguage($id){
            $sqlQuery = "SELECT * FROM languages WHERE lang_id = ?;";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        


    }
?>

