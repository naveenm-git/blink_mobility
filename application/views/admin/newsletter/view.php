<?php $this->load->view('admin/templates/header'); ?>
<style>

</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					
					<div class="row">
						<div class="col-sm-12 form-group">
							<label class="control-label">Template Name: </label>
							<p class="form-control" style="margin:0;"><?php echo $result['name']; ?></p>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12 form-group">
							<label class="control-label">Email Subject: </label>
							<p class="form-control" style="margin:0;"><?php echo $result['email_subject']; ?></p>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12 form-group">
							<label class="control-label">Sender Name: </label>
							<p class="form-control" style="margin:0;"><?php echo $result['sender_name']; ?></p>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12 form-group">
							<label class="control-label">Sender Email: </label>
							<p class="form-control" style="margin:0;"><?php echo $result['sender_email']; ?></p>
						</div>
					</div>
										
					<div class="form-group">
						<label class="control-label">Email Description: </label>
						<table style="width:100%;margin:0 auto;border:1px solid #ccc;">
							<tbody>
								<tr><td style="padding: 10px;"><?php echo $result['content']; ?></td></tr>
							</tbody>
						</table>
					</div>
					
					<div class="form-group">
						<a href="<?php echo base_url('admin/email-template-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
					</div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>