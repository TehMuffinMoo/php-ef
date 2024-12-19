<?php
  require_once(__DIR__."/../../inc/inc.php");
  if ($ib->rbac->checkAccess("ADMIN-PAGES") == false) {
    die();
  }
return '
<style>
.card {
  padding: 10px;
}
</style>

<div class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <center>
            <h4>Page Configuration</h4>
            <p>Use the following to configure Navigation Links, Menus and Sub-Menus.</p>
          </center>
        </div>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="container">
          <table id="pagesTable" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true" data-detail-view="true" data-detail-formatter="detailFormatter" data-buttons="pagesButtons" data-buttons-order="btnAddPage">
              <thead>
                  <tr>
                      <th data-field="Icon" data-formatter="pageIconFormatter">Icon</th>
                      <th data-field="Type">Type</th>
                      <th data-field="Name">Name</th>
                      <th data-field="Title">Title</th>
                      <th data-field="Url">URL</th>
                      <th data-field="ACL">ACL</th>
                      <th data-formatter="pageActionFormatter" data-events="pageActionEvents">Actions</th>
                  </tr>
              </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
  <br>
</div>

<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pageModalLabel">Page Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body" id="pageModalBody">
        <div class="form-group" hidden>
          <input type="text" class="form-control" id="pageID">
        </div>
        <div class="form-group">
          <label for="pageType">Type</label>
          <select class="form-select" id="pageType" aria-describedby="pageTypeHelp">
            <option value="Link">Link</option>
            <option value="Menu">Menu</option>
          </select>
          <small id="pageTypeHelp" class="form-text text-muted">The type of navigation item.</small>
        </div>
        <div class="form-group">
          <label for="pageName">Name</label>
          <input type="text" class="form-control" id="pageName" aria-describedby="pageNameHelp">
          <small id="pageNameHelp" class="form-text text-muted">The Name for this page displayed in the navigation menu.</small>
        </div>
        <div class="form-group">
          <label for="pageTitle">Title</label>
          <input type="text" class="form-control" id="pageTitle" aria-describedby="pageTitleHelp">
          <small id="pageTitleHelp" class="form-text text-muted">The title of the page shown in the top navigation bar.</small>
        </div>
        <div class="form-group">
          <label for="pageUrl">URL</label>
          <input type="text" class="form-control" id="pageUrl" aria-describedby="pageUrlHelp">
          <small id="pageUrlHelp" class="form-text text-muted">The URL of the page to to display when this link is clicked.</small>
        </div>
        <div class="form-group">
          <label for="pageACL">Role</label>
          <select class="form-select dynamic" id="pageACL" aria-describedby="pageACLHelp"></select>
          <small id="pageACLHelp" class="form-text text-muted">The role required for this navigation link to be visible.</small>
        </div>
        <div class="form-group">
          <label for="pageMenu">Parent Menu</label>
          <select class="form-select dynamic" id="pageMenu" aria-describedby="pageMenuHelp"></select>
          <small id="pageMenuHelp" class="form-text text-muted">The Menu where this link will be placed in.</small>
        </div>
        <div class="form-group">
          <label for="pageSubMenu">Submenu</label>
          <select class="form-select dynamic" id="pageSubMenu" aria-describedby="pageSubMenuHelp"></select>
          <small id="pageSubMenuHelp" class="form-text text-muted">The Sub Menu where this link will be placed in.</small>
        </div>
        <div class="form-group">
          <label for="pageIcon">Icon</label>
          <input type="text" class="form-control" id="pageIcon" aria-describedby="pageIconHelp">
          <small id="pageIconHelp" class="form-text text-muted">The Fontawesome Icon to use.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success" id="pageSubmit" onclick="submit()">Save</button>
      </div>
    </div>
  </div>
</div>

