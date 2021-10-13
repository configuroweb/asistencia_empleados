<?php 
require_once('./../DBConnection.php');
$log_type = array('IN'=>'Hora de Ingreso','L_OUT'=>'Salida de Descanso','L_IN'=>'Ingreso a Descanso','OUT'=>'Hora de Salida');
if(isset($_GET['id'])){
    $sql = "SELECT a.*,(e.lastname || ', ' || e.firstname || ' ' || e.middlename) as name,e.employee_code,d.name as dept,p.name as pos FROM attendance_list a inner join employee_list e on a.employee_id = e.employee_id inner join department_list d on e.department_id = d.department_id inner join position_list p on e.position_id = p.position_id where attendance_id = '{$_GET['id']}'";
    $qry = $conn->query($sql)->fetchArray();
    if($qry){
        foreach($qry as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}

?>
<style>
    #uni_modal .modal-footer{
        display:none !important
    }
    #map-holder{
        width:calc(100%);
        height:0;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <dl>
                    <dt class="text-muted">Fecha de Registro Hora</dt>
                    <dd class="ps-4"><?php echo date("Y-m-d h:i A",strtotime($date_created)) ?></dd>
                    <dt class="text-muted">Empleado</dt>
                    <dd class="ps-4">
                        <p>
                            Código de Empleado: <b><?php echo $employee_code ?></b><br>
                            Departmento: <b><?php echo $dept ?></b><br>
                            Posición: <b><?php echo $pos ?></b><br>
                            Nombre: <b><?php echo $name ?></b><br>
                        </p>
                    </dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl>
                    <dt class="text-muted">Tipo de Registro</dt>
                    <dd class="ps-4"><?php echo $log_type[$att_type] ?></dd>
                    <dt class="text-muted">Dispositivo Utilizado</dt>
                    <dd class="ps-4">
                            <?php if($device_type == 'desktop'): ?>
                                <span><span class="fa fa-desktop text-primary"></span> Equipo de Escritorio</span>
                            <?php else: ?>
                                <span><span class="fa fa-mobile-alt text-primary"></span> Teléfono Móvil</span>
                            <?php endif; ?>
                    </dd>
                    <dt class="text-muted">IP</dt>
                    <dd class="ps-4"><?php echo $ip ?></dd>
                </dl>
            </div>
            <div class="col-md-12">
                <div class="fs-6">Ubicación de la IP</div>
                <iframe id="map-holder" src="https://maps.google.com/maps?output=embed&q=<?php echo $location ?>" frameborder="1"></iframe>
                <div class="w-100 d-flex justify-content-center" id="map-loader">
                    <div class="text-center">
                        <div class="spinner-grow" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <center>Cargando Mapa</center>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mx-0">
            <div class="col-12">
                <div class="row justify-content-end mt-3">
                    <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#map-holder').on('load',function(){
        $('#map-holder').css('height',"40vh")
        $('#map-loader').addClass('d-none')
    })
</script>
