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
                        <li><a class="bulk-status" data-status="1" data-submitto="vehicle" data-redirect="vehicle-list" title="Make all active"></i>Active</a></li>
                        <li><a class="bulk-status" data-status="0" data-submitto="vehicle" data-redirect="vehicle-list" title="Make all in-active"></i>In Active</a></li>
                        <li><a class="bulk-delete" data-submitto="vehicle" data-redirect="vehicle-list" title="Remove selected data"></i>Delete</a></li>
                     </ul>
                  </div>
                  <a class="btn btn-xs btn-default" href="<?php echo base_url('admin/vehicle-add'); ?>" title="Add new author detail"><i class="fa fa-plus">&nbsp;&nbsp;</i>Add New Vehicle</a>
               </div>
               
               <div class="filter-panel-cover">
                  <button class="filter-accordion">Filter Data &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                  <div class="filter-panel">
                     <div class="row">
                        <div class="form-group">
                           <div class="col-sm-3">
                              <label for="make_id" id="for-make_id">Make</label>
                              <select id="make_id" class="form-control filters">
                                 <option value="">Select Make</option>
                                 <?php foreach($makes->result() as $res){ ?>
                                 <option value="<?php echo (string)$res->_id; ?>"><?php echo $res->make; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-sm-3">
                              <label for="model_id" id="for-model_id">Model</label>
                              <select id="model_id" class="form-control filters">
                                 <option value="">Select Model</option>
                                 <?php foreach($models->result() as $res){ ?>
                                 <option value="<?php echo (string)$res->_id; ?>"><?php echo $res->model; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-sm-3">
                              <label for="year" id="for-year">Year</label>
                              <select id="year" class="form-control filters">
                                 <option value="">Select Year</option>
                                 <?php foreach($years as $year){ ?>
                                 <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <div class="col-sm-3">
                              <label for="car_status" id="for-car_status">Car Status</label>
                              <select name="car_status" class="form-control" id="car_status" required>
                                 <option value="">Select car status</option>
                                 <?php foreach($car_status as $key => $value){ ?>
                                 <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
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
								<th>VIN No.</th>
								<th>License Plate</th>
								<th>Make</th>
								<th>Modal</th>
								<th>Year</th>
								<th>Car Status</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><input type="checkbox" class="select-all"/></th>
								<th>VIN No.</th>
								<th>License Plate</th>
								<th>Make</th>
								<th>Modal</th>
								<th>Year</th>
								<th>Car Status</th>
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
                  { "orderable": false, "targets": [0, 3, 4, 6, -1] }
               ],
               "processing": true,
               "serverSide": true,
               "ajax":{
                  url : "<?php echo base_url(); ?>admin/vehicle/listing_ajax",
                  type: "post",
                  error: function(){
                     $(".datatable-error").html("");
                     $("#datatable").append('<tbody class="datatable-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
                     $("#datatable_processing").css("display","none");
                  }
               }
            });
            
            $('#clear-filter').click(function(){
               $('#make_id, #model_id, #year, #car_status').val('');
               dataTable.column(3).search('');
               dataTable.column(4).search('');
               dataTable.column(5).search('');
               dataTable.column(6).search('');
               dataTable.draw();
            });
            
            dataTable.columns(3).each(function ( colIdx ){
               $('#make_id').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(4).each(function ( colIdx ){
               $('#model_id').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(5).each(function ( colIdx ){
               $('#year').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(6).each(function ( colIdx ){
               $('#car_status').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            
            $('#make_id').change(function(){
               make_id = $(this).val();
               $.ajax({
                  url: '<?php echo base_url(); ?>admin/vehicle/get_maker_models',
                  type: 'POST',
                  data: {make_id: make_id},
                  success: function(d){
                     d = JSON.parse(d);
                     if(d.status=='1'){
                        option = '<option value="">Select Model</option>';
                        $.each(d.response, function(i, v){
                           option += '<option value="'+v._id+'">'+v.model+'</option>';
                        });
                        $('select#model_id').empty().append(option);
                     } 
                  }
               });
            });

         });
                     
         function change_status(id, status){
            posturl = "<?php echo base_url().'admin/vehicle/change_status'; ?>";
            redirecturl = "<?php echo base_url().'admin/vehicle-list'; ?>";
            update_status(id, status, posturl, redirecturl);
         }
            
         function delete_record(id){
            posturl = "<?php echo base_url().'admin/vehicle/remove'; ?>";
            redirecturl = "<?php echo base_url().'admin/vehicle-list'; ?>";
            remove_draft(id, posturl, redirecturl);
         }
      </script>
   </div>
</div>