<?php
    namespace application\models;
    use PDO;
    //$pdo -> lastInsertId();

    class UserModel extends Model {
        public function insUser(&$param) {
            $sql = "INSERT INTO t_user
                    ( email, pw, nm ) 
                    VALUES 
                    ( :email, :pw, :nm )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array($param["email"], $param["pw"], $param["nm"]));

            return $stmt->rowCount();
            
        }
        public function selUser(&$param) {
            $sql = "SELECT * FROM t_user
                    WHERE email = :email";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array($param["email"]));
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        }

        public function selUserProfile(&$param) {
            $feediuser = $param["feediuser"];
            $loginiuser = $param["loginiuser"];

            $sql = "SELECT iuser, email, nm, cmt, mainimg
                        , (SELECT COUNT(ifeed) FROM t_feed WHERE iuser = {$feediuser}) AS feedCnt
                        , (SELECT COUNT(fromiuser) FROM t_user_follow WHERE fromiuser = {$feediuser} AND toiuser = {$loginiuser} ) AS youme
                        , (SELECT COUNT(fromiuser) FROM t_user_follow WHERE fromiuser = {$loginiuser} AND toiuser = {$feediuser} ) AS meyou
                        , (SELECT COUNT(fromiuser) FROM t_user_follow WHERE fromiuser = {$feediuser} ) AS following
                        , (SELECT COUNT(toiuser) FROM t_user_follow WHERE toiuser = {$feediuser} ) AS follower
                    FROM t_user
                    WHERE iuser = {$feediuser};";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ);
        }
    }