<?php

// DB PRODUCCION 
const DB_CALL_PROD =  array("host" => "localhost", "user" => "root", "password" => "", "db" => "call");
const DB_ABMMOD_PROD =  array("host" => "192.168.1.250", "user" => "root", "password" => "", "db" => "abmmod");
const DB_LAT_LNG_PROD =  array("host" => "localhost", "user" => "root", "password" => "", "db" => "sistema_rutas");
//DEV O DB TEST 
const DB_CALL_DEV =  array("host" => "localhost", "user" => "root", "password" => "", "db" => "call_dev");
const DB_ABMMOD_DEV  =  array("host" => "localhost", "user" => "root", "password" => "", "db" => "abmmod_dev");
const DB_LAT_LNG_DEV =  array("host" => "localhost", "user" => "root", "password" => "", "db" => "sistema_rutas");

const DB_CALL = PRODUCCION ? DB_CALL_PROD  : DB_CALL_DEV;
const DB_ABM = PRODUCCION ? DB_ABMMOD_PROD : DB_ABMMOD_DEV;
const DB_SISTEMA_RUTAS = PRODUCCION  ? DB_LAT_LNG_PROD : DB_LAT_LNG_DEV;
