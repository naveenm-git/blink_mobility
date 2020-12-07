<?php $this->load->view('admin/templates/header'); ?>
<?php $tabindex=1; $result = $result->result_array()[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
        <div class="box-content bordered default">
          <h4 class="box-title"><?php echo $heading; ?></h4>
					<div id="rootwizard-pill">
						<div class="tab-header pill">
							<div class="navbar">
								<div class="navbar-inner">
									<ul class="nav nav-tabs custom-nav">
										<li><a href="#general" data-toggle="tab">General</a></li>
										<li><a href="#upload" data-toggle="tab">Upload</a></li>
										<li><a href="#google" data-toggle="tab">API Key</a></li>
										<li><a href="#mail" data-toggle="tab">Mail Config</a></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="tab-content">
                     
							<div class="tab-pane" id="general">
								<div class="card-content">
									<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/admin/admin_global_settings',$attributes); ?>
									<input type="hidden" value="admin_settings" name="form_mode">
									<div class="form-group">
										<label class="col-sm-3 control-label" for="admin_name">Admin Name <span class="req">*</span></label>
										<div class="col-sm-9">
											<input name="admin_name" value="<?php echo $result['admin_name'];?>" id="admin_name" type="text" class="form-control" title="Please enter the admin name" required/>
											<div class="help-block with-errors"></div>
										</div>
									</div>
                           <div class="form-group">
                              <label class="col-sm-3 control-label" for="email_title">Site Name <span class="req">*</span></label>
                              <div class="col-sm-9">
                                 <input name="email_title" id="email_title" type="text" value="<?php echo $result['email_title'];?>" class="form-control" title="Please enter the site name" required/>
                                 <div class="help-block with-errors"></div>
                              </div>
                           </div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="admin_email">Email <span class="req">*</span></label>
										<div class="col-sm-9">
											<input name="admin_email" id="admin_email" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="<?php echo $result['admin_email'];?>" class="form-control" required title="Please enter the admin email"/>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="contact_tel">Mobile Number</label>
										<div class="col-sm-9">
											<input name="contact_tel" id="contact_tel" type="number" value="<?php echo ($result['contact_tel']!='0')?htmlentities($result['contact_tel']):'';?>" class="form-control" title="Please enter mobile number"/>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="admin_footer_content">Footer Content</label>
										<div class="col-sm-9">
											<input name="admin_footer_content" id="admin_footer_content" type="text" value="<?php echo htmlentities($result['admin_footer_content']);?>" class="form-control" title="Please enter footer copyright content"/>
											<div class="help-block with-errors"></div>
										</div>
									</div>
										
									<div class="form-group">
										<label class="col-sm-3 control-label">Change Password</label>
										<div class="col-sm-9">
											<div class="switch primary margin-top-10">
												<input type="checkbox" name="changepwd" id="changepwd">
												<label for="changepwd">&nbsp;</label>
											</div>
										</div>
									</div>
									<div class="form-group pswd hidden">
										<label class="col-sm-3 control-label" for="password">New Password <span class="req">*</span></label>
										<div class="col-sm-9">
											<input name="password" data-minlength="6" id="password" type="password" class="form-control" title="Please enter new password" />
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group pswd hidden">
										<label class="col-sm-3 control-label" for="confirm_password">Confirm Password <span class="req">*</span></label>
										<div class="col-sm-9">
											<input id="confirm_password" type="password" data-error="Please enter confirm password" data-match="#password" class="form-control" data-match-error="Password doesn't match" title="Please enter confirm password"/>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
										<div class="col-sm-3">
											<input type="submit" class="btn btn-sm btn-success" value="Submit"/>
										</div>
									</div>
									</form>
								</div>
							</div>
                     
							<div class="tab-pane" id="upload">
								<div class="card-content">
									<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/admin/admin_global_settings',$attributes); ?>
									<input type="hidden" value="admin_settings" name="form_mode">
										<div class="form-group">
											<label class="col-sm-3 control-label" for="background_image">Login Background</label>
											<div class="col-sm-9">
												<input name="background_image" id="background_image" type="file" class="large" title="Please select the login background image"/>
												<?php if(isset($result['background_image']) && $result['background_image'] !=''){ ?>
												<img src="<?php echo base_url().'images/logo/'.$result['background_image'];?>" width="100px" class="files"/>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="site_logo">Site Logo</label>
											<div class="col-sm-9">
												<input name="site_logo" id="site_logo" type="file" class="large" title="Please select the logo image"/>
												<?php if(isset($result['site_logo']) && $result['site_logo'] !=''){ ?>
												<img src="<?php echo base_url().'images/logo/'.$result['site_logo'];?>" width="100px" class="files"/>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="favicon">Favicon</label>
											<div class="col-sm-9">
												<input name="favicon" id="favicon" type="file" class="large" title="Please select the favicon image"/>
												<?php if(isset($result['favicon']) && $result['favicon']!=''){ ?>
												<img src="<?php echo base_url().'images/logo/'.$result['favicon'];?>" width="50px" class="files"/>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
											<div class="col-sm-9">
												<input type="submit" class="btn btn-sm btn-success" value="Submit"/>
											</div>
										</div>
									</form>
								</div>
							</div>
                     
							<div class="tab-pane" id="google">
								<div class="card-content">
									<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/admin/admin_global_settings',$attributes); ?>
									<input type="hidden" value="admin_settings" name="form_mode">
										<h4 class="box-title">Google Analytics</h4>
										
										<div class="form-group">
											<label class="col-sm-3 control-label" for="google_maps_api_key">Google Maps API Key</label>
											<div class="col-sm-9">
												<input name="google_maps_api_key" id="google_maps_api_key" type="text" class="form-control" value="<?php echo $result['google_maps_api_key']; ?>"/>
												<div class="help-block with-errors"></div>
											</div>
										</div>
																									
										<div class="form-group">
											<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
											<div class="col-sm-9">
												<input type="submit" class="btn btn-sm btn-success" value="Submit"/>
											</div>
										</div>
									</form>
								</div>
							</div>
                     
							<div class="tab-pane" id="mail">
								<div class="card-content">
									<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/admin/admin_global_settings',$attributes); ?>
									<input type="hidden" value="admin_settings" name="form_mode">
										<div class="form-group">
											<label class="col-sm-3 control-label" for="smtp_host">SMTP Host <span class="req">*</span></label>
											<div class="col-sm-9">
												<input name="smtp_host" id="smtp_host" type="text" class="form-control" value="<?php echo $result['smtp_host']; ?>" required/>
												<div class="help-block with-errors"></div>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-3 control-label" for="smtp_port">SMTP Port <span class="req">*</span></label>
											<div class="col-sm-9">
												<input name="smtp_port" id="smtp_port" type="text" class="form-control" value="<?php echo $result['smtp_port']; ?>" required/>
												<div class="help-block with-errors"></div>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-3 control-label" for="smtp_email">SMTP Email <span class="req">*</span></label>
											<div class="col-sm-9">
												<input name="smtp_email" id="smtp_email" type="email" class="form-control" value="<?php echo $result['smtp_email']; ?>" required/>
												<div class="help-block with-errors"></div>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-3 control-label" for="smtp_password">SMTP Password <span class="req">*</span></label>
											<div class="col-sm-9">
												<input name="smtp_password" id="smtp_password" type="password" minlength="5" class="form-control" value="<?php echo $result['smtp_password']; ?>" required/>
												<div class="help-block with-errors"></div>
											</div>
										</div>
																														
										<div class="form-group">
											<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
											<div class="col-sm-9">
												<input type="submit" class="btn btn-sm btn-success" value="Submit"/>
											</div>
										</div>
									</form>
								</div>
							</div>
                     
						</div>
					</div>
        </div>
      </div>
    </div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
		<script>
		$('#date_times').datetimepicker({
			minView: 2,
			format: 'dd/mm/yyyy',
			todayHighlight : true,
			startDate: new Date(),
			autoclose: true,
			pickerPosition: "top-right"
		});
		
		$('#changepwd').change(function(){
			if($(this).is(':checked')===true){
				$('input[type="password"]').prop('required', true);
				$('.pswd').removeClass('hidden');
			} else {
				$('input[type="password"]').prop('required', false);
				$('.pswd').addClass('hidden');
			}
		});
		</script>
  </div>
</div>