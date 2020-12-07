<?php $this->load->view('users/templates/header'); ?>
<style>

</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-swal-submit', 'id'=>'subscription-form', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/subscription/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
                  <div class="row">
                     <div class="col-sm-6 form-group">
                        <label class="control-label" for="name">Plan Name <span class="req">*</span></label>
                        <input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="col-sm-6 form-group">
                        <label class="control-label">Membership Fees <span class="req">*</span></label>
                        <input name="fees" id="fees" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['fees']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
						</div>
						
                  <div class="row">
                     <div class="col-sm-3 form-group">
                        <label class="control-label">Membership Type <span class="req">*</span></label>
                        <input name="validity[count]" id="periodCount" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['validity']['count']))?$result['validity']['count']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="col-sm-3 form-group">
                        <label class="control-label">&nbsp;</label>
                        <select name="validity[interval]" id="periodInterval" class="form-control" required>
                           <option value="">Select Interval</option>
                           <?php foreach($validityInterval as $k => $v){ ?>
                              <option value="<?php echo (string) $k; ?>" <?php echo (count($result)>0 && $k==$result['validity']['interval'])?'selected="selected"':''; ?>><?php echo $v; ?></option>
                           <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="col-sm-6 form-group">
                        <label class="control-label">Airport Surcharge <span class="req">*</span></label>
                        <input name="airport_surcharge" id="airport_surcharge" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['airport_surcharge']))?$result['airport_surcharge']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
						</div>
						
                  <div class="row">
                     <div class="col-sm-6 form-group">
                        <label class="control-label">Initial 20 Mins Charge <span class="req">*</span></label>
                        <input name="initial_charge" id="initial_charge" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['initial_charge']))?$result['initial_charge']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="col-sm-6 form-group">
                        <label class="control-label">After 20 Mins (per minute charge) <span class="req">*</span></label>
                        <input name="after_charge" id="after_charge" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['after_charge']))?$result['after_charge']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
						</div>
						
						<div class="row">
                     <div class="col-sm-6 form-group">
                        <label class="control-label" for="android_link">&nbsp;</label>
                        <input type="submit" value="Submit" class="btn btn-sm btn-success"/>
                     </div>
						</div>
					</form>	
				</div>
			</div>
		</div>
    <?php $this->load->view('users/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
		<script>
			function add_others(){
				html = '<div class="input-group margin-top-15">'+
					'<input type="text" name="others[]" class="form-control others" onkeydown="make_first_caps(this, event)" required/>'+
					'<span class="input-group-addon btn btn-xs btn-danger" onclick="remove_others(this)"><i class="fa fa-minus text-white"></i></span>'+
				'</div>';
				$('.others-div').append(html);
				$("form#subscription-form").validator('update');
			}
			
			function remove_others(current){
				$(current).parent('div.input-group').remove();
				$("form#subscription-form").validator('update');
			}
		</script>
  </div>
</div>