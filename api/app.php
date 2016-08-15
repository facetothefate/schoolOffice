<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once "vendor/autoload.php";
require_once "db.php";
require_once "auth.php";

function handelPDOException($e,$res){
    $res = $res->withStatus(500)->withHeader('Content-type', 'application/json');
    $res->getBody()->write(json_encode(
        [
            'error' => $e->getMessage(),
        ]
    ));
    return $res;
}
function sendJsonArray($res,$data){
    if($data){
        $res = $res->withStatus(200)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode($data));
        return $res;
    }else{
        $res = $res->withStatus(200)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode([]));
        return $res;
    }
}
function sendJsonObject($res,$data){
    if($data){
        $res = $res->withStatus(200)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode($data));
        return $res;
    }else{
        $res = $res->withStatus(404)->withHeader('Content-type', 'application/json');
        return $res;
    }
}

function handelDb($callback,$req,$res){
    try{
        $db = getDB();
        return $callback($req,$res,$db);
        $db = null;
    }catch(PDOException $e) {
        return handelPDOException($e,$res);
    }
}
//Auth configuration

$authenticator = function($request, TokenAuthentication $tokenAuth){
    # Search for token on header, parameter, cookie or attribute
    $token = $tokenAuth->findToken($request);
    $auth = new TokenAuth();
    $auth->verifyToken($token);
};


//application configuration
$app = new \Slim\App;

//Auth
$app->post('/api/user/login',function(Request $req, Response $res){
    $auth = new TokenAuth();
    $json = json_decode($req->getBody(),true);
    try{
        $tokenData = $auth->getToken($json['username'],$json['password']);
        if($tokenData){
            return sendJsonObject($res,$tokenData);
        }else{
            $res = $res->withStatus(400)->withHeader('Content-type', 'application/json');
            $res->getBody()->write(json_encode([
                'error'=>'Wrong params'
            ]));
            return $res;
        }
    }catch(UnauthorizedException $e){
        $res = $res->withStatus(401)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode([
            'error' => $e->getMessage(),
        ]));
        return $res;
    }catch(InvaildUsernamePasswordException $e){
        $res = $res->withStatus(404)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode([
            'error' => $e->getMessage(),
        ]));
        return $res;
    }
});

$app->get('/api/user/logout',function(Request $req, Response $res){

});

/************************************
*   User API
*************************************/


/************************************
*   Categories API
*************************************/
//GET the whole course categories list
$app->get('/api/course_categories',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare("SELECT * FROM `so_course_categories`");
            $ret = $sth->execute();
            $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$categories);
        },
        $req,$res
    );
});

