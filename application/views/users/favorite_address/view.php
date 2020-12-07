<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
            <div class="box-content bordered default">
               <h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Title</label>
                     <p class="detail"><?php echo $result['title']; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Address</label>
                     <p class="detail"><?php echo $result['address']; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Created At</label>
                     <p class="detail"><?php echo date('M d, Y h:i:s A', MongoEPOCH($result['created_at'])); ?></p>
                  </div>
               </div>
               
               <?php if(isset($result['modified_at']) && $result['modified_at']!=''){ ?>
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Modified At</label>
                     <p class="detail"><?php echo date('M d, Y h:i:s A', MongoEPOCH($result['modified_at'])); ?></p>
                  </div>
               </div>
               <?php } ?>
               
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Status</label>
                     <p class="detail"><?php echo ($result['status']=='1')?'Active':'In Active'; ?></p>
                  </div>
               </div>
               
               <div class="row">
                  <div class="form-group col-md-4">
                     <a href="<?php echo base_url('admin/subscription-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php $this->load->view('admin/templates/footer'); ?>
      <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
   </div>
</div>