<script>
  function pagesButtons() {
    return {
      btnAddGroup: {
        text: "Add Page",
        icon: "bi-plus-lg",
        event: function() {
          $("#pageSubmit").attr("onclick","newPageSubmit()");
          newPage();
        },
        attributes: {
          title: "Add a new page",
          style: "background-color:#4bbe40;border-color:#4bbe40;"
        }
	    }
    }
  }

  function getPOSTData() {
    var postData = {
      name: $("#pageName").val(),
      title: $("#pageTitle").val(),
      url: $("#pageUrl").val(),
      acl: $("#pageACL").val(),
      icon: $("#pageIcon").val(),
      menu: $("#pageMenu").val(),
      submenu: $("#pageSubMenu").val(),
      type: null
    };

    switch($("#pageType").val()) {
      case "Link":
        if (postData.menu && postData.submenu) {
          postData.type = "SubMenuLink";
        } else if (postData.menu) {
          postData.type = "MenuLink";
        } else {
          postData.type = "Link";
        }
        break;
      case "Menu":
        if (postData.menu) {
          postData.type = "SubMenu";
        } else {
          postData.type = "Menu";
        }
        break;
    }
    return postData;
  }

  function newPageSubmit() {
    queryAPI("POST", "/api/pages", getPOSTData()).done(function(data) {
      if (data["result"] == "Success") {
        toast(data["result"], "", data["message"], "success");
        buildPagesTable();
        $("#pageModal").modal("hide");
      } else if (data["result"] == "Error") {
        toast(data["result"],"",data["message"],"danger","30000");
      } else {
        toast("API Error", "", "Failed to create new Page", "danger");
      }
    }).fail(function() {
        toast("API Error", "", "Failed to create new Page", "danger");
    });
  };

  function editPageSubmit() {
    var id = $("#pageID").val();
    queryAPI("PATCH", "/api/page/"+id, getPOSTData()).done(function(data) {
      if (data["result"] == "Success") {
        toast(data["result"], "", data["message"], "success");
        buildPagesTable();
        $("#pageModal").modal("hide");
      } else if (data["result"] == "Error") {
        toast(data["result"],"",data["message"],"danger","30000");
      } else {
        toast("API Error", "", "Failed to update Page", "danger");
      }
    }).fail(function() {
        toast("API Error", "", "Failed to update Page", "danger");
    });
  };

  function pageActionFormatter(value, row, index) {
    var actions = `<a class="edit" title="Edit"><i class="fa fa-pencil"></i></a>&nbsp;`
    if (!row["Protected"]) {
      actions += `<a class="delete" title="Delete"><i class="fa fa-trash"></i></a>`
    }
    return actions
  }

  window.pageActionEvents = {
    "click .edit": function (e, value, row, index) {
      $("#pageModal input").val("");
      $("#pageModal select.dynamic").html("");
      $("#pageSubmit").attr("onclick","editPageSubmit()");
      editPage(row);
      $("#pageModal").modal("show");
    },
    "click .delete": function (e, value, row, index) {
      if(confirm("Are you sure you want to delete the page: "+row.Name+"? This is irriversible.") == true) {
        queryAPI("DELETE","/api/page/"+row.id).done(function(data) {
          if (data["result"] == "Success") {
            toast("Success","","Successfully deleted "+row.Name+" from Pages","success");
            buildPagesTable();
          } else if (data["result"] == "Error") {
            toast(data["result"],"",data["message"],"danger","30000");
          } else {
            toast("Error","","Failed to delete "+row.Name+" from Pages","danger");
          }
        }).fail(function() {
            toast("Error", "", "Failed to remove " + row.Name + " from Pages", "danger");
        });
      }
    }
  }

  function newPage() {
    $("#pageModal input").val("");
    $("#pageModal select.dynamic").html("");
    updateDropDowns();
    $("#pageModal").modal("show");
  }

  function editPage(row) {
    $("#pageID").val(row.id);
    $("#pageName").val(row.Name);
    $("#pageTitle").val(row.Title);
    $("#pageMenu").val(row.Menu);
    $("#pageUrl").val(row.Url);
    $("#pageIcon").val(row.Icon);

    var isMenu = row.Type == "Menu";
    if (isMenu) {
      $("#pageSubMenu").attr("disabled", true);
    } else {
      $("#pageSubMenu").attr("disabled", false);
    }

    switch(row.Type) {
      case "Link":
      case "MenuLink":
      case "SubMenuLink":
        $("#pageType").val("Link");
        break;
      case "Menu":
      case "SubMenu":
        $("#pageType").val("Menu");
        break;
    }
    updateDropDowns(row);
  }

  function hideUnneccessaryInputs() {
    var type = $("#pageType").val();
    var submenu = $("#pageSubMenu").val();
    switch(type) {
      case "Link":
        $("#pageUrl,#pageTitle,#pageUrl,#pageSubMenu,#pageACL").parent().attr("hidden",false);
        if (submenu) {
          $("#pageIcon").parent().attr("hidden",true);
          $("#pageIcon").val("")
        } else {
          $("#pageIcon").parent().attr("hidden",false)
        }
        break;
      case "Menu":
        $("#pageUrl,#pageTitle,#pageUrl,#pageSubMenu,#pageACL").parent().attr("hidden",true).val("");
        break;
    }
  }

  $("#pageSubMenu").on("change", function(elem) {
    hideUnneccessaryInputs();
  });

  $("#pageType,#pageMenu").on("change", function(elem) {
    var menuOpt = $("select#pageMenu.form-select").find(":selected").val();
    var isMenu = $("#pageType").val() == "Menu";
    hideUnneccessaryInputs();
    if (isMenu) {
        $("#pageSubMenu").attr("disabled", true);
    } else {
        $("#pageSubMenu").attr("disabled", false);
    }
    updateSubMenus({Menu: menuOpt});
  });

  function updateDropDowns(row = {}) {
    queryAPI("GET","/api/rbac/roles").done(function(data) {
      const pageACLContainer = $("#pageACL");
      pageACLContainer.append(`<option value="" selected>None</option>`);
      $.each(data.data, function(index, item) {
          const option = $("<option></option>").val(item.name).text(item.name);
          pageACLContainer.append(option);
      });
      row.ACL ? pageACLContainer.val(row.ACL) : pageACLContainer.val("");
    });

    const pageMenuContainer = $("#pageMenu");
    queryAPI("GET","/api/pages?type=Menu").done(function(menuData) {
      pageMenuContainer.append(`<option value="" selected>None</option>`);
      $.each(menuData.data, function(index, item) {
          const option = $("<option></option>").val(item.Name).text(item.Name);
          pageMenuContainer.append(option);
      });
      row.Menu ? pageMenuContainer.val(row.Menu) : pageMenuContainer.val("");
    })
    
    updateSubMenus(row);
  }

  var pageSubMenuContainer = $("#pageSubMenu");
  function updateSubMenus(row) {
    if (!row.Menu) {
        row.Menu = "None";
    }
    queryAPI("GET","/api/pages?type=SubMenu&menu="+row.Menu).done(function(subMenuData) {
      pageSubMenuContainer.html("");
      pageSubMenuContainer.append(`<option value="" selected>None</option>`);
      $.each(subMenuData.data, function(index, item) {
          const option = $("<option></option>").val(item.Name).text(item.Name);
          pageSubMenuContainer.append(option);
      });
      row.Submenu ? pageSubMenuContainer.val(row.Submenu) : pageSubMenuContainer.val("");
      row.Submenu ? $("#pageIcon").parent().attr("hidden",true) : $("#pageIcon").parent().attr("hidden",false);
    })
    hideUnneccessaryInputs();
  }

  function pageIconFormatter(value, row, index) {
      return `<i class="navIcon `+value+`"></i>`
  }

  function createTableHtml(index, items) {
      let html = [];
      let theme = getCookie("theme") == "dark" ? "table-dark" : "";
      
      html.push(`<table class="table table-striped `+theme+`" id="child-table-` + index +`"></table>`);
      return html.join("");
  }

  function detailFormatter(index, row) {
      let html = [];
      if (row.Items) {
          html.push(createTableHtml(index, Array.isArray(row.Items) ? row.Items : Object.values(row.Items)));
      }
      return html.join("");
  }

  function initializeChildTable(index, row, detail) {
      if (!row.Items) return;
      const childTableId = `#child-table-${index}`;
      const detailView = !Array.isArray(row.Items);
      $(childTableId).bootstrapTable({
          data: Array.isArray(row.Items) ? row.Items : Object.values(row.Items),
          detailView: detailView,
          detailFormatter: detailFormatter,
          onExpandRow: initializeChildTable,
          columns: [{
            field: "Icon",
            title: "Icon",
            formatter: "pageIconFormatter",
            sortable: true
          },{
            field: "Type",
            title: "Type",
            sortable: true
          },{
            field: "Name",
            title: "Name",
            sortable: true
          },{
            field: "Title",
            title: "Title",
            sortable: true
          },{
            field: "Url",
            title: "URL",
            sortable: true
          },{
            field: "ACL",
            title: "ACL",
            sortable: true
          },{
            title: "Actions",
            formatter: "pageActionFormatter",
            events: "pageActionEvents"
          }]
      });
  }

  function buildPagesTable() {
    queryAPI("GET","/api/pages/hierarchy",false).done(function(data) {
      if (data["result"] == "Success") {
        const flattenedData = Object.values(data["data"]); // Flatten your JSON data here
        $("#pagesTable").bootstrapTable("destroy");
        $("#pagesTable").bootstrapTable({
            data: flattenedData,
            detailView: true,
            detailFormatter: detailFormatter,
            onExpandRow: initializeChildTable
        });
      } else if (data["result"] == "Error") {
        toast(data["result"],"",data["message"],"danger","30000");
      }
    }).fail(function() {
        toast("Error", "", "Failed to query page information", "danger");
    });
  }
  
  buildPagesTable();
</script>
';