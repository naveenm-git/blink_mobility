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
                     <label class="control-label">First Name</label>
                     <p class="detail" style="margin:0;"><?php echo $result['first_name']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Last Name</label>
                     <p class="detail" style="margin:0;"><?php echo $result['last_name']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">User Name</label>
                     <p class="detail" style="margin:0;"><?php echo $result['user_name']; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Customer ID</label>
                     <p class="detail" style="margin:0;"><?php echo $result['customer_id']; ?></p>
                  </div>

                  <div class="col-sm-4 form-group">
                     <label class="control-label">Email</label>
                     <p class="detail" style="margin:0;"><?php echo $result['email']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Mobile Line</label>
                     <p class="detail" style="margin:0;"><?php echo $result['mobile']; ?></p>
                  </div>
					</div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Date Of Birth</label>
                     <p class="detail" style="margin:0;"><?php echo date('d/m/Y', MongoEPOCH($result['dob'])); ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Civility</label>
                     <p class="detail" style="margin:0;"><?php echo $result['civility']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Street</label>
                     <p class="detail" style="margin:0;"><?php echo $result['street']; ?></p>
                  </div>
					</div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Building</label>
                     <p class="detail" style="margin:0;"><?php echo ($result['building']!='')?$result['building']:'-'; ?></p>
                  </div>

                  <div class="col-sm-4 form-group">
                     <label class="control-label">Zipcode</label>
                     <p class="detail" style="margin:0;"><?php echo $result['zipcode']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">City</label>
                     <p class="detail" style="margin:0;"><?php echo $result['city']; ?></p>
                  </div>
                  
					</div>
					
               <div class="row">
                  <?php $subscription = getrow(SUBSCRIPTION, $result['subscription_id']); ?>
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Subscription</label>
                     <p class="detail" style="margin:0;"><?php echo $subscription->name; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Subscription Status</label>
                     <p class="detail" style="margin:0;"><?php echo $result['subscription_status']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Subscription Date</label>
                     <p class="detail" style="margin:0;"><?php echo date('d/m/Y', MongoEPOCH($result['subscription_date'])); ?></p>
                  </div>
					</div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Activation Date</label>
                     <p class="detail" style="margin:0;"><?php echo date('d/m/Y', MongoEPOCH($result['activation_date'])); ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Expiry Date</label>
                     <p class="detail" style="margin:0;"><?php echo date('d/m/Y', MongoEPOCH($result['expiry_date'])); ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Offer</label>
                     <p class="detail" style="margin:0;"><?php echo $result['offer']; ?></p>
                  </div>
					</div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Marketing ID</label>
                     <p class="detail" style="margin:0;"><?php echo $result['marketing_id']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Third Party Badge</label>
                     <p class="detail" style="margin:0;"><?php echo ($result['third_party_badge']=='1')?'Yes':'No'; ?></p>
                  </div>
					</div>
					
               <div class="row">
                  <div class="col-sm-12 form-group">
                     <a href="<?php echo base_url('admin/users-list'); ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
				</div>
			</div>
		</div>
    <?php $this->load->view('admin/templates/footer'); ?>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
  </div>
</div>