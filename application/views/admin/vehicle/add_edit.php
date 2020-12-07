<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/vehicle/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
                  <div class="form-group">
                     <label class="col-sm-3 control-label">Make <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <select name="make_id" id="make_id" class="form-control select2" required>
                           <option value="">Select Make</option>
                           <?php foreach($makes->result() as $res){ ?>
                              <option value="<?php echo (string) $res->_id; ?>" <?php echo (count($result)>0 && (string) $res->_id==$result['make_id'])?'selected="selected"':''; ?>><?php echo $res->make; ?></option>
                           <?php } ?>
                        </select>
								<div class="help-block with-errors"></div>
                     </div>
                  </div>
						
                  <div class="form-group">
                     <label class="col-sm-3 control-label">Model <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <select name="model_id" id="model_id" class="form-control select2" required>
                           <option value="">Select Model</option>
                           <?php 
                              if(count($result)>0 && $result['make_id']!=''){ 
                                 $models = getresult(MODEL, ['make_id' => $result['make_id']]);
                                 if($models->num_rows() > 0){
                                    foreach($models->result() as $model){
                                       echo '<option value="'.(string) $model->_id.'" '.(((string) $model->_id==$result['model_id'])?'selected="selected"':'').'>'.$model->model.'</option>';
                                    }
                                 }
                              }
                           ?>
                        </select>
								<div class="help-block with-errors"></div>
                     </div>
                  </div>
                  	
						<div class="form-group">
							<label class="col-sm-3 control-label">Year <span class="req">*</span></label>
							<div class="col-sm-9">
                        <select name="year" id="year" class="form-control select2" required>
                           <option value="">Select year</option>
                           <?php foreach($years as $year){ ?>
                              <option value="<?php echo $year; ?>" <?php echo (count($result)>0)?(($result['year']==(string)$year)?'selected':''):''; ?>><?php echo $year; ?></option>
                           <?php } ?>
                        </select>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Mileage <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="mileage" id="mileage" type="text" pattern="\d*" maxlength="3" data-error="Please enter a valid number" class="form-control" value="<?php echo (count($result)>0)?$result['mileage']:''; ?>" required/>
                        <span class="note">** in miles with 100% charge</span>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Car identifier <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="identifier" id="identifier" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['identifier']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">VIN Number <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="vin_number" id="vin_number" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['vin_number']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">License Plate <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="license_plate" id="license_plate" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['license_plate']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Microlise box <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="microlise_box" id="microlise_box" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['microlise_box']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">In-Vehicle Infotainment Computer <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="infotainment_computer" id="infotainment_computer" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['infotainment_computer']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Seats <span class="req">*</span></label>
							<div class="col-sm-9">
                        <select name="seats" id="seats" class="form-control" required>
                           <option value="">Select no.of seat</option>
                           <?php for($seat=3;$seat<=8;$seat++){ ?>
                              <option value="<?php echo $seat; ?>" <?php echo (count($result)>0)?(($result['seats']==(string)$seat)?'selected':''):''; ?>><?php echo $seat.' Seater'; ?></option>
                           <?php } ?>
                        </select>
								<div class="help-block with-errors"></div>
							</div>
						</div>

                  <div class="form-group">
                     <label class="col-sm-3 control-label">Car Status <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <select name="car_status" class="form-control" id="car_status" required>
                           <option value="">Select car status</option>
                           <?php foreach($car_status as $key => $value){ ?>
                           <option value="<?php echo $key; ?>" <?php echo (count($result)>0)?(($result['car_status']==$key)?'selected':''):''; ?>><?php echo $value; ?></option>
                           <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-3 control-label">Car Icon <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <input class="file-field" type="file" id="car_icon" name="car_icon" accept="image/*" style="display:none;" required>
                        <label for="car_icon" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload image</label>
                        <?php if(count($result)>0 && isset($result['car_icon']) && $result['car_icon']!=''){ ?>
                           <img id="img-preview" src="<?php echo base_url('uploads/vehicles/'.$result['car_icon']);?>" width="100px" class="files"/>
                        <?php } else {?>
                           <img id="img-preview" src="<?php echo base_url('assets/images/default-car-icon.png');?>" width="100px" class="files"/>
                        <?php } ?>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
						<div class="form-group">
							<label class="col-sm-3 control-label">Status</label>
							<div class="col-sm-9">
								<div class="switch success margin-top-10">
									<input type="checkbox" name="status" id="status" <?php echo (count($result)>0 && $result['status']=='1')?'checked':''; ?> />
									<label for="status">&nbsp;</label>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
							<div class="col-sm-9">
								<input type="submit" value="Submit" class="btn btn-sm btn-success"/>
							</div>
						</div>
					</form>	
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
    <script>
     
      
      $(document).ready(function(){
         $('#make_id').change(function(){
            change_make($(this).val());
         });
      });
      
      function change_make(make_id){
         $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/get_maker_models',
            type: 'POST',
            data: {make_id: make_id},
            success: function(d){
               d = JSON.parse(d);
               if(d.status=='1'){
                  option = '<option value="">Select Model</option>';
                  $.each(d.response, function(i, v){
                     option += '<option value="'+v._id+'">'+v.model+'</option>';
                  });
                  $('select#model_id').empty().append(option);
                  $('select#model_id .select2').select2('destroy').select2();
               } else {
                  
               }
            }
         });
      }
    </script>
  </div>
</div>