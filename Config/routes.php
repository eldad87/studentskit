<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Router::connect('/', array('controller' => 'Home', 'action' => 'index'));

/**
* Enable the json and or xml extensions
*/
Router::parseExtensions('json', 'rss'/*, 'xml'*/);



////////////
Router::connect('/register', array('controller' => 'Accounts', 'action' => 'register', 'plugin'=>false));
Router::connect('/activate', array('controller' => 'Accounts', 'action' => 'activate', 'plugin'=>false));
Router::connect('/activate/:activation_code', array('controller' => 'Accounts', 'action' => 'activate', 'plugin'=>false), array('pass' => 'activation_code'));
Router::connect('/forgotten_password/:password_reset', array('controller' => 'Accounts', 'action' => 'forgotten_password', 'plugin'=>false), array('pass' => 'password_reset_code'));
Router::connect('/login', array('controller' => 'Accounts', 'action' => 'login', 'plugin'=>false));
Router::connect('/logout', array('controller' => 'Accounts', 'action' => 'logout', 'plugin'=>false));
////////////

////////////
Router::connect('/forum/help/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'help'));
Router::connect('/forum/rules/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'rules'));
Router::connect('/admin/forum/settings/*',  array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'settings', 'admin' => true));
////////////

/**
 * Load all plugin routes.  See the CakePlugin documentation on 
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
