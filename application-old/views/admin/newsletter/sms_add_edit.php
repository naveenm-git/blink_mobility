<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
						<?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/newsletter/sms_save',$attributes); ?>
						<input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="name">Template Name <span class="req">*</span></label>
							<div class="col-sm-9">
								<input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
								<div class="help-block with-errors"></div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" for="content">SMS Description <span class="req">*</span></label>
							<div class="col-sm-9">
								<textarea name="content" id="content" class="form-control" title="Please enter page description" rows="3" style="height: 100px;" required><?php echo (count($result)>0)?$result['content']:''; ?></textarea>
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
		<script>
			function countchars(val) {
				var len = val.value.length;
				if (len > 150) {
					val.value = val.value.substring(0, 150);
				} else {
					$('#wordcount').text(150 - len);
				}
			}
		</script>
  </div>
</div>