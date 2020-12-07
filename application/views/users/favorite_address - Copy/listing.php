<?php $this->load->view(USERURL.'/templates/header'); ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					
					<div class="dropdown js__drop_down">
						<a class="btn btn-xs btn-default" href="<?php echo base_url(USERURL.'/'.$_user_id.'/favorite-address/add'); ?>" title="Add new author detail"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add New Address</a>
					</div>
					
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th>Title</th>
								<th>Address</th> 
								<th>Status</th>  
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Title</th>
								<th>Address</th> 
								<th>Status</th> 
								<th>Action</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php $this->load->view(USERURL.'/templates/footer'); ?>
	<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	<script>
		$(document).ready(function(){
			
			var dataTable = $('#datatable').DataTable({
				"pageLength": 10,
				"lengthMenu": [
					[ 10, 25, 100, -1 ],
					[ '10', '25', '100', 'All' ]
				],
				"order": [[ 0, "asc" ]],
				"columnDefs": [
					{ "orderable": false, "targets": [-1] }
				],
				"processing": true,
				"serverSide": true,
				"ajax":{
					url : "<?php echo base_url().USERURL.'/'.$_user_id.'/favorite-address/get'; ?>",
					type: "post",
					error: function(){
						$(".datatable-error").html("");
						$("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
						$("#datatable_processing").css("display","none");
					}
				}
			});

		});
      
		function change_status(id, status){
			posturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/favorite-address/status'; ?>";
			redirecturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/favorite-address'; ?>";
			update_status(id, status, posturl, redirecturl);
		}
      
      function delete_record(id){
         posturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/favorite-address/remove'; ?>";
			redirecturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/favorite-address'; ?>";
         remove_draft(id, posturl, redirecturl);
      }
	</script>
	</div>
</div>