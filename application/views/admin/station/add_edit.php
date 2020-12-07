<?php $this->load->view('admin/templates/header'); ?>
<style>
.select2-container .select2-search--inline .select2-search__field{margin-top:7px;}
.select2-container--default .select2-search--inline .select2-search__field{padding: 0 10px;}
</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/station/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Station Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
                  
						<div class="form-group">
							<label class="col-sm-3 control-label" for="street">Street <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="street" id="street" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['street']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
                  
						<div class="form-group">
							<label class="col-sm-3 control-label" for="zipcode">Zipcode <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="zipcode" id="zipcode" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['zipcode']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
                  
						<div class="form-group">
							<label class="col-sm-3 control-label" for="city">City <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="city" id="city" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['city']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
                  
                  <div class="form-group">
                     <label class="col-sm-3 control-label" for="vehicles">Vehicles <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <select name="vehicles[]" id="vehicles" class="form-control select2-multiple" multiple="multiple" required>
                           <?php foreach($vehicles->result() as $vehicle){ ?>
                              <option value="<?php echo (string) $vehicle->_id; ?>" <?php echo (count($result)>0 && isset($result['vehicles']) && count($result['vehicles']) && in_array((string) $vehicle->_id, $result['vehicles']))?'selected="selected"':''; ?>><?php echo $makes[$vehicle->make_id].' '.$models[$vehicle->model_id].' '.$vehicle->year.' - '.$vehicle->vin_number; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-3 control-label" for="partner">Partner <span class="req">*</span></label>
                     <div class="col-sm-9">
                        <select name="partner" id="partner" class="form-control" required>
                           <option value="">Select a partner</option>
                           <?php foreach($partners->result() as $partner){ ?>
                              <option value="<?php echo (string) $partner->_id; ?>" <?php echo (count($result)>0 && (string) $partner->_id == $result['partner'])?'selected="selected"':''; ?>><?php echo $partner->name; ?></option>
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
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
    <script>
      $(document).ready(function(){
         $('.select2-multiple').select2({
            placeholder: "Select vehicles",
            maximumSelectionLength: "<?php echo (count($result)>0)?$result['parkings']:'1'; ?>",
            language: {
               maximumSelected: function (e) {
                  var t = "Vehicles must not exceed the no.of parkings";
                  return t;
               }
            },
            allowClear: false
         });
         
         $('select#parkings').on('change', function(){
            $('select#vehicles').val('');
            $('.select2-multiple').select2('destroy').select2({
               placeholder: "Select vehicles",
               maximumSelectionLength: $(this).val(),
               language: {
                  maximumSelected: function (e) {
                     var t = "Vehicles must not exceed the no.of parkings";
                     return t;
                  }
               },
               allowClear: false
            });
         });
      });
    </script>
  </div>
</div>