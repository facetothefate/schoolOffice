<?php
require_once "vendor/autoload.php";
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Middleware\TokenAuthentication;

require_once "db.php";
require_once "auth.php";
require_once "utils.php";
function handelPDOException($e,$res){
    $res = $res->withStatus(500)->withHeader('Content-type', 'application/json');
    $res->getBody()->write(json_encode(
        [
            'error' => $e->getMessage(),
        ]
    ));
    return $res;
}

function handelDb($callback,$req,$res){
    global $config;
    try{
        $db = getDB();
        return $callback($req,$res,$db,$config);
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
    $auth->verifyToken($request,$token);
    //$request['token_auth'] = $auth;
};


//application configuration
$app = new \Slim\App;

//Config auth

$app->add(new TokenAuthentication([
    'path' =>   '/api',
    'passthrough' => '/api/user/login',
    'header' => $config['auth-header'],
    'regex' => '/(.*)$/i', //our own token type
    'authenticator' => $authenticator,
    'secure' => $config['https'],
    //'relaxed' => 'localhost'
]));


/********************************

API rights

We hcve 3 roles

student teacher admin

for those restricted url which only allowed the admin

please let url begin with admin

**********************************/

//Auth
$app->post('/api/user/login',function(Request $req, Response $res){
    $auth = new TokenAuth();
    $json = json_decode($req->getBody(),true);
    try{
        $tokenData = $auth->getToken($json['username'],$json['password']);
        if($tokenData){
            return $res->withJson($tokenData);
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
            return $res->withJson($categories);
        },
        $req,$res
    );
});

/************************************
*   Students API
*************************************/
$app->get('/api/students',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
            "SELECT
                `so_students`.`student_number`,
                `so_students`.`oen`,
                `so_students`.`enter_grade`,
                `so_students`.`enter_date`,
                `so_students`.`birthday`,
                `so_users`.`gender`,
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
            return $res->withJson($resData);
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
                `so_users`.`gender`,
                `so_users`.`firstname`,
                `so_users`.`lastname`,
                `so_users`.`telephone`,
                `so_users`.`email`,
                `so_users`.`address1`,
                `so_users`.`address2`,
                `so_users`.`city`,
                `so_users`.`state`,
                `so_users`.`postal_code`,
                `so_diplomas`.`id` AS 'diploma',
                `so_diplomas`.`name` AS 'diploma_text'

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
            return $res->withJson($resData);
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
                `so_users`.`gender`,
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
            return $res->withJson($resData);
        },
        $req,$res
    );
});
$app->post('/api/admin/students',function(Request $req, Response $res){
    return handelDb(
        function($req,$res,$db){
            $data = json_decode($req->getbody(),true);
            if(!array_key_exists('homeroom',$data)){
                $data['homeroom']=2;
            }
            $sth = $db->prepare(
                "SELECT MAX(`id`)+1 as userId FROM so_users"
            );
            $res = $sth->execute();
            $userId = $sth->fetchColumn();
            /*
            *   Student Id format
            *   1 16 00001
            *   | |    |________________________
            *   | |____________                |
            *   |student role | register year  | user id
            */
            $studentId = '1'.date("y").substr(100000+$userId%100000,1,5);
            $data['username'] = $studentId;
            //Fix-me
            //Default passwd same as username;
            //Generage a random one and send an email out.
            $data['password'] = $studentId;

            $sth = $db->prepare(
                "INSERT INTO `so_users`(
                        `id`,
                        `username`,
                        `password`,
                        `email`,
                        `firstname`,
                        `lastname`,
                        `gender`,
                        `address1`,
                        `address2`,
                        `city`,
                        `state`,
                        `postal_code`,
                        `telephone`,
                        `role`
                    )
                     VALUES (
                        :user_id,
                        :username,
                        :password,
                        :email,
                        :firstname,
                        :lastname,
                        :gender,
                        :address1,
                        :address2,
                        :city,
                        :state,
                        :postal_code,
                        :telephone,
                        '1'
                    )"
            );
            $sth->bindParam(':user_id',$userId,PDO::PARAM_INT);
            $sth->bindParam(':username',$data['username'],PDO::PARAM_STR);
            $sth->bindParam(':password',$data['password'],PDO::PARAM_STR);
            $sth->bindParam(':email',$data['email'],PDO::PARAM_STR);
            $sth->bindParam(':firstname',$data['firstname'],PDO::PARAM_STR);
            $sth->bindParam(':lastname',$data['lastname'],PDO::PARAM_STR);
            $sth->bindParam(':gender',$data['gender'],PDO::PARAM_INT);
            $sth->bindParam(':address1',$data['address1'],PDO::PARAM_STR);
            $sth->bindParam(':address2',$data['address2'],PDO::PARAM_STR);
            $sth->bindParam(':city',$data['city'],PDO::PARAM_STR);
            $sth->bindParam(':state',$data['state'],PDO::PARAM_STR);
            $sth->bindParam(':postal_code',$data['postal_code'],PDO::PARAM_STR);
            $sth->bindParam(':telephone',$data['telephone'],PDO::PARAM_STR);
            $ret = $sth->execute();
            $rowCountUser = $sth->rowCount();

            $sth = $db->prepare(
                "INSERT INTO
                `so_students`
                    (
                        `student_number`,
                        `oen`,
                        `enter_grade`,
                        `enter_date`,
                        `birthday`,
                        `so_users_id`,
                        `homeroom`,
                        `so_diplomas_id`
                    )
                    VALUES
                    (
                        :student_number,
                        :oen,
                        :enter_grade,
                        :enter_date,
                        :birthday,
                        :user_id,
                        :homeroom,
                        '1'
                    )"
            );
            $sth->bindParam(':student_number', $studentId, PDO::PARAM_INT);
            $sth->bindParam(':oen',$data['oen'],PDO::PARAM_STR);
            $sth->bindParam(':enter_grade',$data['enter_grade'],PDO::PARAM_STR);
            $sth->bindParam(':enter_date',$data['enter_date'],PDO::PARAM_STR);
            $sth->bindParam(':birthday',$data['birthday'],PDO::PARAM_STR);
            $sth->bindParam(':user_id',$userId,PDO::PARAM_INT);
            $sth->bindParam(':homeroom',$data['homeroom'],PDO::PARAM_INT);
            $sth->execute();
            $rowCountStudent = $sth->rowCount();
            if($rowCountUser && $rowCountStudent){
                return $res->withStatus(200);
            } else {
                return $res->withStatus(300);
            }
        },
        $req,$res
    );
});

