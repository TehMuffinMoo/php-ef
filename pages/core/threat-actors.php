<?php
  require_once(__DIR__.'/../../inc/inc.php');
  if ($auth->checkAccess(null,"ADMIN-SECASS") == false) {
    die();
  }

?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10 mx-auto">
      <div class="my-4">
        <h5 class="mb-0 mt-5">Threat Actor Configuration</h5>
        <p>Use the following to configure the known Threat Actors. This allows populating Threat Actors with Images / Report Links during Security Assessment Report generation.</p>
        <table  data-url="/api?function=getThreatActorConfig"
          data-toggle="table"
          data-search="true"
          data-filter-control="true" 
          data-show-refresh="true"
          data-pagination="true"
          data-toolbar="#toolbar"
          data-sort-name="Name"
          data-sort-order="asc"
          data-page-size="25"
          data-buttons="threatActorButtons"
          data-buttons-order="btnAddThreatActor,refresh"
          class="table table-striped" id="threatActorTable">

          <thead>
            <tr>
              <th data-field="state" data-checkbox="true"></th>
              <th data-field="Name" data-sortable="true">Threat Actor</th>
              <th data-field="URLStub" data-sortable="true">URL Stub</th>
              <th data-field="PNG" data-sortable="true">PNG Image</th>
              <th data-field="SVG" data-sortable="true">SVG Image</th>
              <th data-formatter="actionFormatter" data-events="actionEvents">Actions</th>
            </tr>
          </thead>
          <tbody id="threatActorConfig"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Threat Actor Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body" id="editModelBody">
        <div class="form-group">
          <label for="threatActorName">Threat Actor Name</label>
          <input type="text" class="form-control" id="threatActorName" aria-describedby="threatActorNameHelp" disabled>
          <small id="threatActorNameHelp" class="form-text text-muted">The name of the Threat Actor.</small>
        </div>
        <div class="form-group">
          <label for="threatActorURLStub">URL Stub</label>
          <input type="text" class="form-control" id="threatActorURLStub" aria-describedby="threatActorURLStubHelp">
          <small id="threatActorURLStubHelp" class="form-text text-muted">The Threat Actor Report <b>URL Stub</b> to use when generating Security Assessment reports.</small>
        </div>
        <div class="form-group row">
          <label for="threatActorIMGSVG" class="col-form-label">SVG Image</label>
          <div class="col-sm-5">
            <input type="file" class="form-control" id="threatActorIMGSVG" accept=".svg" aria-describedby="threatActorIMGSVGHelp">
            <small id="threatActorIMGSVGHelp" class="form-text text-muted">Upload an SVG image.</small>
          </div>
          <div class="col-sm-5">
            <img id="imagePreviewSVG" src="" alt="PNG Image Preview" style="display:none; margin-top: 10px; max-width: 100%;">
          </div>
        </div>
        <div class="form-group row">
          <label for="threatActorIMGPNG" class="col-form-label">PNG Image</label>
          <div class="col-sm-5">
            <input type="file" class="form-control" id="threatActorIMGPNG" accept=".png" aria-describedby="threatActorIMGPNGHelp">
            <small id="threatActorIMGPNGHelp" class="form-text text-muted">Upload an PNG image.</small>
          </div>
          <div class="col-sm-5">
            <img id="imagePreviewPNG" src="" alt="PNG Image Preview" style="display:none; margin-top: 10px; max-width: 100%;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="editThreatActorSubmit">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- New Threat Actor Modal -->
