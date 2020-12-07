<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
						<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/newsletter/save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Template Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="email_subject">Email Subject <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="email_subject" id="email_subject" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['email_subject']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="sender_name">Sender Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="sender_name" id="sender_name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['sender_name']:$siteTitle; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="sender_email">Sender Email <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="sender_email" id="sender_email" type="email" class="form-control" value="<?php echo (count($result)>0)?$result['sender_email']:$siteContactMail; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="content">Email Description <span class="req">*</span></label>
							<div class="col-sm-9">
								<textarea name="content" id="content" class="form-control mceEditor" title="Please enter page description" rows="3" style="height: 100px;" required><?php echo (count($result)>0)?$result['content']:''; ?></textarea>
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
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>