$app->put('/api/students',function(Request $req, Response $res){
    return handelDb(
        function($req,$res,$db){

        },
        $req,$res
    );
});

$app->put('/api/admin/students',function(Request $req, Response $res){
    return handelDb(
        function($req,$res,$db){

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
            return $res->withJson($resData);
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
            return $res->withJson($resData);
        },
        $req,$res
    );
});

//get the user's course selection
$app->get('/api/course-selections/{studentNumber}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db, $config){
            $studentNumber =  $req->getAttribute('studentNumber');
            $sth = $db->prepare(
                "SELECT
                    `so_course_selections`.so_students_student_number AS 'student_number',
                    `so_courses`.course_code AS 'code',
                    `so_courses`.name,
                    `so_courses`.credit,
                    `so_courses`.grade,
                    `so_courses`.`so_course_categories_id` AS 'category_id',
                    `so_semesters`.`id` AS 'semester_id',
                    `so_semesters`.`semester`,
                    `so_course_selections`.status
                    FROM `so_course_selections`
                    INNER JOIN `so_courses`
                        ON `so_course_selections`.`so_courses_course_code` = `so_courses`.`course_code`
                    INNER JOIN `so_semesters`
                        ON `so_course_selections`.`so_semesters_id` = `so_semesters`.`id`
                    WHERE `so_course_selections`.so_students_student_number = :studentNumber"

            );
            $sth->bindParam(':studentNumber', $studentNumber, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

$app->get('/api/course-selections/semester/{semesterId}/unscheduled/summary',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semesterId =  $req->getAttribute('semesterId');
            $sth = $db->prepare(
                "SELECT
                    count(DISTINCT `so_course_selections`.`so_students_student_number`) AS
                    'total_selections', `so_courses`.course_code AS 'code',
                    `so_courses`.name, `so_courses`.credit, `so_courses`.grade
                    FROM `so_course_selections`
                    INNER JOIN `so_courses` ON `so_course_selections`.`so_courses_course_code` = `so_courses`.`course_code`
                    WHERE `so_course_selections`.`so_semesters_id` = :semesterId
                    AND `so_course_selections`.`status` = 0
                    GROUP BY `so_course_selections`.so_courses_course_code"
            );
            $sth->bindParam(':semesterId', $semesterId, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

$app->put('/api/admin/course-selections/semester/{semesterId}/reject/course/{code}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semesterId =  $req->getAttribute('semesterId');
            $code = $req->getAttribute('code');
            $sth = $db->prepare(
                "UPDATE `so_course_selections`
                    SET `status` = 2
                    WHERE `so_course_selections`.`so_semesters_id` = :semesterId
                    AND `so_course_selections`.`so_courses_course_code` = :code");
            $sth->bindParam(':code', $code, PDO::PARAM_STR);
            $sth->bindParam(':semesterId', $semesterId, PDO::PARAM_INT);
            $ret = $sth->execute();
            return $res->withStatus(200);
        },
        $req,$res
    );
});

//create a user's course selection
$app->post('/api/course-selections',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db, $config){
            $token = $req->getHeader($config['auth-header']);
            $auth = new TokenAuth();
            $data = json_decode($req->getbody(),true);
            if(!$data){
                return $res->withStatus(405);
            }
            if(is_assoc($data)){
                $data = array($data);
            }
            //print_r($data);
            foreach ($data as $item) {
                $sth = $db->prepare(
                    "SELECT count(*) FROM `so_course_selections`
                        WHERE so_students_student_number=:studentNumber
                        AND   so_semesters_id=:semesterId
                        AND   so_courses_course_code=:courseCode"
                );
                $sth->bindParam(':studentNumber', $item['student_number'], PDO::PARAM_INT);
                $sth->bindParam(':semesterId', $item['semester_id'], PDO::PARAM_INT);
                $sth->bindParam(':courseCode', $item['course_code'], PDO::PARAM_STR);
                $ret = $sth->execute();
                $numberRow = $sth->fetchColumn();
                if($numberRow){
                    return sendJson($res,409,['error'=>'You have already select that course:'. $item['course_code']]);
                }
                $sth = $db->prepare(
                    "SELECT count(*) FROM `so_courses`
                        INNER JOIN `so_course_selections`
                        ON `so_courses`.prerequisite = `so_course_selections`.so_courses_course_code
                        WHERE `so_courses`.course_code = :courseCode
                        AND `so_course_selections`.status !='0'"
                );
                $sth->bindParam(':courseCode', $item['course_code'], PDO::PARAM_STR);
                $ret = $sth->execute();
                $numberRow = $sth->fetchColumn();
                if($numberRow){
                    return sendJson($res,400,['error'=>'You have not met that course prerequisite:'. $item['course_code']]);
                }
                $sth = $db->prepare(
                    "INSERT INTO
                        `so_course_selections`
                        (`so_students_student_number`,
                        `so_semesters_id`,
                        `so_courses_course_code`,
                        `status`)
                        VALUES
                        (:studentNumber, :semesterId, :courseCode, '0')"
                );
                $sth->bindParam(':studentNumber', $item['student_number'], PDO::PARAM_INT);
                $sth->bindParam(':semesterId', $item['semester_id'], PDO::PARAM_INT);
                $sth->bindParam(':courseCode', $item['course_code'], PDO::PARAM_STR);
                $ret = $sth->execute();
            }
            return $res->withStatus(200);
        },
        $req,$res
    );
});
$app->delete('/api/course-selections/student/{studentNumber}/semester/{semesterId}/code/{courseCode}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db, $config){
            $studentNumber =  (int)$req->getAttribute('studentNumber');
            $semester = (int)$req->getAttribute('semesterId');
            $courseCode = trim($req->getAttribute('courseCode'));
            $sth = $db->prepare(
                "DELETE
                    FROM `so_course_selections`
                    WHERE `so_course_selections`.`so_students_student_number` = :studentNumber
                    AND `so_course_selections`.`so_semesters_id` =  :semesterId
                    AND `so_course_selections`.`so_courses_course_code` = :courseCode;
                    AND `so_course_selections`.'status' != 1
                    "
            );
            $sth->bindParam(':studentNumber', $studentNumber, PDO::PARAM_INT);
            $sth->bindParam(':semesterId', $semester, PDO::PARAM_INT);
            $sth->bindParam(':courseCode', $courseCode, PDO::PARAM_STR);
            $ret = $sth->execute();
            $rowCount = $sth->rowCount();
            if($rowCount)
                return $res->withStatus(200);
            else {
                return sendJson($res,404,["error" => "Cannot find for ".$studentNumber."-".$semester."-".$courseCode.""]);
            }
        },
        $req,$res
    );
});

