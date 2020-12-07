<?php $this->load->view('admin/templates/header'); ?>
<style>

</style>
<?php if(count($cms)>0) $cms = $cms[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<div class="card-content">
						<div id="rootwizard-pill">
							<div class="tab-header pill">
								<div class="navbar">
									<div class="navbar-inner">
										<ul class="nav nav-tabs custom-nav">
											<li><a href="#tab-pill1" data-toggle="tab">Content</a></li>
											<li><a href="#tab-pill2" data-toggle="tab">SEO Information</a></li>
										</ul>
									</div>
								</div>
							</div>
							<div class="tab-content">
								<div class="tab-pane" id="tab-pill1">
									<div class="form-group">
										<label class="control-label">Page Title: </label>
										<p class="form-control" style="margin:0;"><?php echo $cms['name']; ?></p>
									</div>
									
									<div class="form-group">
										<label class="control-label">Custom Style: </label>
										<p><pre class="form-control"><?php echo $cms['styles']; ?></pre></p>
									</div>
									
									<div class="form-group">
										<label class="control-label">Custom Script: </label>
										<p><pre class="form-control"><?php echo $cms['scripts']; ?></pre></p>
									</div>
									
									<div class="form-group">
										<label class="control-label">Page Banner: </label>
										<p><img src="<?php echo ($cms['cms_banner']!='')?base_url('uploads/cms/').$cms['cms_banner']:base_url('assets/images/no-img-banner.png'); ?>" style="width:20%;" class="img-responsive" /></p>
									</div>
									
									<div class="form-group">
										<label class="control-label">Content: </label>
										<table style="border:1px solid #e5e5e5;width:100%;margin:0 auto;background: #f5f5f5;border:1px solid #ccc;">
											<tr><td style="padding: 10px;"><?php echo $cms['content']; ?></td></tr>
										</table>
									</div>
									
									<div class="form-group">
										<label class="control-label" for="android_link">&nbsp;</label>
										<a href="<?php echo base_url('admin/cms-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
									</div>
							</div>
							<div class="tab-pane" id="tab-pill2">
								<div class="form-group">
									<label class="control-label">Meta Title: </label>
									<p class="form-control" style="margin:0;"><?php echo $cms['meta_title']; ?></p>
								</div>
								
								<div class="form-group">
									<label class="control-label">Meta Keyword: </label>
									<p class="form-control" style="margin:0;"><?php echo $cms['meta_keyword']; ?></p>
								</div>
								
								<div class="form-group">
									<label class="control-label">Meta Description: </label>
									<p class="form-control" style="margin:0;"><?php echo $cms['meta_description']; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>