<?php $this->load->view('admin/templates/header'); ?>
<?php $settings = $admin_settings->row(); ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title">Change Password</h4>
					<div class="card-content">
						<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/update_password',$attributes);	?>
						<input type="hidden" name="objectid" value="<?php echo (isset($objectid))?$objectid:''; ?>">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="admin_name">New Password <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="password" data-minlength="6" id="password" type="password" class="form-control" data-error="Please enter new password" title="Please enter new password" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="email">Confirm Password <span class="req">*</span></label>
							<div class="col-sm-9">
								<input id="confirm_password" type="password" data-error="Please enter confirm password" data-match="#password" class="form-control" data-match-error="Password doesn't match" required title="Please enter confirm password"/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="android_link">&nbsp;</label>
							<div class="col-sm-9">
								<input type="submit" value="Submit" class="btn btn-success"/>
							</div>
						</div>
						</form>							
					</div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>