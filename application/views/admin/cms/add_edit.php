<?php $this->load->view('admin/templates/header'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/lightview/css/lightview/lightview.css">
<style>
  ul.custom-nav li a{
  color: #304ffe;
  font-weight: 500;
  }
  ul.custom-nav li a:hover{
  color: #304ffe;
  }
  ul.custom-nav li.active a{
  color: #00457f !important;
  }
  li.next a, li.previous a{
  color: #333333;
  border: 1px solid #000;
  }
  .pager .disabled >a, .pager .disabled>a:focus, .pager .disabled>a:hover, .pager .disabled>span{
  display: none;
  }
</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
  <div class="main-content">
    <div class="row small-spacing">
      <div class="col-md-12 col-xs-12">
        <div class="box-content bordered default">
          <h4 class="box-title"><?php echo $heading; ?></h4>
          <div id="rootwizard-pill">
            <div class="tab-header pill">
              <div class="navbar">
                <div class="navbar-inner">
                  <ul class="nav nav-tabs custom-nav">
                    <li><a href="#tab-pill1" data-toggle="tab">Content</a></li>
                    <li><a href="#tab-pill2" data-toggle="tab">SEO Information</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <?php $attributes = array('class' => 'form-horizontal form-swal-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'data-toggle' => 'validator'); echo form_open('admin/cms/save',$attributes); ?>
            <div class="tab-content">
						
              <div class="tab-pane" id="tab-pill1">
                <div class="card-content">
                  <input type="hidden" name="objectid" value="<?php echo ($form_mode)?(string)$result['_id']:''; ?>">
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">Page Title <span class="req">*</span></label>
                    <div class="col-sm-9">
                      <input name="name" id="name" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['name']:''; ?>" required/>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="styles">Custom Style</label>
                    <div class="col-sm-9">
                      <textarea name="styles" id="styles" class="form-control" title="Please enter needed styles" rows="3" style="height: 100px;"><?php echo (count($result)>0)?$result['styles']:''; ?></textarea>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="scripts">Custom Script</label>
                    <div class="col-sm-9">
                      <textarea name="scripts" id="scripts" class="form-control" title="Please enter needed scripts" rows="3" style="height: 100px;"><?php echo (count($result)>0)?$result['scripts']:''; ?></textarea>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="content">Content <span class="req">*</span></label>
                    <div class="col-sm-9">
                      <textarea name="content" id="content" class="form-control mceEditor" title="Please enter page content" rows="3" style="height: 100px;" required><?php echo (count($result)>0)?$result['content']:''; ?></textarea>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Page Banner</label>
                    <div class="col-sm-9">
                      <input class="file-field" type="file" id="cms_banner" name="cms_banner" accept="image/*" style="display:none;">
                      <label for="cms_banner" class="form-control upload-btn bbond"><i class="fa fa-upload">&nbsp;</i>Click here to upload banner image</label>
                      <?php if(isset($result['cms_banner']) && $result['cms_banner'] !=''){ ?>
						<div class="js__isotope_item massage beauty spa" data-lightview-group="group">
							<a href="<?php echo base_url('uploads/cms/').$result['cms_banner']; ?>" class="item-gallery lightview" data-lightview-group="group">
								<img class="gallery-grid" src="<?php echo base_url('uploads/cms/').$result['cms_banner']; ?>" alt="<?php echo $result['name']; ?>">
								<h2 class="title">View</h2>
							</a>
							<div class="checkbox circled danger" style="margin-bottom: 5px;">
								<input type="checkbox" name="remove" id="checkbox-1" value="<?php echo $result['cms_banner']; ?>" />
								<label for="checkbox-1">Mark to Remove</label>
							</div>
						</div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
							
              <div class="tab-pane" id="tab-pill2">
                <div class="card-content">
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="meta_title">Meta Title</label>
                    <div class="col-sm-9">
                      <input name="meta_title" id="meta_title" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['meta_title']:''; ?>"/>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="meta_keyword">Meta Keyword</label>
                    <div class="col-sm-9">
                      <input name="meta_keyword" id="meta_keyword" type="text" class="form-control" value="<?php echo (count($result)>0)?$result['meta_keyword']:''; ?>"/>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="meta_description">Meta Description</label>
                    <div class="col-sm-9">
                      <textarea name="meta_description" id="meta_description" class="form-control" title="Please enter act description" rows="3" style="height: 100px;"><?php echo (count($result)>0)?$result['meta_description']:''; ?></textarea>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">&nbsp;</label>
                    <div class="col-sm-9">
                      <input type="submit" class="btn btn-sm btn-success" value="Submit"/>
                    </div>
                  </div>
                </div>
              </div>
							
              <ul class="pager wizard">
                <li class="previous"><a href="javascript:void(0)">Previous</a></li>
                <li class="next"><a href="javascript:void(0)">Next</a></li>
              </ul>
            </div>
            </form>	
          </div>
        </div>
      </div>
    </div>
    <?php $this->load->view('admin/templates/footer'); ?>
		<script src="<?php echo base_url(); ?>assets/plugin/lightview/js/lightview/lightview.js"></script>
    <script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
		<script>
			$('input[name="remove"]').on('change', function(){
				forchk = $(this).attr('id');
				if($(this).is(':checked')===true){
					$('label[for="'+forchk+'"]').text('Marked')
				} else {
					$('label[for="'+forchk+'"]').text('Mark to Remove')
				}
			});
		</script>
  </div>
</div>