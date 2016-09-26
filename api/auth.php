<?php
require_once('db.php');
require_once('utils.php');
use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;

class UnauthorizedException extends \Exception implements UnauthorizedExceptionInterface{}
class InvaildUsernamePasswordException extends \Exception {}
class TokenAuth{
    private function getUserFromToken($token){
        $db = getDB();
        $sth = $db->prepare("SELECT * FROM `so_users` WHERE `token`=:token");
        $sth->bindParam(':token',$token, PDO::PARAM_STR);
        $ret = $sth->execute();
        $users = $sth->fetchAll(PDO::FETCH_ASSOC);
        //print_r($users);
        //print_r(count($users));
        if(count($users)==1){
            //print_r(count($users));
            return $users[0];
        }else{
            throw new UnauthorizedException('Unauthorized Access, Invalid token');
        }
    }
    public function verifyToken($req,$token){
        if(!$token){
            throw new UnauthorizedException('Unauthorized Access');
        }
        $user = $this->getUserFromToken($token);
        if(MD5($user['username'])==substr($token,0,32)&&strtotime($user['token_expire'])>strtotime(time())){
            if(strpos("/api/admin",$req->getUri()->getPath())!==FALSE&&intval($user['role'])!==3){
                throw new UnauthorizedException('Unauthorized Access');
            }
            if(intval($user['role'])==1){
                //if user is a student, check if he have rights to access resources
                if(!$req->isGet()&&!$req->isDelete()){
                    $data = json_decode($req->getbody(),true);
                    if(is_assoc($data)){
                        $this->verifyStudentNumber($data['student_number'],$token,$user);
                    }else{
                        foreach($data as $item){
                            $this->verifyStudentNumber($item['student_number'],$token,$user);
                        }
                    }
                }else{
                    $studentNumber =  (int)$req->getAttribute('studentNumber');
                    if($studentNumber){
                        $this->verifyStudentNumber($studentNumber,$token,$user);
                    }
                }
            }
            $db = getDB();
            $update_sth = $db->prepare(
                "UPDATE `so_users`
                    SET
                        `token_expire` = :tokenExpire
                    WHERE `so_users`.`id` = :userId;"
            );
            //Get more time for this token
            $update_sth->bindParam('tokenExpire',date('Y-m-d H:i:s', strtotime('+1 hour')),PDO::PARAM_STR);
            $update_sth->bindParam('userId',intval($user['id']),PDO::PARAM_INT);
            $update_sth->execute();
        }else{
            throw new UnauthorizedException('Unauthorized Access, Invalid token (time expired, please login agian)');
        }
    }

    public function verifyStudentNumber($studentNumber,$token,$user){
        if(!$token){
            throw new UnauthorizedException('Cannot find token');
        }
        if(!$user){
            $user = $this->getUserFromToken($token);
        }
        if(intval($user['role'])==1){
            $db = getDB();
            $sth = $db->prepare("SELECT * FROM `so_students` WHERE `so_students`.`so_users_id`=:userId");
            $sth->bindParam(':userId',intval($user['id']), PDO::PARAM_INT);
            $ret = $sth->execute();
            $student = $sth->fetch(PDO::FETCH_ASSOC);
            if(intval($studentNumber)!=intval($student['student_number'])){
                throw new UnauthorizedException('Unauthorized Access other student information:'.$studentNumber);
            }
            return true;
        }else if(intval($user['role'])==2){
            //todo teacher's auth
            throw new UnauthorizedException('Do not support teacher features for now, Your role:'.$user['role']);
        }else if(intval($user['role'])==3){
            //admin can do anything about a student
            return true;
        }else{
            throw new UnauthorizedException('Unauthorized Access, Your role:'.$user['role']);
        }
    }

    public function verifyUserId($userId,$token){
        $user = $this->getUserFromToken($token);
        if(intval($user['role'])==3){
            return true;
        }
        return $userId == $user['id'];
    }

    public function getToken($username,$password){
        if(!$username||!$password){
            return null;
        }
        $token_data = [];
        $db = getDB();
        $sth = $db->prepare("SELECT * FROM `so_users` WHERE `username`=:username AND `password`=:password");
        $sth->bindParam(':username', $username, PDO::PARAM_STR);
        $sth->bindParam(':password', $password, PDO::PARAM_STR);
        $ret = $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user){
            $bytes = openssl_random_pseudo_bytes(32);
            $token = MD5($username).MD5(bin2hex($bytes)).MD5(time());
            $token_data['username'] = $username;
            $token_data['role'] = $user['role'];
            $token_data['expire'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $token_data['token'] = $token;
            $update_sth = $db->prepare(
                "UPDATE `so_users`
                    SET
                        `token` = :token,
                        `token_expire` = :token_expire
                    WHERE `so_users`.`id` = :user_id;"
            );
            $update_sth->bindParam('token',$token,PDO::PARAM_STR);
            $update_sth->bindParam('token_expire',$token_data['expire'],PDO::PARAM_STR);
            $update_sth->bindParam('user_id',$user['id'],PDO::PARAM_INT);
            $update_sth->execute();
            return $token_data;
        }else{
            throw new InvaildUsernamePasswordException('Invalid username or password');
        }
        return null;
    }
}
?>
