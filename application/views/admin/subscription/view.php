<?php $this->load->view('admin/templates/header'); ?>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
            <div class="box-content bordered default">
               <h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Plan Name</label>
                     <p class="detail"><?php echo $result['name']; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Membership Fee</label>
                     <p class="detail"><?php echo '$ '.$result['fees']; ?></p>
                  </div>
               </div>
               
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Membership Type</label>
                     <p class="detail"><?php echo $result['validity']['count'].' '.$validityInterval[$result['validity']['interval']]; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Airport Surcharge</label>
                     <p class="detail"><?php echo '$ '.$result['airport_surcharge']; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Initial 20 Mins Charge</label>
                     <p class="detail"><?php echo '$ '.$result['initial_charge']; ?></p>
                  </div>
               </div>
                     
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">After 20 Mins (per minute charge)</label>
                     <p class="detail"><?php echo '$ '.$result['after_charge']; ?></p>
                  </div>
               </div>
                     
               
               <div class="row">
                  <div class="form-group col-md-4">
                     <label class="control-label">Proof Of Qualification</label>
                     <p class="detail"><?php echo ($result['proof_of_qualification']=='1')?'Required':'Not Required'; ?></p>
                  </div>
               </div>
               
               <?php if(!empty($result['terms'])){ ?>
               <div class="row">
                  <div class="form-group col-md-12">
                     <label class="control-label">Terms</label>
                     <?php foreach($result['terms'] as $terms){ ?>
                     <li><?php echo $terms; ?></li>
                     <?php } ?>
                  </div>
               </div>
               <?php } ?>
               
               <div class="row">
                  <div class="form-group col-md-4">
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