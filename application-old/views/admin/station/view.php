<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Station Name</label>
                     <p class="detail"><?php echo $result['name']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Street</label>
                     <p class="detail"><?php echo $result['street']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Zipcode</label>
                     <p class="detail"><?php echo $result['zipcode']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">City</label>
                     <p class="detail"><?php echo $result['city']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Parkings</label>
                     <p class="detail"><?php echo $result['parkings'].' '.(($result['parkings']==1)?'Parking':'Parkings'); ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Vehicles</label>
                     <?php $vehicleIds = array_map(function($a){ return objectid($a); }, $result['vehicles']); ?>
                     <?php $vehicles = getresult(VEHICLE, ['_id'=>['$in'=>$vehicleIds]]); ?>
                     <p class="detail"><?php 
                        $vehicleName = [];
                        foreach($vehicles->result() as $vehicle){
                           $make = getrow(MAKE, $vehicle->make_id)->make;
                           $model = getrow(MODEL, $vehicle->model_id)->model;
                           $vehicleName[] = $make.' '.$model.' '.$vehicle->year;
                        }
                        echo @implode(' / ', $vehicleName);
                     ?></p>
                  </div>
               </div>
															
               <div class="row">
                  <div class="col-sm-12 form-group">
                     <a href="<?php echo base_url('admin/users-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>