<?php
/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Configuration
 * dingaling.conf.php
 */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php');
}

/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Configuration
 * @license
 * @author     Raymond Chandler (intralanman) <intralanman@gmail.com>
 * @version    0.1
 * Write XML for dingaling.conf
 */
class dingaling_conf extends fs_configuration
{
    /**
     * Main sub-routine
     * This method will call all of the other methods necessary
     * to write out the XML for the dingaling.conf
     * @return void
     */
    public function main()
    {
        $this->write_settings();
        $this->write_profiles();
    }

    /**
     * Pull dingaling profiles from the database
     * @return array
     */
    private function get_params_array()
    {
        $query = sprintf('%s %s;'
            , "SELECT * FROM dingaling_profile_params"
            , "ORDER BY dingaling_id"
        );
        $res = $this->db->query($query);

        while ($row = $res->fetch()) {
            $id = $row['dingaling_id'];
            $profiles[$id][] = $row;
        }
        return $profiles;
    }

    /**
     * get dingaling profile names, types, and ids
     * @return array
     */
    private function get_profile_array()
    {
        $query = sprintf('SELECT * FROM dingaling_profiles');
        $res = $this->db->query($query);

        while ($row = $res->fetch()) {
            $id = $row['id'];
            $profiles[$id] = $row['type'];
        }
        return $profiles;
    }

    /**
     * Write XML for <profile>s
     * This method will write the XML of the array
     * from get_profiles_array
     * @return void
     */
    private function write_profiles()
    {
        $profile_array = $this->get_profile_array();
        $params_array = $this->get_params_array();
        $params_count = count($params_array);
        if ($params_count < 1) {
            return;
        }
        while (list($id, $type) = each($profile_array)) {
            $this->xmlw->startElement('profile');
            $this->xmlw->writeAttribute('type', $type);
            if (!empty($params_array[$id])) {
                $this_param_count = count($params_array[$id]);
                for ($i = 0; $i < $this_param_count; $i++) {
                    $this->xmlw->startElement('param');
                    $this->xmlw->writeAttribute(
                        'name', $params_array[$id][$i]['param_name']
                    );
                    $this->xmlw->writeAttribute(
                        'value', $params_array[$id][$i]['param_value']
                    );
                    $this->xmlw->endElement();//</param>
                }
                $this->xmlw->endElement();
            }
        }
    }

    /**
     * Write out the XML for the dingaling <settings>
     * @return void
     */
    private function write_settings()
    {
        $query = sprintf('SELECT * FROM dingaling_settings');
        $res = $this->db->query($query);
        $res = $res->fetchAll();

        $setting_count = count($res);
        if ($setting_count > 0) {
            $this->xmlw->startElement('settings');
            for ($i = 0; $i < $setting_count; $i++) {
                $this->xmlw->startElement('param');
                $this->xmlw->writeAttribute('name', $res[$i]['param_name']);
                $this->xmlw->writeAttribute('value', $res[$i]['param_value']);
                $this->xmlw->endElement();
            }
            $this->xmlw->endElement();
        }
    }
}
