<?php
/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Configuration
 * post_load_modules.conf.php
 */

/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Configuration
 * @license
 * @author     Raymond Chandler (intralanman) <intralanman@gmail.com>
 * @version    0.1
 * Class to write the post_load_modules.conf XML for FreeSWITCH
 */
class post_load_modules_conf extends fs_configuration
{
    public function main()
    {
        $params = $this->get_modules_array();
        $this->write_modules_array($params);
        $this->output_xml();

    }

    /**
     * This method will pull the postloaded modules from the database
     * @return array
     */
    public function get_modules_array()
    {
        $query = sprintf(
            "SELECT * FROM post_load_modules_conf WHERE load_module='1' ORDER BY priority;"
        );
        $res = $this->db->query($query);
        $res = $res->fetchAll();

        return $res;
    }

    /**
     * This method will write the XML from the array returned by get_modules_array
     * @see post_load_modules_conf::get_modules_array
     *
     * @param array $params array of modules to load
     */
    public function write_modules_array($params)
    {
        $this->xmlw->startElement('configuration');
        $this->xmlw->writeAttribute('name', basename(__FILE__, '.php'));
        $this->xmlw->writeAttribute(
            'description', 'Load All External Modules'
        );
        $this->xmlw->startElement('modules');

        $param_count = count($params);
        for ($i = 0; $i < $param_count; $i++) {
            $this->xmlw->startElement('load');
            $this->xmlw->writeAttribute('module', $params[$i]['module_name']);
            $this->xmlw->endElement();
        }
        $this->xmlw->endElement();
        $this->xmlw->endElement();
    }
}