$app->get('/api/course-schedule/semester/{semesterId}/schedule/{weekday}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $weekday =  (int)$req->getAttribute('weekday');
            $semester = (int)$req->getAttribute('semesterId');
            $sth = $db->prepare(
                "SELECT
                        `so_courses`.course_code AS code,
                        `so_courses`.name AS name,
                        `so_schedule`.start AS start_date,
                        `so_schedule`.end AS end_data,
                        `so_schedule`.start_time AS start_time,
                        `so_schedule`.end_time AS end_time,
                        `so_schedule`.repeat_day AS repeat_day
                    FROM
                        `so_course_schedule`
                    INNER JOIN `so_courses`
                    ON `so_course_schedule`.so_courses_course_code = `so_courses`.course_code
                    INNER JOIN `so_schedule`
                    ON `so_course_schedule`.so_schedule_id = `so_schedule`.id
                    WHERE
                        `so_course_schedule`.so_semesters_id = :semesterId
                    AND
                        `so_schedule`.repeat_day = :weekday
                "
            );
            $sth->bindParam(':weekday', $weekday, PDO::PARAM_INT);
            $sth->bindParam(':semesterId', $semester, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

$app->post('/api/admin/course-schedule/semester/{semesterId}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester = (int)$req->getAttribute('semesterId');
            $data = json_decode($req->getbody(),true);
            if(!$data){
                return $res->withStatus(405);
            }
            if(is_assoc($data)){
                $data = array($data);
            }
            $sth = $db->prepare(
                "SELECT
                    `so_schedule`.start AS start,
                    `so_schedule`.end AS end
                FROM
                    `so_schedule`
                INNER JOIN
                    `so_semesters`
                ON
                    `so_schedule`.id = `so_semesters`.`so_schedule_id`
                WHERE
                    `so_semesters`.id = :semesterId
            ");
            $sth->bindParam(':semesterId', $semester, PDO::PARAM_INT);
            $sth->execute();
            $semesterData = $sth->fetch(PDO::FETCH_ASSOC);
            //print_r($semesterData);
            //print_r($data);
            foreach($data as $item){
                $sth = $db->prepare(
                    "INSERT INTO `so_schedule` (
                        `start`,
                        `end`,
                        `start_time`,
                        `end_time`,
                        `repeat_day`,
                        `so_location_id`
                    )
                    VALUES (
                        :start,
                        :end,
                        :start_time,
                        :end_time,
                        :repeat_day,
                        '1'
                    )");
                $sth->bindParam(':start', $semesterData['start'], PDO::PARAM_STR);
                $sth->bindParam(':end', $semesterData['end'], PDO::PARAM_STR);
                $sth->bindParam(':start_time', $item['start_time'], PDO::PARAM_STR);
                $sth->bindParam(':end_time', $item['end_time'], PDO::PARAM_STR);
                $sth->bindParam(':repeat_day', $item['repeat_day'], PDO::PARAM_INT);
                $ret = $sth->execute();
                $scheduleId =$db->lastInsertId();

                $sth = $db->prepare(
                    "INSERT INTO `so_course_schedule`(
                        `so_schedule_id`,
                        `so_courses_course_code`,
                        `so_semesters_id`,
                        `so_teachers_so_users_id`
                    )
                    VALUES(
                        :scheduleId,
                        :code,
                        :semesterId,
                        '2'
                    )"
                );
                $sth->bindParam(':scheduleId', $scheduleId, PDO::PARAM_INT);
                $sth->bindParam(':code', $item['code'], PDO::PARAM_STR);
                $sth->bindParam(':semesterId', $semester, PDO::PARAM_INT);
                $ret = $sth->execute();
                //update the selection status
                $sth = $db->prepare(
                    "UPDATE `so_course_selections`
                        SET `status` = 1
                        WHERE `so_course_selections`.`so_semesters_id` = :semesterId
                        AND `so_course_selections`.`so_courses_course_code` = :code");
                $sth->bindParam(':code', $item['code'], PDO::PARAM_STR);
                $sth->bindParam(':semesterId', $semester, PDO::PARAM_INT);
                $ret = $sth->execute();
            }
            return $res->withStatus(200);
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
                    `so_semesters`.`open_for_register`,
                    `so_schedule`.`start`,
                    `so_schedule`.`end`
                    FROM `so_semesters`
                    INNER JOIN `so_schedule`
                    ON `so_semesters`.`so_schedule_id` = `so_schedule`.`id`
                    ORDER BY `so_schedule`.`start` DESC"
            );
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

$app->post('/api/admin/semesters',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $data = json_decode($req->getbody(),true);
            $sth = $db->prepare(
                "INSERT INTO `so_schedule` (
                    `start`,
                    `end`,
                    `start_time`,
                    `end_time`,
                    `repeat_day`,
                    `so_location_id`
                )
                VALUES (
                    :start,
                    :end,
                    Null,
                    Null,
                    Null,
                    '1'
                )");
            $sth->bindParam(':start', $data['start'], PDO::PARAM_STR);
            $sth->bindParam(':end', $data['end'], PDO::PARAM_STR);
            $ret = $sth->execute();
            $scheduleId =$db->lastInsertId();
            $sth = $db->prepare(
                "INSERT INTO `so_semesters`
                (`semester`, `open_for_register`, `so_schedule_id`)
                VALUES ( :name,'FALSE',:scheduleId)"
            );
            $sth->bindParam(':name', $data['semester'], PDO::PARAM_STR);
            $sth->bindParam(':scheduleId', $scheduleId, PDO::PARAM_INT);
            $ret = $sth->execute();
            return $res->withStatus(200);
        },
        $req,$res
    );
});

