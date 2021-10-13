<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Usuario o Contraseña Inválidas";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Ingreso Exitoso";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function e_login(){
        extract($_POST);
        $sql = "SELECT *,(lastname || ', ' || firstname || ' ' || middlename) as name FROM employee_list where email = '{$email}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Correo o Contraseña Inválidas";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Acceso Exitoso";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function e_logout(){
        session_destroy();
        header("location:./");
    }
    
    function e_update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Antigua contraseña es incorrecta";
        }else{
            $sql = "UPDATE `employee_list` set {$data} where employee_id = '{$_SESSION['employee_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credenciales Actualizadas Exitósamente';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                    $_SESSION['name'] = $_SESSION['lastname'].", ".$_SESSION['firstname']." ".$_SESSION['middlename'];
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Proceso de Actualización de Credenciales Fallido Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_admin(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' ".($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Usuario Existe Actualmente";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `admin_list` {$data}";
            }else{
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'Nuev@ usuari@ guardado con éxito';
                else
                $resp['msg'] = 'Detalles de usuario actualizados correctamente.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'No se pudieron guardar los detalles del usuario. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_admin(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Usuario Eliminado Exitósamente';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Antigua Contraseña es Incorrecta";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credencial actualizada correctamente';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Actualización de Credenciales Fallida. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_settings(){
        extract($_POST);
        $update = file_put_contents('./about.html',htmlentities($about));
        if($update){
            $resp['status'] = "success";
            $resp['msg'] = "Configuración Actualizada Exitósamente";
        }else{
            $resp['status'] = "failed";
            $resp['msg'] = "Actualización de Configuración Fallida";
        }
        return json_encode($resp);
    }
    function save_department(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `department_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `department_list` set {$data} where department_id = '{$id}'";
        }

        $check = $this->query("SELECT count(department_id) as `count` FROM `department_list` where `name` = '{$name}' ".($id > 0 ? " and department_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Nombre de Departamento Existe Actualmente";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Departamento Actualizado Exitósamente";
                else
                    $resp['msg'] = "Departamento Actualizado Exitósamente";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "No se pudo Guardar el Nuevo Departamento";
                else
                    $resp['msg'] = "Proceso de Actualización de Departamento Falló";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_department(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `department_list` where department_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Departamento Eliminado Exitósamente';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_dept(){
        extract($_POST);

        $update = $this->query("UPDATE department_list set status = '{$status}' where department_id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = 'Estado de Departamento Actualizado Exitósamente';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Proceso de Actualización de Departamento Falló';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_position(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `position_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `position_list` set {$data} where position_id = '{$id}'";
        }

        $check = $this->query("SELECT count(position_id) as `count` FROM `position_list` where `name` = '{$name}' ".($id > 0 ? " and position_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Nombre de Posición Existe Actualmente";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Posición Guardada Exitósamente";
                else
                    $resp['msg'] = "Posición Actualizada Exitósamente";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Proceso de Nueva Posición Falló";
                else
                    $resp['msg'] = "Proceso de Actualización de Posición";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_position(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `position_list` where position_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Posición Eliminada Exitósamente';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_position(){
        extract($_POST);

        $update = $this->query("UPDATE position_list set status = '{$status}' where position_id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = 'Estado de Posición Actualizada Exitósamente';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Proceso de Actualización de Estado Falló';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_employee(){
        extract($_POST);
        $data = "";
        if(empty($id)){
            $_POST['password'] = md5($employee_code);
        }
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `employee_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `employee_list` set {$data} where employee_id = '{$id}'";
        }

        $check = $this->query("SELECT count(employee_id) as `count` FROM `employee_list` where `employee_code` = '{$employee_code}' ".($id > 0 ? " and employee_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Código de Empleado Existe Actualmente";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id)){
                    $resp['msg'] = "Empleado Guardado Exitósamente";
                    $eid = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
                }else{
                    $resp['msg'] = "Empleado Actualizado Exitósamente";
                    $eid = $id;
               } 
               $fname = __DIR__.'/avatars/'.$eid.'.png';
               if(isset($_FILES['avatar']['tmp_name']) && !empty($_FILES['avatar']['tmp_name'])){
                   $upload = $_FILES['avatar']['tmp_name'];
                   $type = mime_content_type($upload);
                   $allowed = array('image/png','image/jpeg');
                   if(!in_array($type,$allowed)){
                       $resp['msg'].=" La imagen no se pudo cargar debido a un tipo de extensión de archivo no válido.";
                   }else{
                       $gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                       if($gdImg){
                            if(is_file($fname))
                            unlink($fname);
                            imagepng($gdImg,$fname);
                            imagedestroy($gdImg);
                       }else{
                       $resp['msg'].=" La imagen no se pudo cargar debido a un motivo desconocido.";
                       }
                   }
               }
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Proceso de Generación de Nuevo Empleado Fallido";
                else
                    $resp['msg'] = "Proceso de Actualización de Empleado Fallido";
                    $resp['error']=$this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_employee(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `employee_list` where employee_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Empleado Eliminado Exitósamente';
            if(is_file(__DIR__.'/avatars/'.$id.'.png'))
            unlink(__DIR__.'/avatars/'.$id.'.png');
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_attendance(){
        extract($_POST);
        $jdata = json_decode($json_data);
        $json_data = $this->escapeString($json_data);
        $ip = isset($jdata->ip) ? $jdata->ip : '';
        $location = isset($jdata->loc) ? $jdata->loc : '';
        $dtype = $this->isMobileDevice() ? 'mobilde' :'desktop';
        $datetime = date("Y-m-d H:i");
        $sql = "INSERT INTO `attendance_list` (`employee_id`,`device_type`,`att_type`,`ip`,`location`,`json_data`,`date_created`) VALUES 
        ('{$_SESSION['employee_id']}','{$dtype}','{$type}','{$ip}','{$location}','{$json_data}','{$datetime}')
        ";
        $save = $this->query($sql);
        if($save){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
        }
        return json_encode($resp);
    }
    function delete_attendance(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `attendance_list` where attendance_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Registro de Asistencia Eliminado Exitósamente';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'e_login':
        echo $action->e_login();
    break;
    case 'e_logout':
        echo $action->e_logout();
    break;
    case 'e_update_credentials':
        echo $action->e_update_credentials();
    break;
    case 'save_admin':
        echo $action->save_admin();
    break;
    case 'delete_admin':
        echo $action->delete_admin();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_settings':
        echo $action->save_settings();
    break;
    case 'save_department':
        echo $action->save_department();
    break;
    case 'delete_department':
        echo $action->delete_department();
    break;
    case 'update_stat_dept':
        echo $action->update_stat_dept();
    break;
    case 'save_position':
        echo $action->save_position();
    break;
    case 'delete_position':
        echo $action->delete_position();
    break;
    case 'update_stat_position':
        echo $action->update_stat_position();
    break;
    case 'save_employee':
        echo $action->save_employee();
    break;
    case 'delete_employee':
        echo $action->delete_employee();
    break;
    case 'save_attendance':
        echo $action->save_attendance();
    break;
    case 'delete_attendance':
        echo $action->delete_attendance();
    break;
    default:
    // default action here
    break;
}