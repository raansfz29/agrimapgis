<?php
/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'Landing::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/export/(:any)', 'Dashboard::export/$1');
$routes->get('/dashboard/activities', 'Dashboard::activities');

// Message Routes
$routes->get('/message', 'Message::index');
$routes->get('/message/chat/(:num)', 'Message::chat/$1');
$routes->post('/message/send', 'Message::send');
$routes->get('/message/messages/(:num)', 'Message::messages/$1');
$routes->post('/message/send-ajax', 'Message::sendAjax');

// Statistics & Report Routes
$routes->get('/reports', 'Report::index');

// Profile Routes
$routes->get('/profile', 'Profile::index');
$routes->post('/profile/update', 'Profile::update');

// Activity Routes
$routes->get('/activity', 'Activity::index');
$routes->get('/activity/(:num)', 'Activity::index/$1');          // Split-panel: select activity
$routes->get('/activity/verification', 'Activity::index');
$routes->get('/activity/verification/(:num)', 'Activity::index/$1');
$routes->get('/activity/input', 'Activity::input');
$routes->get('/activity/detail/(:num)', 'Activity::detail/$1');
$routes->get('/activity/edit/(:num)', 'Activity::edit/$1');
$routes->post('/activity/approve/(:num)', 'Activity::approve/$1');
$routes->post('/activity/reject/(:num)', 'Activity::reject/$1');
$routes->post('/activity/reopen/(:num)', 'Activity::reopen/$1');
$routes->post('/activity/save', 'Activity::save');
$routes->post('/activity/update/(:num)', 'Activity::update/$1');
$routes->get('/uploads/(:any)', 'Activity::showImage/$1');

// Land Routes
$routes->get('/land', 'Land::index');
$routes->get('/land/add', 'Map::index'); // Fallback redirect to Map
$routes->get('/land/detail/(:num)', 'Land::detail/$1');

// Disaster Routes
$routes->get('/disaster', 'Disaster::index');
$routes->get('/disaster/activate/(:num)', 'Disaster::activate/$1');
$routes->post('/disaster/activate/(:num)', 'Disaster::activateSubmit/$1');
$routes->post('/disaster/deactivate/(:num)', 'Disaster::deactivate/$1');
$routes->post('/disaster/broadcast-alert', 'Disaster::broadcastAlert');
$routes->get('/disaster/log/(:num)', 'Disaster::log/$1');
$routes->post('/disaster/submitLog/(:num)', 'Disaster::submitLog/$1');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginSubmit');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerSubmit');

// Map Routes
$routes->get('/peta-gis', 'Map::index');
$routes->get('/map', 'Map::index'); // Alias for backward compatibility
$routes->get('/map/api-lands', 'Map::apiLands');
$routes->post('/api/lands/save', 'Map::saveLand');

// Farmer Group Routes
$routes->get('/farmer-groups', 'FarmerGroup::index');
$routes->post('/farmer-groups/store', 'FarmerGroup::store');
$routes->post('/farmer-groups/update/(:num)', 'FarmerGroup::update/$1');
$routes->post('/farmer-groups/store-farmer', 'FarmerGroup::storeFarmer');
$routes->post('/farmer-groups/store-land', 'FarmerGroup::storeLand');
$routes->get('/farmer-groups/delete-land/(:num)', 'FarmerGroup::deleteLand/$1');
$routes->get('/farmer-groups/get-farmers/(:num)', 'FarmerGroup::getFarmersByGroup/$1');

// Notification Routes
$routes->get('/notifications', 'Notification::index');
$routes->get('/notification/api-get', 'Notification::apiGet');
$routes->get('/notification/mark-read/(:num)', 'Notification::markRead/$1');
$routes->post('/notification/mark-all-read', 'Notification::markAllRead');
$routes->post('/notification/clear-all', 'Notification::clearAll');
