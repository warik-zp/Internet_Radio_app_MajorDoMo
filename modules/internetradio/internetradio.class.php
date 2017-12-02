<?
/**
* Internet Radio module for MajorDomo
*
*
* @package project
* @author Egor Sav <savenko.zp@gmail.com>
* @copyright Egor Sav (c)
*/
//
//
class internetradio extends module {
/**
* internetradio
*
* Module class constructor
*
* @access private
*/
function internetradio() {
  $this->name="internetradio";
  $this->title="Интернет радио";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}

/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams() {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}

/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}

/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}

/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
  if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
    $out['SET_DATASOURCE'] = 1;
  }
  if ($this->data_source == 'app_radio' || $this->data_source == '') {
    $out['VER'] = '1.3.1';
    global $select_terminal;
    if ($select_terminal != '')
      setGlobal('RadioSetting.PlayTerminal', $select_terminal);
    $out['PLAY_TERMINAL'] = getGlobal('RadioSetting.PlayTerminal');
    $res = SQLSelect("SELECT NAME FROM terminals");
    if ($res[0]) {
      $out['LIST_TERMINAL'] = $res;
    }

    if ($this->view_mode == '' || $this->view_mode == 'view_stations') {
      $this->view_stations($out);
    }
    if ($this->view_mode == 'edit_stations') {
      $this->edit_stations($out, $this->id);
    }
    if ($this->view_mode == 'delete_stations') {
      $this->delete_stations($this->id);
      $this->redirect("?");
    }
    if ($this->view_mode == 'import_stations') {
      $this->import_stations($out);
    }
  }
}

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}

function view_stations(&$out) {
  //require(DIR_MODULES.$this->name.'/view_stations.php');
  $table_name = 'internetradio';
  $res = SQLSelect("SELECT * FROM $table_name");
  if ($res[0][ID]) {
    $out['RESULT'] = $res;
  }
}

function edit_stations(&$out, $id) {
  //require(DIR_MODULES.$this->name.'/view_stations.php');
  $table_name = 'internetradio';
  $rec = SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

  if ($this->mode == 'update') {
    $ok = 1;
    //updating 'stations' (text, required)
    global $stations;
    global $name;
    $rec['URL'] = $stations;
    $rec['Name'] = $name;
    if ($rec['URL'] == '' || $rec['Name'] == '') {
      $out['ERR_stations'] = 1;
      $ok = 0;
    }
    //UPDATING RECORD
    if ($ok) {
      if ($rec['ID']) {
        SQLUpdate($table_name, $rec); // update
      } else {
        $new_rec = 1;
        $rec['ID'] = SQLInsert($table_name, $rec); // adding new record
      }
      $out['OK'] = 1;
    } else {
      $out['ERR'] = 1;
    }
  }
  outHash($rec, $out);
}



/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install() {
  parent::install();
 }


 function dbInstall($data)
 {
$data = <<<EOD
internetradio: ID int(10) unsigned NOT NULL auto_increment
internetradio: Name text
internetradio: URL text
EOD;
     parent::dbInstall($data);
 }

// --------------------------------------------------------------------
}
?>
