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
                        <li><a class="bulk-status" data-status="1" data-submitto="station" data-redirect="station-list" title="Make all active"></i>Active</a></li>
                        <li><a class="bulk-status" data-status="0" data-submitto="station" data-redirect="station-list" title="Make all in-active"></i>In Active</a></li>
                        <li><a class="bulk-delete" data-submitto="station" data-redirect="station-list" title="Remove selected data"></i>Delete</a></li>
                     </ul>
                  </div>
                  <a class="btn btn-xs btn-default" href="<?php echo base_url('admin/station-add'); ?>" title="Add new author detail"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add New Station</a>
               </div>
               
               <div class="filter-panel-cover">
                  <button class="filter-accordion">Filter Data &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                  <div class="filter-panel">
                        <div class="row">
                           <div class="form-group">
                              <div class="col-sm-3">
                                 <label for="zipcode">Zip Code</label>
                                 <select id="zipcode" class="form-control filters">
                                    <option value="">Select zip code</option>
                                    <?php foreach($zipcodes as $zipcode){ ?>
                                    <option value="<?php echo $zipcode; ?>"><?php echo $zipcode; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-3">
                                 <label for="city">City</label>
                                 <select id="city" class="form-control filters">
                                    <option value="">Select zip code</option>
                                    <?php foreach($cities as $city){ ?>
                                    <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
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
               </div>
                 
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>Station Name</th>
								<th>Street</th>
								<th>Zipcode</th>
								<th>City</th>
								<th>RP</th>
								<th>CP</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>Station Name</th>
								<th>Street</th>
								<th>Zipcode</th>
								<th>City</th>
								<th>RP</th>
								<th>CP</th>
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
               "order": [[ 1, "asc" ]],
               "columnDefs": [
                  { "orderable": false, "targets": [0, -4, -3, -1] }
               ],
               "processing": true,
               "serverSide": true,
               "ajax":{
                  url : "<?php echo base_url(); ?>admin/station/listing_ajax",
                  type: "post",
                  error: function(){
                     $(".datatable-error").html("");
                     $("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
                     $("#datatable_processing").css("display","none");
                  }
               }
            });
            
            $('#clear-filter').click(function(){
               $('#zipcode, #city').val('');
               dataTable.column(3).search($(this).val());
               dataTable.column(4).search($(this).val());
               dataTable.draw();
            });
            
            dataTable.columns(3).each(function ( colIdx ){
               $('#zipcode').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            dataTable.columns(4).each(function ( colIdx ){
               $('#city').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
         });
         
         function change_status(id, status){
            posturl = "<?php echo base_url().'admin/station/change_status'; ?>";
            redirecturl = "<?php echo base_url().'admin/station-list'; ?>";
            update_status(id, status, posturl, redirecturl);
         }
            
         function delete_record(id){
            posturl = "<?php echo base_url().'admin/station/remove'; ?>";
            redirecturl = "<?php echo base_url().'admin/station-list'; ?>";
            remove_draft(id, posturl, redirecturl);
         }
      </script>
   </div>
</div>