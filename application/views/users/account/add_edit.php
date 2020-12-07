<?php $this->load->view('users/templates/header'); ?>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
		<div class="col-md-12 col-xs-12">
			<div class="box-content bordered default">
				<h4 class="box-title"><?php echo $heading; ?></h4>
				<?php $attributes = array('class' => 'form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open(USERURL.'/'.$_user_id.'/account/save',$attributes); ?>
               <input type="hidden" name="objectid" value="<?php echo (string)$_user_id; ?>">
               <div class="card-content">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label class="control-label" for="title">Title <span class="req">*</span></label>
                        <select name="title" class="form-control" id="title" required>
                           <option value="Mr." <?php echo (count($result)>0 && isset($result['title']) && $result['title']=='Mr.')?'selected':''; ?>>Mr.</option>
                           <option value="Mrs." <?php echo (count($result)>0 && isset($result['title']) && $result['title']=='Mrs.')?'selected':''; ?>>Mrs.</option>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="first_name">First Name <span class="req">*</span></label>
                        <input name="first_name" id="first_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['first_name']:''; ?>" required/>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                           <label class="control-label" for="last_name">Last Name <span class="req">*</span></label>
                           <input name="last_name" id="last_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['last_name']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="birth_name">Birth Name</label>
                           <input name="birth_name" id="birth_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['birth_name']:''; ?>"/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="dob">Date Of Birth <span class="req">*</span></label>
                           <input name="dob" id="dob" type="text" class="form-control date" value="<?php echo (count($result)>0 && $result['dob']!='')?date('d/m/Y', MongoEPOCH($result['dob'])):''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="email">Email <span class="req">*</span></label>
                           <input name="email" id="email" type="email" class="form-control" value="<?php echo (count($result)>0)?$result['email']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="phone_number">Cell Phone <span class="req">*</span></label>
                           <input name="phone_number" id="phone_number" type="number" class="form-control" value="<?php echo (count($result)>0)?$result['phone_number']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="home_phone">Home Phone <span class="req">*</span></label>
                           <input name="home_phone" id="home_phone" type="number" class="form-control" value="<?php echo (count($result)>0)?$result['home_phone']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="language">Language <span class="req">*</span></label>
                           <select name="language" class="form-control" id="language" required>
                              <?php foreach($languages as $code => $value){ ?>
                              <option value="<?php echo $code; ?>" <?php echo (count($result)>0 && isset($result['language']) && $result['language']==$code)?'selected':''; ?>><?php echo $value; ?></option>
                              <?php } ?>
                           </select>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                  </div>

                  <div class="col-sm-6">
                     <div class="form-group">
                        <label class="control-label" for="floor_no">Floor / Unit No. <span class="req">*</span></label>
                           <input name="floor_no" id="floor_no" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['floor_no']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="address_line_1">Address Line 1</label>
                           <input name="address_line_1" id="address_line_1" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['address_line_1']:''; ?>"/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="address_line_2">Address Line 2</label>
                           <input name="address_line_2" id="address_line_2" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['address_line_2']:''; ?>"/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="po_box">PO box</label>
                           <input name="po_box" id="po_box" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['po_box']:''; ?>"/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="zipcode">Zip code <span class="req">*</span></label>
                           <input name="zipcode" id="zipcode" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['zipcode']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="city">City <span class="req">*</span></label>
                           <input name="city" id="city" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['city']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="state">State <span class="req">*</span></label>
                           <input name="state" id="state" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['state']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="country">Country <span class="req">*</span></label>
                           <input name="country" id="country" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['country']:''; ?>" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="spoken_language">Spoken Language</label>
                           <select name="spoken_language" class="form-control" id="spoken_language">
                              <?php foreach($languages as $code => $value){ ?>
                              <option value="<?php echo $code; ?>" <?php echo (count($result)>0 && isset($result['spoken_language']) && $result['spoken_language']==$code)?'selected':''; ?>><?php echo $value; ?></option>
                              <?php } ?>
                           </select>
                           <div class="help-block with-errors"></div>
                     </div>

                     
                     <!--<div class="form-group">
                        <label class="control-label" for="edit_paper_invoice">Edit Invoice</label>
                        <div class="col-sm-9">
                           <div class="switch success margin-top-10">
                              <input type="checkbox" name="edit_paper_invoice" id="edit_paper_invoice" <?php echo (count($result)>0 && $result['edit_paper_invoice']=='1')?'checked':''; ?> />
                              <label for="edit_paper_invoice">&nbsp;</label>
                           </div>
                        </div>
                     </div>-->
                  </div>
                  
                  <div class="col-sm-12 text-center">
                     <input type="submit" value="Submit" class="btn btn-sm btn-success"/>
                  </div>
               </div>
				</form>	
			</div>
		</div>
    <?php $this->load->view('users/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>