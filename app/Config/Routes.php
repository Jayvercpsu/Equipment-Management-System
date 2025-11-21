<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');

$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth\Login::index');
    $routes->post('login', 'Auth\Login::authenticate');
    $routes->get('logout', 'Auth\Login::logout');
    $routes->get('register', 'Auth\Register::index');
    $routes->post('register', 'Auth\Register::store');
    $routes->get('verify/(:any)', 'Auth\Register::verify/$1');
    $routes->get('forgot-password', 'Auth\ForgotPassword::index');
    $routes->post('forgot-password', 'Auth\ForgotPassword::send');
    $routes->get('reset-password/(:any)', 'Auth\ResetPassword::index/$1');
    $routes->post('reset-password', 'Auth\ResetPassword::reset');
});

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    
    $routes->group('users', ['filter' => 'role:itso_personnel'], function($routes) {
        $routes->get('/', 'Admin\UsersController::index');
        $routes->get('create', 'Admin\UsersController::create');
        $routes->post('store', 'Admin\UsersController::store');
        $routes->get('edit/(:num)', 'Admin\UsersController::edit/$1');
        $routes->post('update/(:num)', 'Admin\UsersController::update/$1');
        $routes->post('toggle/(:num)', 'Admin\UsersController::toggle/$1');
        $routes->post('delete/(:num)', 'Admin\UsersController::delete/$1');
    });
    
    $routes->group('equipment', function($routes) {
        $routes->get('/', 'Admin\EquipmentController::index');
        $routes->get('create', 'Admin\EquipmentController::create', ['filter' => 'role:itso_personnel']);
        $routes->post('store', 'Admin\EquipmentController::store', ['filter' => 'role:itso_personnel']);
        $routes->get('edit/(:num)', 'Admin\EquipmentController::edit/$1', ['filter' => 'role:itso_personnel']);
        $routes->post('update/(:num)', 'Admin\EquipmentController::update/$1', ['filter' => 'role:itso_personnel']);
        $routes->post('delete/(:num)', 'Admin\EquipmentController::delete/$1', ['filter' => 'role:itso_personnel']);
        $routes->get('view/(:num)', 'Admin\EquipmentController::view/$1');
    });
    
    $routes->group('borrow', function($routes) {
        $routes->get('/', 'Admin\BorrowController::index');
        $routes->get('create', 'Admin\BorrowController::create');
        $routes->post('store', 'Admin\BorrowController::store');
        $routes->get('view/(:num)', 'Admin\BorrowController::view/$1');
        $routes->post('approve/(:num)', 'Admin\BorrowController::approve/$1', ['filter' => 'role:itso_personnel']);
        $routes->post('cancel/(:num)', 'Admin\BorrowController::cancel/$1');
    });
    
    $routes->group('return', function($routes) {
        $routes->get('/', 'Admin\ReturnController::index');
        $routes->get('create/(:num)', 'Admin\ReturnController::create/$1');
        $routes->post('store', 'Admin\ReturnController::store');
    });
    
    $routes->group('reservation', function($routes) {
        $routes->get('/', 'Admin\ReservationController::index');
        $routes->get('create', 'Admin\ReservationController::create');
        $routes->post('store', 'Admin\ReservationController::store');
        $routes->get('view/(:num)', 'Admin\ReservationController::view/$1');
        $routes->post('cancel/(:num)', 'Admin\ReservationController::cancel/$1');
        $routes->get('reschedule/(:num)', 'Admin\ReservationController::reschedule/$1');
        $routes->post('update-schedule/(:num)', 'Admin\ReservationController::updateSchedule/$1');
    });
    
    $routes->group('reports', ['filter' => 'role:itso_personnel'], function($routes) {
        $routes->get('active-equipment', 'Admin\ReportsController::activeEquipment');
        $routes->get('unusable-equipment', 'Admin\ReportsController::unusableEquipment');
        $routes->get('user-history', 'Admin\ReportsController::userHistory');
        $routes->get('export/(:any)', 'Admin\ReportsController::export/$1');
    });
});

$routes->get('profile', 'Profile::index', ['filter' => 'auth']);
$routes->post('profile/update', 'Profile::update', ['filter' => 'auth']);