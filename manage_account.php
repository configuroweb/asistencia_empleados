<?php
require_once("./DBConnection.php");
$qry = $conn->query("SELECT * FROM `employee_list` where employee_id = '{$_SESSION['employee_id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
?>
<h3>Gestionar Cuenta</h3>
<hr>
<div class="col-md-6">
    <form action="" id="user-form">
        <input type="hidden" name="id" value="<?php echo isset($admin_id) ? $admin_id : '' ?>">
        <div class="form-group">
            <label for="firstname" class="control-label">Nombre</label>
            <input type="text" name="firstname" id="firstname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>">
        </div>
        <div class="form-group">
            <label for="middlename" class="control-label">Segundo Nombre</label>
            <input type="text" name="middlename" id="middlename" class="form-control form-control-sm rounded-0" placeholder="optional" value="<?php echo isset($middlename) ? $middlename : '' ?>">
        </div>
        <div class="form-group">
            <label for="lastname" class="control-label">Apellido</label>
            <input type="text" name="lastname" id="lastname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>">
        </div>
        <div class="form-group">
            <label for="email" class="control-label">Correo</label>
            <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
        </div>
        <div class="form-group">
            <label for="password" class="control-label">Nueva Contraseña</label>
            <input type="password" name="password" id="password" class="form-control form-control-sm rounded-0" value="">
        </div>
        <div class="form-group">
            <label for="old_password" class="control-label">Contraseña Anterior</label>
            <input type="password" name="old_password" id="old_password" class="form-control form-control-sm rounded-0" value="">
        </div>
        <div class="form-group">
            <small class="text-info">Deje el campo de la contraseña en blanco si no desea actualizar su contraseña.</small>
        </div>
        <div class="form-group d-flex w-100 justify-content-end">
            <button class="btn btn-sm btn-primary rounded-0 my-1">Actualizar</button>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#user-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=e_update_credentials',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("Ocurrió un Error")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                            location.reload()
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