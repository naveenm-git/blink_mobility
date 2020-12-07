<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/users/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Customer ID <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="customer_id" id="customer_id" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['customer_id']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">First Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="first_name" id="first_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['first_name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Last Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="last_name" id="last_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['last_name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">User Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="user_name" id="user_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['user_name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Date Of Birth <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="dob" id="dob" type="text" class="form-control date" value="<?php echo (count($result)>0 && $result['dob']!='')?date('d/m/Y', MongoEPOCH($result['dob'])):''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Email <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="email" id="email" type="email" class="form-control" value="<?php echo (count($result)>0)?$result['email']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="mobile">Mobile Line <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="mobile" id="mobile" type="number" class="form-control" value="<?php echo (count($result)>0)?$result['mobile']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Civility <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="civility" id="civility" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['civility']:''; ?>" required/>
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
							<label class="col-sm-3 control-label" for="building">Building</label>
							<div class="col-sm-9">
								<input name="building" id="building" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['building']:''; ?>"/>
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
							<label class="col-sm-3 control-label" for="name">Subscription ID <span class="req">*</span></label>
							<div class="col-sm-9">
                        <select name="subscription_id" id="subscription_id" class="form-control" required>
                           <option value="">Select a Plan</option>
                           <?php if($subscription->num_rows() > 0){ ?>
                           <?php foreach($subscription->result() as $subs){ ?>
                              <option value="<?php echo (string) $subs->_id; ?>" <?php echo (count($result)>0)?(($result['subscription_id']==(string)$subs->_id)?'selected':''):''; ?>><?php echo $subs->name; ?></option>
                           <?php } ?>
                           <?php } ?>
                        </select>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="subscription_date">Subscription Date <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="subscription_date" id="subscription_date" type="text" class="form-control date" value="<?php echo (count($result)>0 && $result['subscription_date']!='')?date('d/m/Y', MongoEPOCH($result['subscription_date'])):''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="activation_date">Activation Date <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="activation_date" id="activation_date" type="text" class="form-control date" value="<?php echo (count($result)>0 && $result['activation_date']!='')?date('d/m/Y', MongoEPOCH($result['activation_date'])):''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="expiry_date">Expiry Date <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="expiry_date" id="expiry_date" type="text" class="form-control date" value="<?php echo (count($result)>0 && $result['expiry_date']!='')?date('d/m/Y', MongoEPOCH($result['expiry_date'])):''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Subscription Status <span class="req">*</span></label>
							<div class="col-sm-9">
								<label style="font-weight: 400;"><input name="subscription_status" id="subscription_status1" type="radio" value="Valid" <?php echo (count($result)>0 && $result['subscription_status']!='')?($result['subscription_status']=='Valid'?'checked':''):'checked'; ?>/> Valid</label> &nbsp;
								<label style="font-weight: 400;"><input name="subscription_status" id="subscription_status2" type="radio" value="Suspended" <?php echo (count($result)>0 && $result['subscription_status']=='Suspended')?'checked':''; ?>/> Suspended</label>
								<div class="help-block with-errors"></div>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label">Offer <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="offer" id="offer" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['offer']:''; ?>" required/>
							</div>
						</div>
	
						<div class="form-group">
							<label class="col-sm-3 control-label" for="marketing_id">Marketing ID <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="marketing_id" id="marketing_id" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['marketing_id']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="third_party_badge">Third Party Badge <span class="req">*</span></label>
							<div class="col-sm-9">
								<div class="switch success margin-top-10">
									<input type="checkbox" name="third_party_badge" id="third_party_badge" <?php echo (count($result)>0 && $result['third_party_badge']=='1')?'checked':''; ?> />
									<label for="third_party_badge">&nbsp;</label>
								</div>
							</div>
						</div>
						
						<?php if($this->uri->segment(2)=='users-add'){ ?>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="password">New Password <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="password" data-minlength="6" id="password" type="password" class="form-control" title="Please enter new password" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="confirm_password">Confirm Password <span class="req">*</span></label>
							<div class="col-sm-9">
								<input id="confirm_password" type="password" data-error="Please enter confirm password" data-match="#password" class="form-control" data-match-error="Password doesn't match" title="Please enter confirm password" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<?php } ?>
						
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
  </div>
</div>