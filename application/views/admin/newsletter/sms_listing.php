<?php $this->load->view('admin/templates/header'); ?>
<style>
table.display td:first-child, 
table.display th:first-child,
table.display td:last-child,
table.display th:last-child {
	text-align:center;
}
.filters{
	height: auto;
}
</style>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="dropdown js__drop_down">
                  <div class="btn-group">
                     <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Bulk Action <span class="caret"></span></button> 
                     <ul class="dropdown-menu">
                        <li><a class="bulk-status" data-status="1" data-submitto="sms-template" data-redirect="sms-template-list" title="Make all active"></i>Active</a></li>
                        <li><a class="bulk-status" data-status="0" data-submitto="sms-template" data-redirect="sms-template-list" title="Make all in-active"></i>In Active</a></li>
                        <li><a class="bulk-delete" data-submitto="sms-template" data-redirect="sms-template-list" title="Remove selected data"></i>Delete</a></li>
                     </ul>
                  </div>
                  <a class="btn btn-xs btn-default" href="<?php echo base_url('admin/sms-template-add'); ?>" title="Add new author detail"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add New SMS Template</a>
               </div>
					
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>#</th>
								<th>Template Name</th>
								<th>Date</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>#</th>
								<th>Template Name</th>
								<th>Date</th>
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
				"order": [[ 3, "desc" ]],
				"columnDefs": [
					{ "orderable": false, "targets": [0, 1, -1] }
				],
				"processing": true,
				"serverSide": true,
				"ajax":{
					url : "<?php echo base_url(); ?>admin/newsletter/sms_listing_ajax",
					type: "post",
					error: function(){
						$(".datatable-error").html("");
						$("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
						$("#datatable_processing").css("display","none");
					}
				}
			});
			
			$('#clear-filter').click(function(){
				$('#category, #author, #sport').val('');
				dataTable.column(2).search($(this).val());
				dataTable.column(4).search($(this).val());
				dataTable.column(5).search($(this).val());
				dataTable.draw();
			});
			
			dataTable.columns(2).each(function ( colIdx ){
				$('#category').on('change', function () {
					dataTable .column(colIdx) .search($(this).val()) .draw();
				});
			});
			
			dataTable.columns(4).each(function ( colIdx ){
				$('#author').on('change', function () {
					dataTable .column(colIdx) .search($(this).val()) .draw();
				});
			});
			
			dataTable.columns(5).each(function ( colIdx ){
				$('#sport').on('change', function () {
					dataTable .column(colIdx) .search($(this).val()) .draw();
				});
			});
		});
						
		function change_status(id, status){
			posturl = "<?php echo base_url().'admin/newsletter/change_status'; ?>";
			redirecturl = "<?php echo base_url().'admin/sms-template-list'; ?>";
			update_status(id, status, posturl, redirecturl);
		}
			
		function delete_record(id){
			posturl = "<?php echo base_url().'admin/newsletter/remove'; ?>";
			redirecturl = "<?php echo base_url().'admin/sms-template-list'; ?>";
			remove_draft(id, posturl, redirecturl);
		}
	</script>
	</div>
</div>