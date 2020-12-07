<?php $this->load->view('admin/templates/header'); ?>
<style>
p.detail{
   color: #26556e;
}
</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Station Name</label>
                     <p class="detail" style="margin:0;"><?php echo getrow(STATION, $result['station_id'])->name; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Make</label>
                     <p class="detail" style="margin:0;"><?php echo $result['make']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Model</label>
                     <p class="detail" style="margin:0;"><?php echo $result['model']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Year</label>
                     <p class="detail" style="margin:0;"><?php echo $result['year']; ?></p>
                  </div>
               </div>
												
               <div class="row">
                  <div class="col-sm-12 form-group">
                     <a href="<?php echo base_url('admin/vehicle-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>