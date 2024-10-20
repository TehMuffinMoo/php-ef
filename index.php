<?php
  require_once(__DIR__.'/scripts/inc/inc.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Infoblox Security Assessment Report Generator</title>
</head>
<body>
    <div class="mainContainer">
      <div class="container">
          <h2>Infoblox Security Assessment Report Generator</h2>
          <input id="APIKey" type="password" placeholder="Enter API Key" required>
          <select id="Realm" class="form-select" aria-label="Realm Selection">
            <option value="US" selected>US Realm</option>
            <option value="EU">EU Realm</option>
          </select>
          <br>
          <div class="calendar">
            <h5>Start:&nbsp;</h5><input type="datetime-local" id="startDate" placeholder="Start Date/Time" onchange="setEndDateMax()">
            <h5>End:&nbsp;</h5><input type="datetime-local" id="endDate" placeholder="End Date/Time" onchange="setStartDateMin()">
          </div>
          <br>
          <div class="alert alert-info genInfo" role="alert">
            It can take up to 2 minutes to generate the report, please be patient.
          </div>
          <button class="btn btn-success" id="Generate">Generate Report</button>
          <div class="loading-icon">
            <hr>
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
      </div>

      <div id="changelog-modal" class="modal fade changelog-modal" tabindex="-1" role="dialog" aria-labelledby="Change Log" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content changelog-modal">
              <iframe src="api?function=getChangelog"></iframe>
            </div>
          </div>
      </div>

      <div class="footnote">
        <a href="https://github.com/TehMuffinMoo" target="_blank"><i class="fab fa-github fa-lg"></i> &copy; 2024 Mat Cox.</a>&nbsp;
        <a id="changelog-modal-button" href="#">Change Log</button>
      </div>
    </div>
</body>
</html>

<script>
function setEndDateMax() {
    const startDate = new Date(document.getElementById('startDate').value);
    const maxEndDate = new Date(startDate);
    maxEndDate.setDate(startDate.getDate() + 30);
    document.getElementById('endDate').max = maxEndDate.toISOString().slice(0, 16);
}

function setStartDateMin() {
    const endDate = new Date(document.getElementById('endDate').value);
    const minStartDate = new Date(endDate);
    minStartDate.setDate(endDate.getDate() - 30);
    document.getElementById('startDate').min = minStartDate.toISOString().slice(0, 16);
}

function toast(title,note,body,theme,delay = "8000") {
  $('#toastContainer').append(`
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="`+delay+`">
        <div class="toast-header">
          <img class="bg-`+theme+` p-2 rounded-2">&nbsp;
          <strong class="me-auto">`+title+`</strong>
          <small class="text-muted">`+note+`</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          `+body+`
        </div>
      </div>
  `);
  $('.toast').toast('show').on('hidden.bs.toast', function (elem) {
    $(elem.target).remove();
  })
};

function download(url) {
  const a = document.createElement('a')
  a.href = url
  console.log(url)
  a.download = url.split('/').pop()
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
}

function showLoading() {
  document.querySelector('.loading-icon').style.display = 'block';
}
function hideLoading() {
  document.querySelector('.loading-icon').style.display = 'none';
}

$("#changelog-modal-button").click(function(){
  $("#changelog-modal").modal('show')
})

$("#Generate").click(function(){

  if(!$('#APIKey')[0].value){
    toast("Error","Missing Required Fields","The API Key is a required field.","danger","30000");
    return null;
  }
  if(!$('#startDate')[0].value){
    toast("Error","Missing Required Fields","The Start Date is a required field.","danger","30000");
    return null;
  }
  if(!$('#endDate')[0].value){
    toast("Error","Missing Required Fields","The End Date is a required field.","danger","30000");
    return null;
  }

  $("#Generate").prop('disabled', true)
  showLoading()
  const startDateTime = new Date($('#startDate')[0].value)
  const endDateTime = new Date($('#endDate')[0].value)
  $.post( "api?function=createReport", {
    APIKey: $('#APIKey')[0].value,
    StartDateTime: startDateTime.toISOString(),
    EndDateTime: endDateTime.toISOString(),
    Realm: $('#Realm').find(":selected").val()
  }).done(function( data, status ) {
    if (data['Status'] == 'Success') {
      toast("Success","","The report has been successfully generated.","success","30000");
      download('/api?function=downloadReport&id='+data['id'])
    } else {
      toast(data['Status'],"",data['Error'],"danger","30000");
    }
  }).fail(function( data, status ) {
      toast("API Error","","Unknown API Error","danger","30000");
  }).always(function() {
      hideLoading()
      $("#Generate").prop('disabled', false)
  });
});
</script>
