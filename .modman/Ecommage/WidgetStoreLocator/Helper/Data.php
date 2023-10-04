<?php
namespace Ecommage\WidgetStoreLocator\Helper;

use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;

class Data {
    protected $locationCollection;
    /**
     * @var Reader
     */
    protected $_moduleReader;
    /**
     * @var Csv
     */
    protected $csv;
    public function __construct(
        \Amasty\Storelocator\Model\ResourceModel\Location\Collection $locationCollection,
        Reader $_moduleReader,
        Csv $csv
    ) {
        $this->locationCollection = $locationCollection;
        $this->_moduleReader = $_moduleReader;
        $this->csv = $csv;
    }

    public function getProvincesFromFile(): array
    {
        $dir  = $this->_moduleReader->getModuleDir(
            "",
            "Ecommage_WidgetStoreLocator"
        );
        $file = $dir . "/data/postalCodeVN.csv";
        $data = $this->csv->getData($file);
        unset($data[0]);
        return $data;
    }
}
