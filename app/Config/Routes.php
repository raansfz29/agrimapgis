<?php
/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'Landing::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/export/(:any)', 'Dashboard::export/$1');
$routes->get('/dashboard/activities', 'Dashboard::activities');

// FIX GEOM ROUTE
$routes->get('/fixgeom', 'Fixgeom::index');

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
$routes->post('/activity/sync-offline', 'Activity::syncOffline');
$routes->post('/activity/update/(:num)', 'Activity::update/$1');
$routes->get('/uploads/(:any)', 'Activity::showImage/$1');

// Land Routes
$routes->get('/land', 'Land::index');
$routes->get('/land/add', 'Map::index'); // Fallback redirect to Map
$routes->get('/land/detail/(:num)', 'Land::detail/$1');
$routes->post('/land/update-detail/(:num)', 'Land::updateDetail/$1');

// Traceability Routes (public, no auth needed)
$routes->get('/trace/(:num)', 'Trace::index/$1');

// Disaster Routes
$routes->get('/disaster', 'Disaster::index');
$routes->get('/disaster/add', 'Disaster::add');
$routes->post('/disaster/activate', 'Disaster::activate');
$routes->post('/disaster/deactivate', 'Disaster::deactivate');
$routes->post('/disaster/broadcast', 'Disaster::broadcast');

// Map Routes
$routes->get('/peta-gis', 'Map::index');
$routes->get('/map/api-lands', 'Map::apiLands');
$routes->get('/map/api-heatmap', 'Map::apiHeatmap');

// Notification Routes
$routes->get('/notification', 'Notification::index');
$routes->get('/notification/api-get', 'Notification::apiGet');
$routes->get('/notification/read/(:num)', 'Notification::markAsRead/$1');
$routes->get('/notification/read-all', 'Notification::markAllRead');
$routes->get('/notification/check', 'Notification::checkNew');

// Farmer Groups Routes
$routes->get('/farmer-groups', 'FarmerGroup::index');
$routes->post('/farmer-groups/store', 'FarmerGroup::store');
$routes->post('/farmer-groups/update/(:num)', 'FarmerGroup::update/$1');
$routes->post('/farmer-groups/store-farmer', 'FarmerGroup::storeFarmer');
$routes->post('/farmer-groups/update-farmer/(:num)', 'FarmerGroup::updateFarmer/$1');
$routes->get('/farmer-groups/delete-farmer/(:num)', 'FarmerGroup::deleteFarmer/$1');
$routes->post('/farmer-groups/store-land', 'FarmerGroup::storeLand');
$routes->post('/farmer-groups/update-land', 'FarmerGroup::updateLand');
$routes->get('/farmer-groups/delete-land/(:num)', 'FarmerGroup::deleteLand/$1');
$routes->get('/farmer-groups/get-farmers-by-group/(:num)', 'FarmerGroup::getFarmersByGroup/$1');

// Add PWA Routes here at the end of the file
$routes->get('/offline', 'Pwa::offline');
$routes->get('/manifest.json', 'Pwa::manifest');
$routes->get('/service-worker.js', 'Pwa::serviceWorker');

// Auth Routes (Put at bottom to not conflict, or top, doesn't matter since they are distinct)
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginSubmit');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerSubmit');

// Temporary script to migrate old lands
$routes->get('/migrate-geojson', 'Migrate::geojson');
