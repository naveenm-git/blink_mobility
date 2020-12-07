<?php $this->load->view('users/templates/header'); ?>
<style>
   .photo_of_yourself, .proof_of_qualification{
      display: none;
   }
</style>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
            <div class="box-content bordered default">
               <h4 class="box-title"><?php echo $heading; ?></h4>
               <?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open(USERURL.'/'.$_user_id.'/documents/save',$attributes); ?>
               <div class="form-group">
                  <label class="col-sm-3 control-label">Document Type <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <select name="document_type" class="form-control" id="document_type" required>
                        <option value="license">License</option>
                        <option value="photo_of_yourself">Photo Of Yourself</option>
                        <option value="proof_of_qualification">Proof Of Qualification</option>
                     </select>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group license">
                  <label class="col-sm-3 control-label">License Front <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input class="file-field" type="file" id="license_front" name="license_front" accept="image/*" style="display:none;" required>
                     <label for="license_front" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload license front</label>
                     <img id="img-preview" src="<?php echo base_url('assets/images/no-img-banner.png');?>" width="100px" class="files"/>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group license">
                  <label class="col-sm-3 control-label">License Back <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input class="file-field" type="file" id="license_back" name="license_back" accept="image/*" style="display:none;" required>
                     <label for="license_back" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload license back</label>
                     <img id="img-preview" src="<?php echo base_url('assets/images/no-img-banner.png');?>" width="100px" class="files"/>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group photo_of_yourself">
                  <label class="col-sm-3 control-label">Photo Of Yourself <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input class="file-field" type="file" id="photo_of_yourself" name="photo_of_yourself" accept="image/*" style="display:none;">
                     <label for="photo_of_yourself" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload license back</label>
                     <img id="img-preview" src="<?php echo base_url('assets/images/no-img-banner.png');?>" width="100px" class="files"/>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
               
               <div class="form-group proof_of_qualification">
                  <label class="col-sm-3 control-label">Proof Of Qualification <span class="req">*</span></label>
                  <div class="col-sm-9">
                     <input class="file-field" type="file" id="proof_of_qualification" name="proof_of_qualification" accept="image/*" style="display:none;">
                     <label for="proof_of_qualification" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload license back</label>
                     <img id="img-preview" src="<?php echo base_url('assets/images/no-img-banner.png');?>" width="100px" class="files"/>
                     <div class="help-block with-errors"></div>
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
      <?php $this->load->view('users/templates/footer'); ?>
      <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script>
         $(document).ready(function(){
            $('#document_type').change(function(){
               thisval = $(this).val();
               $('.'+thisval).show();
               if(thisval == 'license'){
                  $('#license_front, #license_back').prop('required', true);
                  $('#photo_of_yourself, #proof_of_qualification').prop('required', false);
                  $('.photo_of_yourself, .proof_of_qualification').hide();
               }
               if(thisval == 'photo_of_yourself'){
                  $('#photo_of_yourself').prop('required', true);
                  $('#license_front, #license_back, #proof_of_qualification').prop('required', false);
                  $('.license, .proof_of_qualification').hide();
               }
               if(thisval == 'proof_of_qualification'){
                  $('#proof_of_qualification').prop('required', true);
                  $('#photo_of_yourself, #license_front, #license_back').prop('required', false);
                  $('.license, .photo_of_yourself').hide();
               }
            });
         });
      </script>
   </div>
</div>