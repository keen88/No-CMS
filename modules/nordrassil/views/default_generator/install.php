&lt;?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Installation script for {{ project_name }}
 *
 * @author No-CMS Module Generator
 */
class Install extends CMS_Module_Installer {
    /////////////////////////////////////////////////////////////////////////////
    // Default Variables
    /////////////////////////////////////////////////////////////////////////////
    
    protected $DEPENDENCIES = array();
    protected $NAME         = '{{ namespace }}';
    protected $DESCRIPTION  = 'Another cool module generated by Nordrassil ...';
    protected $VERSION      = '0.0.0';
    
    
    /////////////////////////////////////////////////////////////////////////////
    // Default Functions
    /////////////////////////////////////////////////////////////////////////////
    
    // OVERRIDE THIS FUNCTION TO PROVIDE "Module Setting" FEATURE
    public function setting(){
        parent::setting();        
    }

    // ACTIVATION
    protected function do_activate(){        
        $this->remove_all();
        $this->build_all();
    }
    
    // DEACTIVATION
    protected function do_deactivate(){
        $module_path = $this->cms_module_path();
        
        $this->backup_database(array(
            {{ table_list }}
        ));        
        $this->remove_all();
    }
    
    
    /////////////////////////////////////////////////////////////////////////////
    // Private Functions
    /////////////////////////////////////////////////////////////////////////////
    
    // REMOVE ALL NAVIGATIONS, WIDGETS, AND PRIVILEGES
    private function remove_all(){
        $module_path = $this->cms_module_path();
    
        // remove navigations
{{ remove_navigations }}
        
        // remove parent of all navigations
        $this->remove_navigation({{ navigation_parent_name }});

        // import uninstall.sql
        $this->import_sql(BASEPATH.'../modules/'.$module_path.
            '/assets/db/uninstall.sql');
            
    }
    
    // CREATE ALL NAVIGATIONS, WIDGETS, AND PRIVILEGES
    private function build_all(){
        $module_path = $this->cms_module_path();
    
        // parent of all navigations
        $this->add_navigation({{ navigation_parent_name }}, "{{ project_caption }}", 
            $module_path."/{{ main_controller }}", $this->PRIV_EVERYONE);
        
        // add navigations
{{ add_navigations }}
        
        // import install.sql
        $this->import_sql(BASEPATH.'../modules/'.$module_path.
            '/assets/db/install.sql');
    }
    
    // IMPORT SQL FILE
    private function import_sql($file_name){
        $this->execute_SQL(file_get_contents($file_name), '/*split*/');
    }
    
    // EXPORT DATABASE
    private function backup_database($table_names, $limit = 100){
        $module_path = $this->cms_module_path();
        
        $this->load->dbutil();
        $sql = '';
        
        // create DROP TABLE syntax
        for($i=count($table_names)-1; $i>=0; $i--){
            $table_name = $table_names[$i];
            $sql .= 'DROP TABLE IF EXISTS `'.$table_name.'`; '.PHP_EOL;
        }
        if($sql !='')$sql.= PHP_EOL;
        
        // create CREATE TABLE and INSERT syntax
        $prefs = array(
                'tables'      => $table_names,
                'ignore'      => array(),
                'format'      => 'txt',
                'filename'    => 'mybackup.sql',
                'add_drop'    => FALSE,
                'add_insert'  => TRUE, 
                'newline'     => PHP_EOL
              );
        $sql.= $this->dbutil->backup($prefs); 
        
        //write file
        $file_name = 'backup_'.date('Y-m-d_G:i:s').'.sql';
        file_put_contents(
                BASEPATH.'../modules/'.$module_path.'/assets/db/'.$file_name,
                $sql
            );     
        
    }
}