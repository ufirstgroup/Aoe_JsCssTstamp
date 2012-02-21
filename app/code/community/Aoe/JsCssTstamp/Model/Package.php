<?php

class Aoe_JsCssTstamp_Model_Package extends Mage_Core_Model_Design_Package {

    /**
     * Overwrite original method in order to add filemtime as parameter 
     * 
     * @return string
     */
    public function getMergedJsUrl($files) {
        $tstamp = $this->getYoungestFile($files);
        $targetFilename = md5(implode(',', $files)) . '.' . $tstamp . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js')) {
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
        }
        return '';
    }

    /**
     * Overwrite original method in order to add filemtime as parameter 
     * 
     * @return string
     */
    public function getMergedCssUrl($files) {
        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $tstamp = $this->getYoungestFile($files);
        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}") . $tstamp . '.css';
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css')) {
            return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
        }
        return '';
    }

    /**
     * Get the timestamp of the youngest file
     * 
     * @param array $files
     * @return int tstamp
     */
    protected function getYoungestFile($files) {
        $tstamp = null;
        foreach ($files as $file) {
            $tstamp = is_null($tstamp) ? filemtime($file) : max($tstamp, filemtime($file));
        }
        return $tstamp;
    }

}