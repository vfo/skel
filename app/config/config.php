<?php

date_default_timezone_set("Europe/Paris");
$config = array();

/* CORE */
//activation du mode debug
$config['core']['debug'] = true; // true or false
//definition de l'environnement
$config['core']['env'] = 'dev'; // dev or prod
//activation des logs
$config['core']['logs'] = true; // true or false
//action par defaut
$config['core']['default_action'] = ''; // default action
//action par defaut pour les utilisateurs connectes
$config['core']['default_logged_action'] = ''; // default action
//url frontale du serveur
$config['core']['servername'] = '';
//pour forcer la transmission HTTPS des cookies
$config['core']['secure_cookie'] = false;
//email de contact
$config['core']['contact'] = '';
//path du repertoire pour les ficheirs temporaires de telechargement
$config['core']['upl_tmp_path'] = '../tmp/upl/';
//nom du cookie de session
$config['core']['ssid_cookie_name'] = '';
//grain de sel
$config['core']['salt'] = '';
//liste des actions autorisees pour les utilisateurs non connectes
$config['core']['not_logged_permitted_actions'] = array();

/* BDD */


$config['db']['host'] = '';
$config['db']['base'] = '';
$config['db']['user'] = '';
$config['db']['pass'] = '';



/* SMARTY */
$config['smarty']['templates'] = '../app/views';
$config['smarty']['compil'] = '../tmp/compil';
$config['smarty']['cache'] = '../tmp/cache';
$config['smarty']['config'] = '../tmp/config';
/* TOOLS */
$config['tools']['gen_pwd'] = ''; //Mot de passe du generateur de model

/* TEMPLATES PAR DEFAUT */
//vue principale (layout)
$config['tpls']['default_layout'] = 'index.tpl'; // default layout tpl
//vue centrale par defaut
$config['tpls']['error'] = 'layouts/error.tpl';


?>