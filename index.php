<?php 
require_once __DIR__ . '/vendor/autoload.php';

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\controllers\HomeController;
use routes\Router;

require_once './utils.php';

Router::get('/home', 'HomeController', 'index');
Router::get('/', 'HomeController', 'index');
Router::post('/signup', 'AuthController', 'signup');
Router::post('/signin', 'AuthController', 'login');
Router::get('/logout', 'AuthController', 'logout');
Router::get('/admin/dashboard', 'AdminController', 'dashboard');
Router::get('/instructor/dashboard', 'InstructorController', 'dashboard');
Router::post('/course/add', 'CourseController', 'addCourse');
Router::post('/instructor/course/archive', 'CourseController', 'archiveCourse');
Router::post('/instructor/course/edit', 'CourseController', 'editCourse');
Router::get('/instructor/course/details/{id}', 'CourseController', 'getCourseDetails');
Router::post('/instructor/course/status', 'CourseController', 'toggleCourseStatus');
Router::post('/admin/manage-user', 'AdminController', 'manageUser');
Router::get('/courses', 'CourseController', 'listCourses');
Router::get('/course/{id}', 'CourseController', 'showCourseDetails');
Router::post('/enroll/{id}', 'CourseController','enroll');
Router::get('/my-courses', 'CourseController','myCourses');
Router::get('/search-courses', 'CourseController','searchCourses');




Router::resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);