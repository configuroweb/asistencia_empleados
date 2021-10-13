<?php
date_default_timezone_set('America/Bogota');
if(!is_dir(__DIR__.'./db'))
    mkdir(__DIR__.'./db');
if(!defined('db_file')) define('db_file',__DIR__.'./db/attendance_db.db');
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        $this->open(db_file);
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        $this->exec("CREATE TABLE IF NOT EXISTS `admin_list` (
            `admin_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL Default 1,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        //User Comment
        // Type = [ 1 = Administrator, 2 = Cashier]
        // Status = [ 1 = Active, 2 = Inactive]
        $this->exec("CREATE TABLE IF NOT EXISTS `department_list` (
            `department_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `position_list` (
            `position_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `employee_list` (
            `employee_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `department_id` INTEGER NOT NULL,
            `position_id` INTEGER NOT NULL,
            `employee_code` TEXT NOT NULL,
            `firstname` TEXT NOT NULL,
            `middlename` TEXT  NULL,
            `lastname` TEXT NOT NULL,
            `gender` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `email` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `address` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`department_id`) REFERENCES `department_list`(`department_id`) ON DELETE CASCADE,
            FOREIGN KEY (`position_id`) REFERENCES `position_list`(`position_id`) ON DELETE CASCADE
        ) ");

        $this->exec("CREATE TABLE IF NOT EXISTS `attendance_list` (
            `attendance_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `employee_id` INTEGER NOT NULL,
            `device_type` TEXT NOT NULL,
            `att_type` TEXT NOT NULL,
            `ip` TEXT NOT NULL,
            `location` TEXT NOT NULL,
            `json_data` TEXT NOT NULL,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`employee_id`) REFERENCES `employee_list`(`employee_id`) ON DELETE CASCADE
        ) ");
        // $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_prod AFTER UPDATE on `vacancy_list`
        // BEGIN
        //     UPDATE `vacancy_list` SET date_updated = CURRENT_TIMESTAMP where vacancy_id = vacancy_id;
        // END
        // ");
        $this->exec("INSERT or IGNORE INTO `admin_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");

    }
    function isMobileDevice(){
        $aMobileUA = array(
            '/iphone/i' => 'iPhone', 
            '/ipod/i' => 'iPod', 
            '/ipad/i' => 'iPad', 
            '/android/i' => 'Android', 
            '/blackberry/i' => 'BlackBerry', 
            '/webos/i' => 'Mobile'
        );
    
        //Return true if Mobile User Agent is detected
        foreach($aMobileUA as $sMobileKey => $sMobileOS){
            if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
                return true;
            }
        }
        //Otherwise return false..  
        return false;
    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();