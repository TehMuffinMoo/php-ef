<?php
class Pages {
    private $db;
    private $api;
    private $logging;
  
    public function __construct($db,$api,$core) {
      $this->db = $db;
      $this->api = $api;
      $this->logging = $core->logging;
      $this->createPagesTable();
    }
  
    private function createPagesTable() {
      $dbHelper = new dbHelper($this->db);
      if (!$dbHelper->tableExists("pages")) {
        // Create pages table if it doesn't exist
        $this->db->exec("CREATE TABLE IF NOT EXISTS pages (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          Name TEXT,
          Title TEXT,
          ACL TEXT,
          Type TEXT,
          Menu TEXT,
          Submenu TEXT,
          Url TEXT,
          LinkType TEXT,
          Icon TEXT,
          Weight INTEGER
        )");
  
        // Insert default nav links if they don't exist
        $navLinks = [
          ['Home','Home',null,'Link',null,null,'core/default','Native','fa fa-house',1],
          ['Admin','Admin',null,'Menu',null,null,null,null,'fas fa-user-shield',2],
          ['Reports','Reports',null,'Menu',null,null,null,null,'fa-solid fa-chart-simple',3],
          ['Settings','Settings',null,'SubMenu','Admin',null,null,null,'fa fa-cog',1],
          ['Logs','Logs',null,'SubMenu','Admin',null,null,null,'fa-regular fa-file',2],
          ['Users','Users','ADMIN-USERS','SubMenuLink','Admin','Settings','core/users','Native',null,1],
          ['Pages','Pages','ADMIN-PAGES','SubMenuLink','Admin','Settings','core/pages','Native',null,2],
          ['Configuration','Configuration','ADMIN-CONFIG','SubMenuLink','Admin','Settings','core/configuration','Native',null,3],
          ['Role Based Access','Role Based Access','ADMIN-RBAC','SubMenuLink','Admin','Settings','core/rbac','Native',null,4],
          ['Portal Logs','Portal Logs','ADMIN-LOGS','SubMenuLink','Admin','Logs','core/logs','Native',null,1],
          ['Web Tracking','Web Tracking',"REPORT-TRACKING",'MenuLink',"Reports",null,"reports/tracking",'Native','fa-solid fa-bullseye',1]
        ];
  
        foreach ($navLinks as $link) {
          if (!$this->pageExists($link[0])) {
            $stmt = $this->db->prepare("INSERT INTO pages (Name, Title, ACL, Type, Menu, Submenu, Url, LinkType, Icon, Weight) VALUES (:Name, :Title, :ACL, :Type, :Menu, :Submenu, :Url, :LinkType, :Icon, :Weight)");
            $stmt->execute([':Name' => $link[0],':Title' => $link[1], ':ACL' => $link[2], ':Type' => $link[3], ':Menu' => $link[4], ':Submenu' => $link[5], ':Url' => $link[6], ':LinkType' => $link[7], ':Icon' => $link[8], ':Weight' => $link[9]]);
          }
        }
      }
    }
  
    // Function to check if a page exists in DB
    private function pageExists($pageName) {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM pages WHERE Name = :name");
      $stmt->execute([':name' => $pageName]);
      return $stmt->fetchColumn() > 0;
    }
  
