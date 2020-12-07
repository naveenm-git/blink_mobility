<?php $this->load->view('admin/templates/header'); ?>
<style>
table.display td:last-child, table.display th:last-child{
	text-align:center;
}
</style>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<table id="admin-users" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Type</th>
								<th>Camera ID</th>
								<th>Bio-Metric ID</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Type</th>
								<th>Camera ID</th>
								<th>Bio-Metric ID</th>
								<th>Action</th>
							</tr>
						</tfoot>
						<tbody>
							<?php if($adminusers->num_rows() > 0){ ?>
							<?php foreach($adminusers->result_array() as $admin){ ?>
								<tr>
									<td><?php echo $admin['admin_name']; ?></td>
									<td><?php echo $admin['admin_email']; ?></td>
									<td><?php echo ucwords($admin['type']); ?></td>
									<td><?php echo $admin['camera_id']; ?></td>
									<td><?php echo $admin['device_id']; ?></td>
									<td>
										<?php if($admin['admin_id']!='1'){ ?>
											<a class="action-a" href="<?php echo base_url().'admin/view-subadmin/'.(string)$admin['_id']; ?>" title="View"><i class="fa fa-eye"></i></a>
											<a class="action-a" href="<?php echo base_url().'admin/subadmin-password/'.(string)$admin['_id']; ?>" title="Change Password"><i class="fa fa-lock"></i></a>
											<a class="action-a" href="<?php echo base_url().'admin/subadmin/'.(string)$admin['_id']; ?>" title="Edit Subadmin"><i class="fa fa-pencil"></i></a>
										<?php } ?>
									</td>
								</tr>
							<?php } } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php $this->load->view('admin/templates/footer'); ?>
	<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	</div>
</div>