<div class="modal fade" id="newThreatActorModal" tabindex="-1" role="dialog" aria-labelledby="newThreatActorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newThreatActorModalLabel">New Threat Actor Wizard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body" id="newThreatActorModelBody">
	      <p>Enter the Threat Actor's Name below to add it to the list.</p>
        <div class="form-group">
          <label for="newThreatActorName">Threat Actor Name</label>
          <input type="text" class="form-control" id="newThreatActorName" aria-describedby="newThreatActorNameHelp">
          <small id="newThreatActorNameHelp" class="form-text text-muted">The name of the Threat Actor to add to the list.</small>
        </div>
        <div class="form-group">
          <label for="newThreatActorURLStub">URL Stub</label>
          <input type="text" class="form-control" id="newThreatActorURLStub" aria-describedby="newThreatActorURLStubHelp">
          <small id="newThreatActorURLStubHelp" class="form-text text-muted">The Threat Actor Report <b>URL Stub</b> to use when generating Security Assessment reports.</small>
        </div>
        <div class="form-group row">
          <label for="newThreatActorIMGSVG" class="col-form-label">SVG Image</label>
          <div class="col-sm-5">
            <input type="file" class="form-control" id="newThreatActorIMGSVG" accept=".svg" aria-describedby="newThreatActorIMGSVGHelp">
            <small id="newThreatActorIMGSVGHelp" class="form-text text-muted">Upload an SVG image.</small>
          </div>
          <div class="col-sm-5">
            <img id="newImagePreviewSVG" src="" alt="PNG Image Preview" style="display:none; margin-top: 10px; max-width: 100%;">
          </div>
        </div>
        <div class="form-group row">
          <label for="newThreatActorIMGPNG" class="col-form-label">PNG Image</label>
          <div class="col-sm-5">
            <input type="file" class="form-control" id="newThreatActorIMGPNG" accept=".png" aria-describedby="newThreatActorIMGPNGHelp">
            <small id="newThreatActorIMGPNGHelp" class="form-text text-muted">Upload an PNG image.</small>
          </div>
          <div class="col-sm-5">
            <img id="newImagePreviewPNG" src="" alt="PNG Image Preview" style="display:none; margin-top: 10px; max-width: 100%;">
          </div>
        </div>
        <button class="btn btn-primary" id="newThreatActorSubmit">Submit</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<script>
  function actionFormatter(value, row, index) {
    return [
      '<a class="edit" title="Edit">',
      '<i class="fa fa-pencil"></i>',
      '</a>&nbsp;',
      '<a class="delete" title="Delete">',
      '<i class="fa fa-trash"></i>',
      '</a>'
    ].join('')
  }

  function threatActorButtons() {
    return {
      btnAddThreatActor: {
        text: "Add Threat Actor",
        icon: "bi-plus-lg",
        event: function() {
          $('#newThreatActorModal').modal('show');
          $('#newThreatActorModal input').val('');
        },
        attributes: {
          title: "Add a new Threat Actor",
          style: "background-color:#4bbe40;border-color:#4bbe40;"
        }
	    }
    }
  }

  function listThreatActor(row) {
    $('#editModal input').val('');
    $('#imagePreviewSVG, #imagePreviewPNG').attr('src','').css('display','none');
    $('#threatActorName').val(row['Name']);
    if (row['SVG'] != "") {
      imagePreview('imagePreviewSVG','/assets/images/Threat Actors/Uploads/'+row['PNG']);
    }
    if (row['PNG'] != "") {
      imagePreview('imagePreviewPNG','/assets/images/Threat Actors/Uploads/'+row['PNG']);
    }
    $('#threatActorURLStub').val(row['URLStub']);
  }

  function imagePreview(elem,img) {
    const preview = document.getElementById(elem);
    if (img) {
      preview.src = img;
      preview.style.display = 'block'; // Show the image preview
    } else {
      preview.src = '';
      preview.style.display = 'none'; // Hide the image preview if no file is selected
    }
  }

  $('#threatActorIMGPNG, #threatActorIMGSVG, #newThreatActorIMGPNG, #newThreatActorIMGSVG').on('change', function(event) {
    const file = event.target.files[0];
    let target = '';
    if (event.target.id == 'threatActorIMGPNG') {
      target = 'imagePreviewPNG';
    }
    if (event.target.id == 'threatActorIMGSVG') {
      target = 'imagePreviewSVG';
    }
    if (event.target.id == 'newThreatActorIMGPNG') {
      target = 'newImagePreviewPNG';
    }
    if (event.target.id == 'newThreatActorIMGSVG') {
      target = 'newImagePreviewSVG';
    }
    const preview = document.getElementById(target);
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; // Show the image preview
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none'; // Hide the image preview if no file is selected
    }
  });

  window.actionEvents = {
    'click .edit': function (e, value, row, index) { 
      listThreatActor(row);
      $('#editModal').modal('show');
    },
    'click .delete': function (e, value, row, index) {
      if(confirm("Are you sure you want to delete "+row.Name+" from the list of Threat Actors? This is irriversible.") == true) {
        var postArr = {}
        postArr.name = row.Name;
        $.post( "/api?function=removeThreatActorConfig", postArr).done(function( data, status ) {
          if (data['Status'] == 'Success') {
            toast(data['Status'],"",data['Message'],"success");
            $('#threatActorTable').bootstrapTable('refresh');
          } else if (data['Status'] == 'Error') {
            toast(data['Status'],"",data['Message'],"danger","30000");
          } else {
            toast("Error","","Failed to remove Threat Actor: "+row.Name,"danger","30000");
          }
        }).fail(function( data, status ) {
            toast("API Error","","Failed to remove Threat Actor: "+row.Name,"danger","30000");
        })
      }
    }
  }

  $(document).on('click', '#newThreatActorSubmit', function(event) {
    const svgFiles = $('#newThreatActorIMGSVG')[0].files;
    const pngFiles = $('#newThreatActorIMGPNG')[0].files;

    var postArr = {}
    if (svgFiles[0]) {
      postArr.SVG = svgFiles[0].name;
    }
    if (svgFiles[0]) {
      postArr.PNG = pngFiles[0].name;
    }
    postArr.name = encodeURIComponent($('#newThreatActorName').val())
    postArr.URLStub = encodeURIComponent($('#newThreatActorURLStub').val())
    $.post( "/api?function=newThreatActorConfig", postArr).done(function( data, status ) {
      if (data['Status'] == 'Success') {
        toast(data['Status'],"",data['Message'],"success");
        $('#threatActorTable').bootstrapTable('refresh');
        if (svgFiles.length > 0 || pngFiles.length > 0) {
          const formData = new FormData();
          if (svgFiles[0]) {
            formData.append('svgImage', svgFiles[0]);
          }
          if (pngFiles[0]) {
            formData.append('pngImage', pngFiles[0]);
          }
          $.ajax({
            url: '/api?function=uploadThreatActorImage', // Replace with your PHP API endpoint
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
              toast('Success',"","Uploaded images","success");
              console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              toast('Error',"","Error submitting images","danger");
              console.error(errorThrown);
            }
          });
        }
      } else if (data['Status'] == 'Error') {
        toast(data['Status'],"",data['Message'],"danger","30000");
      } else {
        toast("Error","","Failed to add new Threat Actor","danger","30000");
      }
    }).fail(function( data, status ) {
        toast("API Error","","Failed to add new Threat Actor","danger","30000");
    }).always(function( data, status) {
      $('#newThreatActorModal').modal('hide');
    })
  });

  $(document).on('click', '#editThreatActorSubmit', function(event) {
    const svgFiles = $('#threatActorIMGSVG')[0].files;
    const pngFiles = $('#threatActorIMGPNG')[0].files;

    var postArr = {}
    if (svgFiles[0]) {
      postArr.SVG = svgFiles[0].name;
    }
    if (svgFiles[0]) {
      postArr.PNG = pngFiles[0].name;
    }
    postArr.name = encodeURIComponent($('#threatActorName').val())
    postArr.URLStub = encodeURIComponent($('#threatActorURLStub').val())
    $.post( "/api?function=setThreatActorConfig", postArr).done(function( data, status ) {
      if (data['Status'] == 'Success') {
        toast(data['Status'],"",data['Message'],"success");
        $('#threatActorTable').bootstrapTable('refresh');
        if (svgFiles.length > 0 || pngFiles.length > 0) {
          const formData = new FormData();
          if (svgFiles[0]) {
            formData.append('svgImage', svgFiles[0]);
          }
          if (pngFiles[0]) {
            formData.append('pngImage', pngFiles[0]);
          }
          $.ajax({
            url: '/api?function=uploadThreatActorImage', // Replace with your PHP API endpoint
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
              toast('Success',"","Uploaded images","success");
              console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              toast('Error',"","Error submitting images","danger");
              console.error(errorThrown);
            }
          });
        }
      } else if (data['Status'] == 'Error') {
        toast(data['Status'],"",data['Message'],"danger","30000");
      } else {
        toast("Error","","Failed to update Threat Actor: "+postArr.name,"danger","30000");
      }
    }).fail(function( data, status ) {
        toast("API Error","","Failed to update Threat Actor: "+postArr.name,"danger","30000");
    }).always(function( data, status) {
      $('#editModal').modal('hide');
    })
  });

</script>
