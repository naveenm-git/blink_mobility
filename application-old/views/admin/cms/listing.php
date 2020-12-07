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
					<?php if($user_type!='station'){ ?>
						<div class="dropdown js__drop_down">
							<a title="Add New Case" href="<?php echo base_url('admin/cms-add'); ?>" class="btn btn-xs btn-black btn-rounded btn-bordered"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add Static Page</a>
						</div>
					<?php } ?>
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Page Title</th>
								<th>Page Url</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>#</th>
								<th>Page Title</th>
								<th>Page Url</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php $this->load->view('admin/templates/footer'); ?>
	<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	<script>
		$(document).ready(function(){
			
			var dataTable = $('#datatable').DataTable({
				"pageLength": 10,
				"lengthMenu": [
					[ 10, 25, 100, -1 ],
					[ '10', '25', '100', 'All' ]
				],
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{ "orderable": false, "targets": [0, -1] }
				],
				"processing": true,
				"serverSide": true,
				"ajax":{
					url : "<?php echo base_url(); ?>admin/cms/listing_ajax",
					type: "post",
					error: function(){
						$(".datatable-error").html("");
						$("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
						$("#datatable_processing").css("display","none");
					}
				}
			});
		});
				
		function status_change(id, status){
			posturl = "<?php echo base_url().'admin/cms/change_cms_status'; ?>";
			redirecturl = "<?php echo base_url().'admin/cms-list'; ?>";
			update_status(id, status, posturl, redirecturl);
		}
			
		function delete_record(id){
			posturl = "<?php echo base_url().'admin/cms/remove_cms'; ?>";
			redirecturl = "<?php echo base_url().'admin/cms-list'; ?>";
			remove_draft(id, posturl, redirecturl);
		}
	</script>
	</div>
</div>