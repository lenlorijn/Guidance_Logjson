<?php
/**
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     Logjson
 * @copyright   Copyright (c) 2013 Guidance Solutions (http://www.guidance.com)
 */

require_once 'processor.php';

class Error_Processor_Json extends Error_Processor
{

    protected $_reportLog;

    public function __construct()
    {
        parent::__construct();
        $this->_reportLog = $this->_reportDir . '/' . 'reports.log';
    }

    /**
     * Create report
     *
     * @param array $reportData
     */
    public function saveReport($reportData)
    {
        $this->reportData = $reportData;
        $this->reportId   = abs(intval(microtime(true) * rand(100, 1000)));
        $this->_reportFile = $this->_reportDir . '/' . $this->reportId;
        $this->_setReportData($reportData);

        if (!file_exists($this->_reportDir)) {
            @mkdir($this->_reportDir, 0777, true);
        }

        @file_put_contents($this->_reportFile, serialize($reportData));
        $jsonData = array(
            'message' => $reportData[0],
            'backtrace' => $reportData[1]
        );
        @file_put_contents($this->_reportLog, json_encode($jsonData) . PHP_EOL, FILE_APPEND);
        @chmod($this->_reportFile, 0777);

        if (isset($reportData['skin']) && self::DEFAULT_SKIN != $reportData['skin']) {
            $this->_setSkin($reportData['skin']);
        }
        $this->_setReportUrl();

        if (headers_sent()) {
            print '<script type="text/javascript">';
            print "window.location.href = '{$this->reportUrl}';";
            print '</script>';
            exit;
        }
    }
}