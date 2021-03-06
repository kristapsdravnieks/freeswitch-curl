<?php
/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Directory
 *
 */

/**
 * @package    FS_CURL
 * @subpackage FS_CURL_Configuration
 * @license
 * @author     Raymond Chandler (intralanman) <intralanman@gmail.com>
 * @version    0.1
 * class to write XML for rss.conf
 */
class rss_conf extends fs_configuration
{
    public function main()
    {
        $feeds_array = $this->get_feeds_array();
        $this->xmlw->startElement('feeds');
        $this->xmlw->endElement();
        $this->write_xml($feeds_array);
    }

    /**
     * Get RSS feed info from DB
     *
     * @return array
     */
    public function get_feeds_array()
    {
        $query = sprintf(
            'SELECT * FROM rss_conf ORDER BY priority, local_file;'
        );
        $res = $this->db->query($query);

        $feeds_array = [];
        while ($row = $res->fetch()) {
            $feeds_array[] = $row;
        }
        return $feeds_array;
    }

    /**
     * Write XML for RSS feeds that were pulled by get_feeds_arrray
     * @see get_feeds_array
     *
     * @param array $feeds_in
     */
    public function write_xml($feeds_in)
    {
        $this->xmlw->startElement('configuration');
        $this->xmlw->writeAttribute('name', basename(__FILE__, '.php'));
        $this->xmlw->writeAttribute('description', 'RSS Parser');
        $this->xmlw->startElement('feeds');
        $feed_count = count($feeds_in);
        for ($i = 0; $i < $feed_count; $i++) {
            $this->xmlw->startElement('feed');
            $this->xmlw->writeAttribute(
                'name', $feeds_in[$i]['description']
            );
            $this->xmlw->text($feeds_in[$i]['local_file']);
            $this->xmlw->endElement();
        }
        $this->xmlw->endElement();
    }
}
