<?php $this->load->view('station/templates/header'); ?>
<style>
	table.details{
		width: 100%;
	}
	table.details th, td{
		border-top: 1px solid #e5e5e5;
		padding: 5px;
	}
	table.details tr.table-title td{
		font-size: 20px;
		border: none;
		border-bottom: 2px solid #000;
	}
	table.details td.subscription{
		border-left: 3px solid #488a29;
		color: #488a29;
      background: rgba(72, 138, 41, 0.1);
		font-weight: 600;
	}
	.fa-times{
		color: red;
	}
	.fa-check{
		color: green;
	}
</style>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					
					<div class="row">
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td colspan="3">Station Details</td>
								</tr>
                        <?php
                           $address = $result['street'].', '.$result['city'].', '.$result['zipcode'];
                        ?>
								<tr>
									<td>Address</td>
									<td>:</td>
									<td><?php echo $address; ?></td>
								</tr>
								<tr>
									<td>Push Notification</td>
									<td>:</td>
									<td><?php echo (isset($result['preferences']) && $result['preferences']['app_push_notifications']==1)?'<i class="fa fa-check"></i>':'<i class="fa fa-times"></i>'; ?></td>
								</tr>
							</table>
						</div>
						
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td colspan="3">Customer Details</td>
								</tr>
								<tr>
									<th>Offer</th>
									<th>Status</th>
								</tr>
								<?php if(isset($result['subscription_id'])){ ?>
								<tr>
									<td class="subscription"><?php echo getrow(SUBSCRIPTION, getresult(USERS_SUBSCRIPTION, ['_id'=>MongoID($result['subscription_id']), 'user_id'=>$_user_id])->row()->membership_type)->name; ?></td>
									<td><span>Valid</span></td>
								</tr>
								<?php } else { ?>
								<tr>
									<td colspan="2">No subscription detail.</td>
								</tr>
								<?php } ?>
							</table>
						</div>
						
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td colspan="2">Actions</td>
								</tr>
								<tr>
									<td><button class="btn btn-xs btn-default" style="width:100%;">Flag this customer's address as incorrect</button><br/><button class="btn btn-xs btn-default" style="margin-top:5px;width:100%;">Manage RFID cards</button></td>
								</tr>
							</table>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('station/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	</div>
</div>