    private function getPageById($pageId) {
      $stmt = $this->db->prepare("SELECT * FROM pages WHERE id = :id");
      $stmt->execute([':id' => $pageId]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
  
    public function getiFrameLinks() {
      $stmt = $this->db->prepare("SELECT * FROM pages WHERE LinkType = 'iFrame'");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  
    public function get() {
      $prepare = "SELECT * FROM pages ORDER BY Weight";
      
      $stmt = $this->db->prepare($prepare);
      $stmt->execute();
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $users; 
    }
  
    public function new($Name,$Title,$Type,$Url,$Menu,$Submenu,$ACL,$Icon,$LinkType) {
      $prepare = [];
      $execute = [];
      if (!empty($Name)) {
        $prepare[] = 'Name';
        $execute[':Name'] = $Name;
      }
      if (!empty($Title)) {
        $prepare[] = 'Title';
        $execute[':Title'] = $Title;
      }
      if (!empty($Type)) {
        $prepare[] = 'Type';
        $execute[':Type'] = $Type;
      }
      if (!empty($Url)) {
        $prepare[] = 'Url';
        $execute[':Url'] = $Url;
      }
      if (!empty($Menu)) {
        $prepare[] = 'Menu';
        $execute[':Menu'] = $Menu;
      }
      if (!empty($Submenu)) {
        $prepare[] = 'Submenu';
        $execute[':Submenu'] = $Submenu;
      }
      if (!empty($ACL)) {
        $prepare[] = 'ACL';
        $execute[':ACL'] = $ACL;
      }
      if (!empty($Icon)) {
        $prepare[] = 'Icon';
        $execute[':Icon'] = $Icon;
      }
      if (!empty($LinkType)) {
        $prepare[] = 'LinkType';
        $execute[':LinkType'] = $LinkType;
      }
      $valueArray = array_map(function($value) {
        return ':' . $value;
      }, $prepare);
      $stmt = $this->db->prepare("INSERT INTO pages (".implode(", ",$prepare).") VALUES (".implode(', ', $valueArray).")");
      $stmt->execute($execute);
      $this->api->setAPIResponseMessage('Created new page successfully.');
    }
  
    public function set($ID,$Name,$Title,$Type,$Url,$Menu,$Submenu,$ACL,$Icon,$LinkType) {
      if ($this->getPageById($ID)) {
        $prepare = [];
        $execute = [];
        $stmt = $this->db->prepare('UPDATE pages SET Name = :Name, Title = :Title, Type = :Type, Url = :Url, Menu = :Menu, Submenu = :Submenu, ACL = :ACL, Icon = :Icon, LinkType = :LinkType WHERE id = :id');
        $stmt->execute([':id' => $ID,':Name' => $Name,':Title' => $Title,':Type' => $Type,':Url' => $Url,':Menu' => $Menu,':Submenu' => $Submenu,':ACL' => $ACL,':Icon' => $Icon, ':LinkType' => $LinkType]);
        $this->api->setAPIResponseMessage('Page updated successfully');
      } else {
        $this->api->setAPIResponse('Error','Page does not exist');
      }
    }
  
    public function delete($PageID) {
      if ($this->getPageById($PageID)) {
        $stmt = $this->db->prepare("DELETE FROM pages WHERE id = :id");
        $stmt->execute([':id' => $PageID]);
        $this->logging->writeLog("Pages","Deleted Page: $PageID","debug",$_REQUEST);
        $this->api->setAPIResponseMessage('Page deleted successfully');  
      } else {
        $this->logging->writeLog("Pages","Unable to delete Page. The Page does not exist.","error",$_REQUEST);
        $this->api->setAPIResponse('Error','Unable to delete Page. The Page does not exist.');
      }
    }
  
    public function getByMenu($Menu = null,$SubMenu = null) {
      $Filters = [];
      $Prepare = "SELECT * FROM pages";
      $Execute = [];
      $Types = [];
      if ($Menu) {
        $Filters[] = 'Menu = :Menu';
        $Types[] = "MenuLink";
        $Types[] = "SubMenu";
        $Execute[':Menu'] = $Menu;
      }
      if ($SubMenu) {
        $Filters[] = 'SubMenu = :SubMenu';
        $Types[] = "SubMenuLink";
        $Execute[':SubMenu'] = $SubMenu;
      }
      if ($Filters) {
        $Prepare .= " WHERE ".implode(" AND ",$Filters);
        if ($Types) {
          $typesArr = array_map(function($value) {
            return "'" . $value . "'";
          }, $Types);
          $Prepare .= ' AND Type IN ('.implode(',',$typesArr).')';        
        }
      }
      $Prepare .= " ORDER BY Weight";
  
      $stmt = $this->db->prepare($Prepare);
      $stmt->execute($Execute);
      $Pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $Pages; 
    }
  
    public function getMainLinksAndMenus() {
      $stmt = $this->db->prepare('SELECT * FROM pages WHERE Type IN (\'Menu\',\'Link\') ORDER BY Weight');
      $stmt->execute();
      $Pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $Pages; 
    }
  
    public function getByType($Type,$Menu = null) {
      $Prepare = "SELECT * FROM pages WHERE Type = :Type";
      $Execute = [':Type' => $Type];
      if ($Menu) {
        $Prepare .= " AND Menu = :Menu";
        $Execute[':Menu'] = $Menu;
      }
      $Prepare .= " ORDER BY Weight";
      $stmt = $this->db->prepare($Prepare);
      $stmt->execute($Execute);
      $Pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $Pages; 
    }
  
    private function getPagesRecursively($directory,$pluginName = null) {
      $result = [];
  
      $files = scandir($directory);
      foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
  
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
          $result = array_merge($result, $this->getPagesRecursively($filePath,$pluginName));
        } else {
          $result[] = [
              'plugin' => $pluginName,
              'directory' => basename($directory),
              'filename' => pathinfo($filePath, PATHINFO_FILENAME)
          ];
        }
      }
      return $result;
    }
  
    private function getAllPluginPagesRecursively() {
      $pluginPages = [];
  
      $pluginsDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'plugins';
      if (file_exists($pluginsDir)) {
          $directoryIterator = new DirectoryIterator($pluginsDir);
          foreach ($directoryIterator as $pluginDir) {
              if ($pluginDir->isDir() && !$pluginDir->isDot()) {
                  $pagesDir = $pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'pages';
                  if (file_exists($pagesDir) && is_dir($pagesDir)) {
                      $pluginPages = array_merge($pluginPages, $this->getPagesRecursively($pagesDir,$pluginDir->getFilename()));
                  }
              }
          }
      }
      return $pluginPages;
    }
  
    public function getAllAvailablePages() {
      $result = array();
      // Get Built In Pages
      $result = array_merge($result, $this->getPagesRecursively(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'pages'));
      // Get Plugin Pages
      $result = array_merge($result, $this->getAllPluginPagesRecursively());
      return $result;
    }
  
    // Function to manage updating weights of Pages/Menus/Submenus
    public function updatePageWeight($id, $Weight) {
      $Page = $this->getPageById($id);
  
      if ($Page) {
          $originalWeight = $Page['Weight'];
  
          // Update the weight of the specific row
          $updateRow = $this->db->prepare("UPDATE pages SET Weight = :Weight WHERE id = :id;");
          $execute = [":id" => $id, ":Weight" => $Weight];
  
          if ($updateRow->execute($execute)) {
              // Prepare to shift the weights of other rows
              if ($Weight > $originalWeight) {
                  $Prepare = 'UPDATE pages SET Weight = Weight - 1 WHERE id != :id AND Weight > :originalWeight AND Weight <= :Weight';
              } else {
                  $Prepare = 'UPDATE pages SET Weight = Weight + 1 WHERE id != :id AND Weight < :originalWeight AND Weight >= :Weight';
              }
  
              $StrictWeights = '';
              $StrictExecute = [];
  
              if ($Page['Type'] == "Menu" || $Page['Type'] == "Link") {
                  $Prepare .= ' AND Type IN (\'Menu\',\'Link\')';
                  $StrictWeights = ' WHERE Type IN (\'Menu\',\'Link\')';
              } elseif ($Page['Type'] == "SubMenu" || $Page['Type'] == "MenuLink") {
                  $Prepare .= ' AND Type IN (\'SubMenu\',\'MenuLink\') AND Menu = :Menu';
                  $StrictWeights = ' WHERE Type IN (\'SubMenu\',\'MenuLink\') AND Menu = :Menu';
                  $execute[':Menu'] = $Page['Menu'];
                  $StrictExecute[':Menu'] = $Page['Menu'];
              } elseif ($Page['Type'] == "SubMenuLink") {
                  $Prepare .= ' AND Type = "SubMenuLink" AND Submenu = :SubMenu';
                  $StrictWeights = ' WHERE Type IN (\'SubMenuLink\') AND Submenu = :SubMenu';
                  $execute[':SubMenu'] = $Page['Submenu'];
                  $StrictExecute[':SubMenu'] = $Page['Submenu'];
              }
  
              $execute[':originalWeight'] = $originalWeight;
              $updateOtherRows = $this->db->prepare($Prepare);
  
              if ($updateOtherRows->execute($execute)) {
                  // Enforce strict weight assignment
                  $enforceConsecutiveWeights = $this->db->prepare('
                      WITH NumberedRows AS (
                          SELECT 
                              id, 
                              ROW_NUMBER() OVER (ORDER BY Weight) AS row_number
                          FROM 
                              pages
                          ' . $StrictWeights . '
                      )
                      UPDATE pages
                      SET Weight = (SELECT row_number FROM NumberedRows WHERE pages.id = NumberedRows.id)
                      ' . $StrictWeights . ';
                  ');
  
                  if ($enforceConsecutiveWeights->execute($StrictExecute)) {
                      $this->api->setAPIResponseMessage('Successfully updated position');
                      return true;
                  }
              }
          }
      } else {
          $this->api->setAPIResponse('Error', 'Column not found');
          return false;
      }
    }
  }