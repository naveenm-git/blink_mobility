<?php $this->load->view('users/templates/header'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/lightview/css/lightview/lightview.css">
<style>
   p.detail{
   color: #26556e;
   margin:0;
   }
   table.details, table.inner-table{
   width: 100%;
   }
   table.details th, td{
   border-top: 1px solid #e5e5e5;
   padding: 5px;
   }table.inner-table th, td{
   border: none;
   }
   table.details th{
   background: #e5e5e5;
   }
   table.inner-table th{
   background: #fff;
   }
   table.details tr.table-title td{
   font-size: 20px;
   border: none;
   border-bottom: 2px solid #000;
   }
   .document-img{
   width: 75px;
   height: 75px;
   object-fit: contain;
   }
   .gallery-grid{
   width: auto;
   height: 200px;
   }
   .item-gallery img{
   min-width: auto;
   }
   .js__isotope_item{
   float: none;
   margin: 0;
   }
   .item-gallery{
   display: inline-block;
   }
</style>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
   <div class="main-content">
      <div class="row small-spacing">
         <div class="col-md-12 col-xs-12">
            <div class="box-content bordered default">
               <?php 
                  if(isset($result[$query]) && isset($result[$query][$document])){
                     $document_type = $query;
                     $created_at = $result[$query][$document]['created_at'];
                     $modified_at = isset($result[$query][$document]['modified_at'])?date('m/d/Y h:i:s A', MongoEPOCH($result[$query][$document]['modified_at'])):'-';
                     if($query=='documents_to_verify'){
                        $status = '<span class="text-primary">Not Verified</span>';
                     } else if($query=='verified_documents'){
                        $status = '<span class="text-success">Verified</span>';
                     } else {
                        $status = '<span class="text-danger">Rejected</span>';
                     }
                     if($document=='license'){
                        $imgArr = [$result[$query][$document]['front'], $result[$query][$document]['back']];
                     } else {
                        $imgArr = [$result[$query][$document]['value']];
                     }
                  }
                  
                  $tr = "<tr><th>Status</th><td>:</td><td>".$status."</td></tr>";
                  $tr .= "<tr><th>Created At</th><td>:</td><td>".date('m/d/Y h:i:s A', MongoEPOCH($created_at))."</td></tr>";
                  $tr .= "<tr><th>Modified At</th><td>:</td><td>".$modified_at."</td></tr>";
                  $tr .= "<tr><th>Validated By</th><td>:</td><td>".(isset($doc['verified_by'])?getrow(USERS, $doc['verified_by']):$siteTitle)."</td></tr>";
                  ?>   
               <h4 class="box-title"><?php echo $heading; ?></h4>
               <div class="row">
                  <div class="col-sm-12">
                     <table class="details">
                        <tr class="table-title">
                           <td colspan="2">Documents To Validate</td>
                        </tr>
                        <tr>
                           <td style="width: 35%;border-right: 1px solid #e5e5e5;">
                              <table class="inner-table">
                                 <?php echo $tr; ?>
                              </table>
                           </td>
                           <td>
                              <?php $totimg = count($imgArr); $i=1; ?>
                              <?php if($totimg > 0){ ?>
                              <?php foreach($imgArr as $img){ ?>
                              <div class="row col-sm-12 form-group text-center">
                                 <div class="js__isotope_item massage beauty spa" data-lightview-group="group">
                                    <a href="<?php echo base_url().'uploads/'.$img; ?>" class="item-gallery lightview" data-lightview-group="group">
                                       <img class="gallery-grid" src="<?php echo base_url().'uploads/'.$img; ?>" alt="<?php echo $result['name']; ?>">
                                       <h2 class="title">View</h2>
                                    </a>
                                 </div>
                              </div>
                              <?php } ?>
                              <?php } else { ?>
                              <p><i>No documents found.</i></p>
                              <?php } ?>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" align="center" style="padding: 20px 0;">
                              <a href="<?php echo base_url().'users/'.$_user_id.'/documents'; ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                              &nbsp;&nbsp;
                              <input type="button" class="btn btn-xs btn-danger" onclick="change_status('<?php echo $document; ?>', 'Rejected')" value="Rejected"/>
                              &nbsp;&nbsp;
                              <input type="button" class="btn btn-xs btn-success" onclick="change_status('<?php echo $document; ?>', 'Verified')" value="Verified"/>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php $this->load->view('users/templates/footer'); ?>
      <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script src="<?php echo base_url(); ?>assets/plugin/lightview/js/lightview/lightview.js"></script>
      <script>
         function change_status(id, status){
            posturl = "<?php echo base_url().'users/'.$_user_id.'/documents/status/'.$document; ?>";
            redirecturl = "<?php echo base_url().'users/'.$_user_id.'/documents'; ?>";
            return swal({
               title: "Change Status",
               text: "Are you sure you want to change status?",
               type: "warning",
               showCancelButton: !0,
               confirmButtonColor: "#DD6B55",
               confirmButtonText: "Yes, Do It!",
               cancelButtonText: "No, Cancel!",
               closeOnConfirm: !1,
               closeOnCancel: !0,
               confirmButtonColor: "#f60e0e"
            }, function(e) {
               if (e) {
                  $.ajax({
                     url: posturl,
                     type: 'POST',
                     data: {status: status, id: id, document_type: '<?php echo $document_type; ?>'},
                     success: function(res){
                        if(res=='success'){
                           e && swal({
                              title: "Success",
                              text: "Status Changed Successfully!!!",
                              type: "success",
                              confirmButtonColor: "#304ffe"
                           }, function() {
                              window.location = redirecturl;
                           })
                        } else {
                           e && swal({
                              title: "Failed",
                              text: res,
                              type: "error",
                              confirmButtonColor: "#304ffe"
                           }, function() {
                              window.location = redirecturl;
                           })
                        }
                     }
                  });
               }
            }), !1
         }
      </script>
   </div>
</div>