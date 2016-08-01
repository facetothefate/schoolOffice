<?php
require_once('db.php');
use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;

class UnauthorizedException extends \Exception implements UnauthorizedExceptionInterface{}
class InvaildUsernamePasswordException extends \Exception {}
class TokenAuth{

    public function verifyToken($token){
        if(!$token){
            throw new UnauthorizedException('Unauthorized Access');
        }
        $db = getDB();
        $sth = $db->prepare("SELECT * FROM `so_users` WHERE `token`=:token");
        $sth->bindParam(':token',$token, PDO::PARAM_STR);
        $ret = $sth->execute();
        $users = $sth->fetchAll(PDO::FETCH_ASSOC);
        if($users&&sizeof($users)==1){
            if(MD5($users[0]['username'])==substr($token,0,32)&&strtotime($users['token_expire'])>strtotime(time())){
                /**
                * TO-do add access rights
                */
                $update_sth = $db->prepare(
                    "UPDATE `so_users`
                        SET
                            `token_expire` = :token_expire
                        WHERE `so_users`.`id` = :user_id;"
                );
                //Get more time for this token
                $update_sth->bindParam('token_expire',date('Y-m-d H:i:s', strtotime('+1 hour')),PDO::PARAM_STR);
                $update_sth->execute();
            }else{
                throw new UnauthorizedException('Unauthorized Access');
            }
        }else{
            throw new UnauthorizedException('Unauthorized Access');
        }
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