$app->put('/api/admin/semesters',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $data = json_decode($req->getbody(),true);
            $data['id'] = (int)$data['id'];
            $sth = $db->prepare(
                "UPDATE `so_schedule`
                    SET `start`=:start,
                        `end`=:end
                    WHERE
                        `so_schedule`.`id` = (
                            SELECT `so_semesters`.`so_schedule_id`
                            FROM `so_semesters`
                            WHERE
                                `so_semesters`.`id` = :id
                        );
                UPDATE `so_semesters`
                    SET `so_semesters`.`semester` = :name
                    WHERE
                        `so_semesters`.`id` = :id;
                ");
            $sth->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $sth->bindParam(':name', $data['semester'], PDO::PARAM_STR);
            $sth->bindParam(':start', $data['start'], PDO::PARAM_STR);
            $sth->bindParam(':end', $data['end'], PDO::PARAM_STR);
            $ret = $sth->execute();
            return $res->withStatus(200);
        },
        $req,$res
    );
});


$app->get('/api/semesters/{semester}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester =  $req->getAttribute('semester');
            $sth = $db->prepare(
                "SELECT
                    `so_semesters`.`id`,
                    `so_semesters`.`semester`,
                    `so_semesters`.`open_for_register`,
                    `so_schedule`.`start`,
                    `so_schedule`.`end`
                    FROM `so_semesters`
                    INNER JOIN `so_schedule`
                    ON `so_semesters`.`so_schedule_id` = `so_schedule`.`id`
                    WHERE `so_semesters`.`id` = :semester"
            );
            $sth->bindParam(':semester', $semester, PDO::PARAM_INT);
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

$app->get('/api/semester/open',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $sth = $db->prepare(
                "SELECT
                    `so_semesters`.`id`,
                    `so_semesters`.`semester`,
                    `so_semesters`.`open_for_register`,
                    `so_schedule`.`start`,
                    `so_schedule`.`end`
                    FROM `so_semesters`
                    INNER JOIN `so_schedule`
                    ON `so_semesters`.`so_schedule_id` = `so_schedule`.`id`
                    WHERE `so_semesters`.`open_for_register` = 1"
            );
            $ret = $sth->execute();
            $resData = $sth->fetch(PDO::FETCH_ASSOC);
            return $res->withJson($resData);
        },
        $req,$res
    );
});

//make a semester open for register
$app->put('/api/admin/semesters/{semester}/open',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester =  (int)$req->getAttribute('semester');
            //set all other to close
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 0"
            );
            $ret = $sth->execute();

            //set this one to open
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 1
                    WHERE `so_semesters`.`id` = :semester"
            );
            $sth->bindParam(':semester', $semester, PDO::PARAM_INT);
            $ret = $sth->execute();
            return $res->withStatus(200);
        },
        $req,$res
    );
});

//make a semester close for register
$app->put('/api/admin/semesters/{semester}/close',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester =  (int)$req->getAttribute('semester');
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 0
                    WHERE `so_semesters`.`id` = :semester"
            );
            $sth->bindParam(':semester', $semester, PDO::PARAM_INT);
            $ret = $sth->execute();
            return $res->withStatus(200);
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
            return $res->withJson($resData);
        },
        $req,$res
    );
});
$app->run();
?>
