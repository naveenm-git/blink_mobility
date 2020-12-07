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
	span.status_yes{
		color: green;
		font-weight: 500;
	}
   span.status_no{
		color: red;
		font-weight: 500;
	}
</style>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<div class="dropdown js__drop_down">
						<a class="btn btn-xs btn-default" href="<?php echo base_url(USERURL.'/'.$_user_id.'/account/edit'); ?>" title="Edit Customer Detail"><i class="fa fa-pencil">&nbsp;&nbsp;</i> Edit</a>
					</div>
					
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
									<td>Title</td>
									<td>:</td>
									<td><?php echo ($result['title']!='')?$result['title']:'N/A'; ?></td>
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
									<td>DOB</td>
									<td>:</td>
									<td><?php echo (isset($result['dob']) && $result['dob']!='')?date('M d, Y', MongoEPOCH($result['dob'])):'N/A'; ?></td>
								</tr>
								<tr>
									<td>Language</td>
									<td>:</td>
									<td><?php echo ($result['language']!='')?$languages[$result['language']]:'N/A'; ?></td>
								</tr>
							</table>
						</div>
						
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td colspan="3">Contacts</td>
								</tr>
								<tr>
									<td>Email</td>
									<td>:</td>
									<td><?php echo ($result['email']!='')?$result['email']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>Home Phone</td>
									<td>:</td>
									<td><?php echo ($result['home_phone']!='')?$result['home_phone']:'N/A'; ?></td>
								</tr>
								<tr>
									<td>Mobile</td>
									<td>:</td>
									<td><?php echo ($result['phone_number']!='')?$result['phone_number']:'N/A'; ?></td>
								</tr>
                        <?php
                           $address = $result['floor_no'].', '.$result['address_line_1'].', '.$result['address_line_2'].', '.$result['city'].', '.$result['state'].', '.$result['country'].' '.$result['zipcode'];
                        ?>
								<tr>
									<td>Address</td>
									<td>:</td>
									<td><?php echo ($address!='')?$address:'N/A'; ?></td>
								</tr>
							</table>
						</div>
						
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td colspan="3">Status</td>
								</tr>
								<tr>
									<td>Contact</td>
									<td>:</td>
									<td><?php echo '<span class="status_no">No</span>'; ?></td>
								</tr>
								<tr>
									<td>Personal</td>
									<td>:</td>
									<td><?php echo '<span class="status_yes">Yes</span>'; ?></td>
								</tr>
								<tr>
									<td>Address</td>
									<td>:</td>
									<td><?php echo '<span class="status_yes">Yes</span>'; ?></td>
								</tr>
								<tr>
									<td>Profile Validation</td>
									<td>:</td>
									<td><?php echo '<span class="status_yes">Yes</span>'; ?></td>
								</tr>
								<tr>
									<td>PIN Code</td>
									<td>:</td>
									<td><?php echo '<span class="status_yes">Yes</span>'; ?></td>
								</tr>
								<tr class="table-title">
									<td colspan="3">Other</td>
								</tr>
								<tr>
									<td>PIN Failures</td>
									<td>:</td>
									<td>0</td>
								</tr>
								<tr>
									<td>Reimbursement IBAN</td>
									<td>:</td>
									<td>Not Defined</td>
								</tr>
								<tr>
									<td>Reimbursement BIC</td>
									<td>:</td>
									<td>Not Defined</td>
								</tr>
							</table>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">
							<table class="details">
								<tr class="table-title">
									<td>Customer Consents</td>
								</tr>
								<tr>
									<td>Preferences</td>
								</tr>
								<tr>
									<td>Last Modification Of The Parameters</td>
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