<?php $this->load->view('admin/templates/header'); ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
		<div class="col-md-12 col-xs-12">
			<div class="box-content bordered default">
				<h4 class="box-title"><?php echo $heading; ?></h4>
				<?php $attributes = array('class' => 'form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/users/save',$attributes); ?>
               <input type="hidden" name="objectid" value="">
               <div class="card-content">
                  <div class="col-sm-6">
                     <div class="form-group">
                        <label class="control-label" for="title">Title <span class="req">*</span></label>
                        <select name="title" class="form-control" id="title" required>
                           <option value="Mr.">Mr.</option>
                           <option value="Mrs.">Mrs.</option>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="first_name">First Name <span class="req">*</span></label>
                        <input name="first_name" id="first_name" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                           <label class="control-label" for="last_name">Last Name <span class="req">*</span></label>
                           <input name="last_name" id="last_name" type="text" class="form-control" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="birth_name">Birth Name</label>
                           <input name="birth_name" id="birth_name" type="text" class="form-control" />
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="dob">Date Of Birth <span class="req">*</span></label>
                           <input name="dob" id="dob" type="text" class="form-control date" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="email">Email <span class="req">*</span></label>
                           <input name="email" id="email" type="email" class="form-control" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="phone_number">Cell Phone <span class="req">*</span></label>
                           <input name="phone_number" id="phone_number" type="number" class="form-control" required/>
                           <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="home_phone">Home Phone <span class="req">*</span></label>
                           <input name="home_phone" id="home_phone" type="number" class="form-control" required/>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="language">Language <span class="req">*</span></label>
                           <select name="language" class="form-control" id="language" required>
                              <?php foreach($languages as $code => $value){ ?>
                              <option value="<?php echo $code; ?>"><?php echo $value; ?></option>
                              <?php } ?>
                           </select>
                           <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="spoken_language">Spoken Language</label>
                        <select name="spoken_language" class="form-control" id="spoken_language">
                           <?php foreach($languages as $code => $value){ ?>
                           <option value="<?php echo $code; ?>"><?php echo $value; ?></option>
                           <?php } ?>
                        </select>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                  </div>

                  <div class="col-sm-6">
                     <div class="form-group">
                        <label class="control-label" for="floor_no">Floor / Unit No. <span class="req">*</span></label>
                        <input name="floor_no" id="floor_no" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="address_line_1">Address Line 1</label>
                        <input name="address_line_1" id="address_line_1" type="text" class="form-control" />
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="address_line_2">Address Line 2</label>
                        <input name="address_line_2" id="address_line_2" type="text" class="form-control" />
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="po_box">PO box</label>
                        <input name="po_box" id="po_box" type="text" class="form-control" />
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="zipcode">Zip code <span class="req">*</span></label>
                        <input name="zipcode" id="zipcode" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="city">City <span class="req">*</span></label>
                        <input name="city" id="city" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="state">State <span class="req">*</span></label>
                        <input name="state" id="state" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>

                     <div class="form-group">
                        <label class="control-label" for="country">Country <span class="req">*</span></label>
                        <input name="country" id="country" type="text" class="form-control" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="password">Password <span class="req">*</span></label>
                        <input name="password" data-minlength="6" id="password" type="password" class="form-control" title="Please enter new password" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                     
                     <div class="form-group">
                        <label class="control-label" for="confirm_password">Confirm Password <span class="req">*</span></label>
                        <input id="confirm_password" type="password" data-error="Please enter confirm password" data-match="#password" class="form-control" data-match-error="Password doesn't match" title="Please enter confirm password" required/>
                        <div class="help-block with-errors"></div>
                     </div>
                  </div>
                  
                  <div class="col-sm-12 text-center">
                     <input type="submit" value="Submit" class="btn btn-sm btn-success"/>
                  </div>
               </div>
				</form>	
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>