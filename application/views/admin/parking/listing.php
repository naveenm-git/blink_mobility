<?php $this->load->view('admin/templates/header'); ?>
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
                        <li><a class="bulk-status" data-status="1" data-submitto="parking" data-redirect="parking-list" title="Make all active"></i>Active</a></li>
                        <li><a class="bulk-status" data-status="0" data-submitto="parking" data-redirect="parking-list" title="Make all in-active"></i>In Active</a></li>
                        <li><a class="bulk-delete" data-submitto="parking" data-redirect="parking-list" title="Remove selected data"></i>Delete</a></li>
                     </ul>
                  </div>
                  <a class="btn btn-xs btn-default" href="<?php echo base_url('admin/parking-add'); ?>" title="Add new author detail"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add New Parking</a>
               </div>
               
					<div class="box-content" style="margin:0;border-bottom:none;box-shadow:none;padding:0 0 10px;">
						<div class="row">
							<div class="form-group">
								<div class="col-sm-3">
									<label for="station" id="for-station">Station</label>
									<select id="station" class="form-control filters">
										<option value="">Select station</option>
										<?php foreach($stations->result() as $station){ ?>
										<option value="<?php echo (string)$station->_id; ?>"><?php echo $station->name; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-2">
									<label>&nbsp;</label>
									<p class="form-control" id="clear-filter">Show All</p>
								</div>
							</div>
						</div>
					</div>
					
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>#</th>
								<th>Name</th>
								<th>Station</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>#</th>
								<th>Name</th>
								<th>Station</th>
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
               "order": [[ 2, "desc" ]],
               "columnDefs": [
                  { "orderable": false, "targets": [0, 1, 3, -1] }
               ],
               "processing": true,
               "serverSide": true,
               "ajax":{
                  url : "<?php echo base_url(); ?>admin/parking/listing_ajax",
                  type: "post",
                  error: function(){
                     $(".datatable-error").html("");
                     $("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
                     $("#datatable_processing").css("display","none");
                  }
               }
            });
            
            $('#clear-filter').click(function(){
               $('#station').val('');
               dataTable.column(3).search($(this).val());
               dataTable.draw();
            });
            
            dataTable.columns(3).each(function ( colIdx ){
               $('#station').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });

         });
                     
         function change_status(id, status){
            posturl = "<?php echo base_url().'admin/parking/change_status'; ?>";
            redirecturl = "<?php echo base_url().'admin/parking-list'; ?>";
            update_status(id, status, posturl, redirecturl);
         }
            
         function delete_record(id){
            posturl = "<?php echo base_url().'admin/parking/remove'; ?>";
            redirecturl = "<?php echo base_url().'admin/parking-list'; ?>";
            remove_draft(id, posturl, redirecturl);
         }
      </script>
   </div>
</div>