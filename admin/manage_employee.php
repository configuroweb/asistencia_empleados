<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `employee_list` where employee_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #logo-img{
        width:75px;
        height:75px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="container-fluid">
    <form action="" id="enrollee-form">
        <input type="hidden" name="id" value="<?php echo isset($employee_id) ? $employee_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employee_code" class="control-label">Código de Empleado</label>
                        <input type="text" name="employee_code" autofocus id="employee_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($employee_code) ? $employee_code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="control-label">Nombre</label>
                        <input type="text" name="firstname" id="firstname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="middlename" class="control-label">Segundo Nombre</label>
                        <input type="text" name="middlename" id="middlename" required class="form-control form-control-sm rounded-0" placeholder="(optional)" value="<?php echo isset($middlename) ? $middlename : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label">Apellido</label>
                        <input type="text" name="lastname" id="lastname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender" class="control-label">Género</label>
                        <select name="gender" id="gender" class="form-select form-select-sm rounded-0">
                            <option value="Male" <?php echo (isset($gender) && $gender == "Male" ) ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?php echo (isset($gender) && $gender == "Female" ) ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Correo</label>
                        <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contacto</label>
                        <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($contact) ? $contact : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address" class="control-label">Dirección</label>
                        <textarea name="address" id="address" cols="30" rows="3" style="resize:none" class="form-control form-control-sm rounded-0" required><?php echo isset($address) ? $address : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="department_id" class="control-label">Departmento</label>
                        <select name="department_id" id="department_id" class="form-select form-select-sm select2 rounded-0">
                            <option <?php echo (!isset($department_id)) ? 'selected' : '' ?> disabled>Please Select department</option>
                            <?php
                            $department = $conn->query("SELECT * FROM department_list where `status` = 1 ".(isset($department_id) ? " or department_id ='{$department_id}'" : "")." order by `name` asc");
                            while($row= $department->fetchArray()):
                            ?>
                                <option value="<?php echo $row['department_id'] ?>" <?php echo (isset($department_id) && $department_id == $row['department_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_id" class="control-label">Posición</label>
                        <select name="position_id" id="position_id" class="form-select form-select-sm select2 rounded-0">
                            <option <?php echo (!isset($position_id)) ? 'selected' : '' ?> disabled>Selecciona Posición</option>
                            <?php
                            $position = $conn->query("SELECT * FROM position_list where `status` = 1 ".(isset($position_id) ? " or position_id ='{$position_id}'" : "")." order by `name` asc");
                            while($row= $position->fetchArray()):
                            ?>
                                <option value="<?php echo $row['position_id'] ?>" <?php echo (isset($position_id) && $position_id == $row['position_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="avatar" class="control-label">Imagen</label>
                        <input type="file" name="avatar" id="avatar" required class="form-control form-control-sm rounded-0" accept="image/png,image/jpeg" <?php echo !isset($employee_id) ? 'required' : '' ?> onchange="display_img(this)">
                    </div>
                    <div class="form-group text-center mt-2">
                        <img src="<?php echo isset($employee_id) && is_file('./../avatars/'.$employee_id.'.png') ? './../avatars/'.$employee_id.'.png' : './../images/no-image-available.png' ?>" id="logo-img" alt="Avatar">
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Estado</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0">
                            <option value="1" <?php echo (isset($status) && $status == 1 ) ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?php echo (isset($status) && $status == 0 ) ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function display_img(input){
        if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#logo-img').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
    }
    $(function(){
        $('#enrollee-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_employee',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("Ocurrió un error")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($employee_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>