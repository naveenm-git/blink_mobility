<?php $this->load->view('users/templates/header'); ?>
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
									<td colspan="3">Customer Details</td>
								</tr>
								<tr>
									<td>User Name</td>
									<td>:</td>
									<td><?php echo ($result['username']!='')?$result['username']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>First Name</td>
									<td>:</td>
									<td><?php echo ($result['first_name']!='')?$result['first_name']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>Last Name</td>
									<td>:</td>
									<td><?php echo ($result['last_name']!='')?$result['last_name']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>Email</td>
									<td>:</td>
									<td><?php echo ($result['email']!='')?$result['email']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>Phone No.</td>
									<td>:</td>
									<td><?php echo ($result['phone_number']!='')?$result['phone_number']:'N/A'; ?></td>
								</tr>
                        <?php
                           $address = $result['floor_no'].', '.$result['address_line_1'].', '.$result['address_line_2'].', '.$result['city'].', '.$result['state'].', '.$result['country'].' '.$result['zipcode'];
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
                        <?php $usersubscription = getresult(USERS_SUBSCRIPTION, ['_id'=>MongoID($result['subscription_id']), 'user_id'=>$_user_id]); ?>
								<tr>
									<td class="subscription"><?php echo $usersubscription->row()->name; ?></td>
									<td><?php echo ($usersubscription->row()->status=='1')?'<span class="btn btn-xs btn-success">Valid</span>':'<span class="btn btn-xs btn-danger">Invalid</span>'; ?></td>
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
									<td><button class="btn btn-xs btn-default" style="width:100%;">Flag this customer's address as incorrect</button></td>
								</tr>
							</table>
						</div>
					</div>
					
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td>Active Rentals</td>
								</tr>
								<tr>
									<td><i>No active rentals.</i></td>
								</tr>
							</table>
						</div>
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td>Active Car Reservation</td>
								</tr>
								<tr>
									<td><i>No car reservations.</i></td>
								</tr>
							</table>
						</div>
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td>Active Parking Reservation</td>
								</tr>
								<tr>
									<td><i>No parking spot reservations.</i></td>
								</tr>
							</table>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('users/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	</div>
</div>