<?php $this->load->view('station/templates/header'); ?>
<style>
   .photo_of_yourself, .proof_of_qualification{
      display: none;
   }
   .select2-selection__clear {
      display: block !important;
      font-size: 15px;
   }
   .select2-container--default .select2-selection--single .select2-selection__arrow{
      top: -1px !important;
   }
</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
            <div class="box-content bordered default">
               <h4 class="box-title"><?php echo $heading; ?></h4>
               <?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open(STATIONURL.'/'.$_station_id.'/parking/save',$attributes); ?>
               <input type="hidden" name="objectid" value="<?php echo (count($result)>0)?(string)$result['_id']:''; ?>" />
               <input type="hidden" name="station_id" value="<?php echo $_station_id; ?>">
               <div class="form-group">
                  <label class="col-sm-3 control-label" for="name">Title <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-sm-3 control-label" for="order">Parking Order <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input name="order" id="order" type="number" class="form-control" value="<?php echo (count($result)>0)?$result['order']:''; ?>" required/>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group">
                  <label class="col-sm-3 control-label">Parking Type <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="parking_type" class="form-control" id="parking_type" required>
                        <option value="">Select parking type</option>
                        <?php foreach($parking_type as $key => $value){ ?>
                        <option value="<?php echo $key; ?>" <?php echo (count($result)>0)?(($result['parking_type']==$key)?'selected':''):''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                     </select>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-sm-3 control-label">Parking Status <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="parking_status" class="form-control" id="parking_status" required>
                        <option value="">Select parking status</option>
                        <?php foreach($parking_status as $key => $value){ ?>
                        <option value="<?php echo $key; ?>" <?php echo (count($result)>0)?(($result['parking_status']==$key)?'selected':''):''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                     </select>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-sm-3 control-label">Cable Status <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="cable_status" class="form-control" id="cable_status" required>
                        <option value="">Select cable status</option>
                        <?php foreach($cable_status as $key => $value){ ?>
                        <option value="<?php echo $key; ?>" <?php echo (count($result)>0)?(($result['cable_status']==$key)?'selected':''):''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                     </select>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>

               <div class="form-group">
                  <label class="col-sm-3 control-label">Light Color <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="light_color" class="form-control" id="light_color" required>
                        <option value="">Select light color</option>
                        <?php foreach($light_color as $key => $value){ ?>
                        <option value="<?php echo $key; ?>" <?php echo (count($result)>0)?(($result['light_color']==$key)?'selected':''):''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                     </select>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>

               <div class="form-group vehicle-div" <?php echo (count($result)>0 && $result['parking_type']=='reservation')?'style="display:none;"':''; ?>>
                  <label class="col-sm-3 control-label">Vehicle <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="vehicle_id" class="form-control select-vehicle" id="vehicle_id" <?php echo (count($result)>0 && $result['parking_type']!='reservation')?'required':''; ?>>
                        <option></option>
                        <?php foreach($vehicles->result() as $vehicle){ ?>
                        <option value="<?php echo (string) $vehicle->_id; ?>" <?php echo (count($result)>0)?(($result['vehicle_id']==(string)$vehicle->_id)?'selected':''):''; ?>><?php echo $vehicle->license_plate.' / '.$vehicle->vin_number; ?></option>
                        <?php } ?>
                     </select>
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
      <?php $this->load->view('station/templates/footer'); ?>
      <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script>
         $(document).ready(function(){
            $('.select-vehicle').select2({
               placeholder: "Select a vehicle",
               allowClear: false
            });
            
            $('#parking_type').change(function(){
               type = $(this).val();
               if(type != ''){
                  if(type=='reservation'){
                     $('.vehicle-div').hide();
                     $('#vehicle_id').prop('required', false);
                  } else {
                     $('.vehicle-div').show();
                     $('#vehicle_id').prop('required', true);
                  }
               } else {
                  $('.vehicle-div').show();
                  $('#vehicle_id').prop('required', true);
               }
            });
         });
      </script>
   </div>
</div>