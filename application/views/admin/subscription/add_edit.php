<?php $this->load->view('admin/templates/header'); ?>
<style>
   span.prepend-input{
   position: absolute;
   top: 30px;
   left: 15px;
   padding: 8px 14px;
   height: 35px;
   font-weight: 500;
   border-right: 1px solid #ccd1d9;
   }
   input.prepend-input {
   padding-left: 45px;
   }
   div.has-error > span.prepend-input {
   border-right: 1px solid #ea4335;
   }
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
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label" for="name">Plan Name <span class="req">*</span></label>
                        <input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label">Fees <span class="req">*</span></label>
                        <input name="fees" id="fees" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['fees']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label">Membership Period <span class="req">*</span></label>
                        <input name="validity[count]" id="periodCount" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['validity']['count']))?$result['validity']['count']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label">&nbsp;</label>
                        <select name="validity[interval]" id="periodInterval" class="form-control" required>
                           <option value="">Select Interval</option>
                           <?php foreach($validityInterval as $k => $v){ ?>
                           <option value="<?php echo (string) $k; ?>" <?php echo (count($result)>0 && $k==$result['validity']['interval'])?'selected':''; ?>><?php echo $v; ?></option>
                           <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
                  <h4 class="box-title">Minimum Charges</h4>
                  
                  <div class="row">
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label">Minimum Rental Period <span class="req">*</span></label>
                        <input name="minimum_rental_period[count]" id="minimum_count" type="number" class="form-control" value="<?php echo (count($result)>0 && isset($result['minimum_rental_period']['count']))?$result['minimum_rental_period']['count']:''; ?>" onchange="change_label()" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label">&nbsp;</label>
                        <select name="minimum_rental_period[interval]" id="minimum_interval" class="form-control" onchange="change_label()" required>
                           <option value="">Select Interval</option>
                           <?php foreach($validityInterval as $k => $v){ ?>
                           <option value="<?php echo (string) $k; ?>" <?php echo (count($result)>0 && $k==$result['minimum_rental_period']['interval'])?'selected':''; ?>><?php echo $v; ?></option>
                           <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label initial-charge"><?php echo (!$form_mode)?'Initial Charge':'Initial '.$result['minimum_rental_period']['count'].' '.$validityInterval[$result['minimum_rental_period']['interval']].' Charge'; ?> <span class="req">*</span></label>
                        <span class="prepend-input">$</span>
                        <input name="initial_charge" id="initial_charge" type="number" step="0.01" pattern="\d*" class="form-control prepend-input" value="<?php echo (count($result)>0 && isset($result['initial_charge']))?$result['initial_charge']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     <div class="col-sm-6 col-md-3 form-group">
                        <label class="control-label after-charge"><?php echo (!$form_mode)?'After Charge':'After '.$result['minimum_rental_period']['count'].' '.$validityInterval[$result['minimum_rental_period']['interval']].' Charge <small>(per min)</small>'; ?> <span class="req">*</span></label>
                        <span class="prepend-input">$</span>
                        <input name="after_charge" id="after_charge" type="number" step="0.01" pattern="\d*" class="form-control prepend-input" value="<?php echo (count($result)>0 && isset($result['after_charge']))?$result['after_charge']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
                  <div class="row">
                     <div class="col-sm-12 form-group">
                        <label class="control-label">Terms</label>
                        <div class="terms-div">
                           <?php if(count($result)>0 && isset($result['terms']) && count($result['terms']) > 0){ ?>
                           <?php foreach($result['terms'] as $i => $answer){ ?>
                           <div class="input-group <?php echo ($i>0)?'margin-top-15':''; ?>">
                              <input type="text" name="terms[]" class="form-control terms" id="terms" value="<?php echo $answer; ?>"/>
                              <?php if($i==0){ ?>
                              <span class="input-group-addon btn btn-xs btn-black" onclick="add_terms();"><i class="fa fa-plus"></i></span>
                              <?php } else { ?>
                              <span class="input-group-addon btn btn-xs btn-danger" onclick="remove_terms(this);"><i class="fa fa-minus text-white"></i></span>
                              <?php } ?>
                           </div>
                           <div class="help-block with-errors"></div>
                           <?php } ?>
                           <?php } else { ?>
                           <div class="input-group">
                              <input type="text" name="terms[]" class="form-control terms" id="terms"/>
                              <span class="input-group-addon btn btn-xs btn-black" onclick="add_terms();"><i class="fa fa-plus"></i></span>
                           </div>
                           <div class="help-block with-errors"></div>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row">
                     <div class="col-sm-6 form-group">
                        <label class="control-label" for="proof_of_qualification">Require Proof of Qualification ?</label>
                        <div class="switch success margin-top-10">
                           <input type="checkbox" name="proof_of_qualification" id="proof_of_qualification" <?php echo (count($result)>0 && $result['proof_of_qualification']=='1')?'checked':''; ?> />
                           <label for="proof_of_qualification">&nbsp;</label>
                        </div>
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
      
      <?php $this->load->view('admin/templates/footer'); ?>
      <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script>
         var intervals = JSON.parse(['<?php echo json_encode($validityInterval); ?>']);
         function add_terms(){
         html = '<div class="input-group margin-top-15">'+
                  '<input type="text" name="terms[]" class="form-control terms" required/>'+
                  '<span class="input-group-addon btn btn-xs btn-danger" onclick="remove_terms(this)"><i class="fa fa-minus text-white"></i></span>'+
               '</div>'+
            '<div class="help-block with-errors"></div>';
            $('.terms-div').append(html);
            $("form#subscription-form").validator('update');
         }
         
         function remove_terms(current){
            $(current).parent('div.input-group').remove();
            $("form#subscription-form").validator('update');
         }
         
         function change_label(){
            initial = 'Initial Charge';
            after = 'After Charge';
            minimum_count = $('#minimum_count').val();
            minimum_interval = $('#minimum_interval').val();
            if(minimum_count != '' && minimum_interval != ''){
               initial = 'Initial '+ minimum_count + ' ' + intervals[minimum_interval] + ' Charge  <span class="req">*</span>';
               after = 'After '+ minimum_count + ' '+ intervals[minimum_interval] + ' Charge <small>(per min)</small>  <span class="req">*</span>';
            }
            $('.initial-charge').html(initial);
            $('.after-charge').html(after);
            console.log(intervals)
         }
      </script>
   </div>
</div>