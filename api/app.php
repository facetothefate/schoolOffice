<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once "vendor/autoload.php";
require_once "db.php";
require_once "auth.php";
function is_assoc($_array) {
    if ( !is_array($_array) || empty($array) ) {
        return -1;
    }
    foreach (array_keys($_array) as $k => $v) {
        if ($k !== $v) {
            return true;
        }
    }
    return false;
}
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
function sendJson($res,$code,$data){
    if($data){
        $res = $res->withStatus($code)->withHeader('Content-type', 'application/json');
        $res->getBody()->write(json_encode($data));
        return $res;
    }else{
        $res = $res->withStatus($code)->withHeader('Content-type', 'application/json');
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
    $auth->verifyToken($request,$token);
};


//application configuration
$app = new \Slim\App;


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

//get the user's course selection
$app->get('/api/course-selections/{studentNumber}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
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
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});

//create a user's course selection
$app->post('/api/course-selections',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $data = json_decode($req->getbody(),true);
            if(!$data){
                return $res->withStatus(405);
            }
            /*if(){
                $data = array($data);
            */
            //print_r($data);
            foreach ($data as $item) {

                $sth = $db->prepare(
                    "SELECT count(*) FROM `so_course_selections`
                        WHERE so_students_student_number=:studentNumber
                        AND   so_semesters_id=:semestersId
                        AND   so_courses_course_code=:courseCode"
                );
                $sth->bindParam(':studentNumber', $item['student_number'], PDO::PARAM_INT);
                $sth->bindParam(':semestersId', $item['semester_id'], PDO::PARAM_INT);
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
                        (:studentNumber, :semestersId, :courseCode, '0')"
                );
                $sth->bindParam(':studentNumber', $item['student_number'], PDO::PARAM_INT);
                $sth->bindParam(':semestersId', $item['semester_id'], PDO::PARAM_INT);
                $sth->bindParam(':courseCode', $item['course_code'], PDO::PARAM_STR);
                $ret = $sth->execute();
            }
            return $res->withStatus(200);
        },
        $req,$res
    );
});
$app->delete('/api/course-selections/student/{studentNumber}/semester/{semestersId}/code/{courseCode}',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $studentNumber =  (int)$req->getAttribute('studentNumber');
            $semester = (int)$req->getAttribute('semestersId');
            $courseCode = trim($req->getAttribute('courseCode'));
            $sth = $db->prepare(
                "DELETE
                    FROM `so_course_selections`
                    WHERE `so_course_selections`.`so_students_student_number` = :studentNumber
                    AND `so_course_selections`.`so_semesters_id` =  :semestersId
                    AND `so_course_selections`.`so_courses_course_code` = :courseCode;
                    AND `so_course_selections`.'status' != 1
                    "
            );
            $sth->bindParam(':studentNumber', $studentNumber, PDO::PARAM_INT);
            $sth->bindParam(':semestersId', $semester, PDO::PARAM_INT);
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
                    ON `so_semesters`.`so_schedule_id` = `so_schedule`.`id`"
            );
            $ret = $sth->execute();
            $resData = $sth->fetchAll(PDO::FETCH_ASSOC);
            return sendJsonArray($res,$resData);
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
            return sendJsonObject($res,$resData);
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
            return sendJsonObject($res,$resData);
        },
        $req,$res
    );
});

//make a semester open for register
$app->post('/api/admin/semesters/{semester}/open',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester =  $req->getAttribute('semester');
            //set all other to close
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 0"
            );
            $ret = $sth->execute();

            //set this one to open
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 1,
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
$app->post('/api/admin/semesters/{semester}/close',function (Request $req, Response $res) {
    return handelDb(
        function($req,$res,$db){
            $semester =  $req->getAttribute('semester');
            $sth = $db->prepare(
                "UPDATE `so_semesters`
                    set `so_semesters`.`open_for_register` = 0,
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
            return sendJsonArray($res,$resData);
        },
        $req,$res
    );
});
$app->run();
?>