/************************************
*   Students API
*************************************/
$app->get('/api/students',function (Request $req, Response $res) {
    global $student_query;
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
            "SELECT
                `so_students`.`student_number`,
                `so_students`.`oen`,
                `so_students`.`enter_grade`,
                `so_students`.`enter_date`,
                `so_students`.`birthday`,
                `so_users`.`firstname`,
                `so_users`.`lastname`,
                `so_users`.`telephone`,
                `so_users`.`email`,
                `so_users`.`address1`,
                `so_users`.`address2`,
                `so_users`.`city`,
                `so_users`.`state`,
                `so_users`.`postal_code`,
                `so_diplomas`.`id` AS 'diploma_id',
                `so_diplomas`.`name` AS 'diploma'

                FROM `so_students`
                INNER JOIN `so_users`
                ON `so_students`.`so_users_id`= `so_users`.`id`
                INNER JOIN `so_diplomas`
                ON `so_students`.`so_diplomas_id` = `so_diplomas`.`id`"
            );
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});
$app->get('/api/students/{student_number}',function (Request $req, Response $res){
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
            "SELECT
                `so_students`.`student_number`,
                `so_students`.`oen`,
                `so_students`.`enter_grade`,
                `so_students`.`enter_date`,
                `so_students`.`birthday`,
                `so_users`.`firstname`,
                `so_users`.`lastname`,
                `so_users`.`telephone`,
                `so_users`.`email`,
                `so_users`.`address1`,
                `so_users`.`address2`,
                `so_users`.`city`,
                `so_users`.`state`,
                `so_users`.`postal_code`,
                `so_diplomas`.`id` AS 'diploma_id',
                `so_diplomas`.`name` AS 'diploma'

                FROM `so_students`
                INNER JOIN `so_users`
                ON `so_students`.`so_users_id`= `so_users`.`id`
                INNER JOIN `so_diplomas`
                ON `so_students`.`so_diplomas_id` = `so_diplomas`.`id`
                WHERE `so_students`.`student_number` = :student_number"
            );
            $student_number =  $req->getAttribute('student_number');
            $sth->bindParam(':student_number', $student_number, PDO::PARAM_STR);
            $ret = $sth->execute();
            $resData = $sth->fetch(PDO::FETCH_ASSOC);
            return sendJsonObject($res,$resData);
        },
        $req,$res
    );
});
$app->get('/api/students/user/{username}',function (Request $req, Response $res){
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
            "SELECT
                `so_students`.`student_number`,
                `so_students`.`oen`,
                `so_students`.`enter_grade`,
                `so_students`.`enter_date`,
                `so_students`.`birthday`,
                `so_users`.`firstname`,
                `so_users`.`lastname`,
                `so_users`.`telephone`,
                `so_users`.`email`,
                `so_users`.`address1`,
                `so_users`.`address2`,
                `so_users`.`city`,
                `so_users`.`state`,
                `so_users`.`postal_code`,
                `so_diplomas`.`id` AS 'diploma_id',
                `so_diplomas`.`name` AS 'diploma'

                FROM `so_students`
                INNER JOIN `so_users`
                ON `so_students`.`so_users_id`= `so_users`.`id`
                INNER JOIN `so_diplomas`
                ON `so_students`.`so_diplomas_id` = `so_diplomas`.`id`
                WHERE `so_users`.`username` = :username"
            );
            $username =  $req->getAttribute('username');
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $ret = $sth->execute();
            $resData = $sth->fetch(PDO::FETCH_ASSOC);
            return sendJsonObject($res,$resData);
        },
        $req,$res
    );
});



/************************************
*   course API
*************************************/
$app->get('/api/courses',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
                "SELECT
                    `so_courses`.course_code AS 'code',
                    `so_courses`.name,
                    `so_courses`.credit,
                    `so_courses`.grade,
                    `so_courses`.prerequisite,
                    `so_courses`.`so_course_categories_id` AS 'category_id',
                    `so_course_categories`.`title` AS 'category'
                    FROM `so_courses`
                    INNER JOIN `so_course_categories`
                    ON `so_courses`.`so_course_categories_id` = `so_course_categories`.`id`"

            );
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});
$app->get('/api/courses/category/{category}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
                "SELECT
                    `so_courses`.course_code AS 'code',
                    `so_courses`.name,
                    `so_courses`.credit,
                    `so_courses`.grade,
                    `so_courses`.prerequisite,
                    `so_courses`.`so_course_categories_id` AS 'category_id',
                    `so_course_categories`.`title` AS 'category'
                    FROM `so_courses`
                    INNER JOIN `so_course_categories`
                    ON `so_courses`.`so_course_categories_id` = `so_course_categories`.`id`
                    WHERE `so_courses`.`so_course_categories_id` = :category"

            );
            $category =  $req->getAttribute('category');
            $sth->bindParam(':category', $category, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});
/************************************
*   Semesters API
*************************************/

$app->get('/api/semesters',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
                "SELECT
                    `so_semesters`.`id`,
                    `so_semesters`.`semester`,
                    `so_schedule`.`start`,
                    `so_schedule`.`end`
                    FROM `so_semesters`
                    INNER JOIN `so_schedule`
                    ON `so_semesters`.`so_schedule_id` = `so_schedule`.`id`"
            );
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});

/************************************
*   Exam API
*************************************/

/************************************
*   Conditions API
*************************************/
$app->get('/api/conditions/{diploma}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
                "SELECT
                    *
                    FROM `so_graduation_conditions`
                    INNER JOIN `so_course_categories`
                    ON `so_course_categories`.`id` = `so_graduation_conditions`.`so_course_categories_id`
                    WHERE `so_graduation_conditions`.`so_diplomas_id` = :diploma"
            );
            $diploma =  $req->getAttribute('diploma');
            $sth->bindParam(':diploma', $diploma, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});
$app->run();
?>
