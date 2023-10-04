<?php
// phpcs:disable Magento2.PHP.ReturnValueCheck.ImproperValueTesting

namespace Ecommage\CustomBundleProduct\Observer;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\ProductRepository;

class BeforeSaveBundle implements ObserverInterface
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;
    /**
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var Config
     */
    protected $eavConfig;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param ProductFactory $productFactory
     * @param RequestInterface $request
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param ProductRepository $productRepository
     */
    public function __construct
    (
        ProductFactory $productFactory,
        RequestInterface $request,
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        ProductRepository $productRepository
    )
    {
        $this->_productFactory = $productFactory;
        $this->_request = $request;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $_product = $observer->getProduct();
        $req = $this->_request->getParam("bundle_options");
        if($req && array_key_exists("bundle_selections", $req['bundle_options'][0])){
            $proFactory = $this->_productFactory->create();
            $selectionArray = $req['bundle_options'][0]['bundle_selections'];

            $doubleAttr = $this->getDoubleAttributes($_product, $selectionArray);

            $arrNewSku = $doubleAttr['arrNewSku'];
            $diameter = $this->getNewOption($doubleAttr['diameter'], 'diameter');
            $screenSize = $this->getNewOption($doubleAttr['screen_size'], 'screen_size');
            $wireSize = $this->getNewOption($doubleAttr['wire_size'], 'wire_size');
            $thickness = $this->getNewOption($doubleAttr['thickness'], 'thickness');

            if(!$_product->getId()){
                $collection = $proFactory->getCollection();
                foreach ($collection as $item) {
                    if($item->getTypeId() == "bundle"){
                        if(strpos($item->getSku(),"+")){
                            $arrOldSku = explode(" + ",$item->getSku());
                            if(!array_diff($arrNewSku, $arrOldSku)){
                                throw new \Magento\Framework\Exception\LocalizedException(__('Double product already exists!'));
                            }
                        }
                    }
                }
            }

            $_product->setData('diameter',$diameter);
            $_product->setData('screen_size',$screenSize);
            $_product->setData('wire_size',$wireSize);
            $_product->setData('thickness',$thickness);
            $_product->setSku($doubleAttr['sku']);
            $_product->setName('Đồng hồ đôi '.$doubleAttr['sku']);
        }
    }

    /**
     * @param $allImagesBundle
     * @param $imageOption
     * @return bool
     */
    public function checkImageOptionFail($allImagesBundle, $imageOption){
        $cancelSuffixImageOption = substr($imageOption, 0, strrpos($imageOption, '.'));
        foreach ($allImagesBundle as $img){
            $file = $img->getData('file');
            $checkImg = strpos($file, $cancelSuffixImageOption);
            if($checkImg !== false){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $_productBundle
     * @param $imageOption
     */
    public function  addImageOption($_productBundle, $imageOption){
        $imageItemPath = 'catalog/product'.$imageOption;
        $_productBundle->addImageToMediaGallery($imageItemPath, null, false, false);
    }

    /**
     * @param $options
     * @param $code
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addOption($options, $code){
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $diameterOptions = [];

        $attributeDiameterId = $eavSetup->getAttributeId('catalog_product', $code);
        foreach ($options as $key=>$value){
            $diameterOptions['values'][$key] = trim($value);
        }
        $diameterOptions['attribute_id'] = "$attributeDiameterId";
        $eavSetup->addAttributeOption($diameterOptions);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param $label
     * @param $code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNewOption($label, $code){
        $arrLabel[] = $label;
        $optionLabel = [];

        $this->addOption($arrLabel, $code);

        $attribute = $this->eavConfig->getAttribute('catalog_product', $code);
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option){
            $optionLabel[] = $option['label'];
        }
        $search = array_search($label,$optionLabel,true);
        return $options[$search]['value'];
    }

    /**
     * @param $bundleProduct
     * @param $selectionArray
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDoubleAttributes($bundleProduct, $selectionArray)
    {
        $doubleDiameter = '';
        $doubleScreenSize = '';
        $doubleWireSize = '';
        $doubleThickness = '';
        $doubleSku = '';
        $arrNewSku = [];
        $count = 0;
        $allImagesBundle = $bundleProduct->getMediaGalleryImages();

        foreach($selectionArray as $pro) {
            $optionProduct = $this->productRepository->get($pro['sku']);
            $count++;
            //start add image option product to bundle product
            $allImageOption = $optionProduct->getMediaGalleryImages();
            if ($allImageOption) {
                foreach ($allImageOption->getItems() as $imageOption){
                    $flag = $this->checkImageOptionFail($allImagesBundle, $imageOption['file']);
                    if (!$flag) {
                        $this->addImageOption($bundleProduct, $imageOption['file']);
                    }
                }
            }
            //end add image option product to bundle product

            //Pairing specifications of option product
            $this->updateDataAttribute($bundleProduct, $optionProduct);

            $gender = $optionProduct->getAttributeText('gender');
            $diameter = $optionProduct->getAttributeText('diameter');
            $screenSize = $optionProduct->getAttributeText('screen_size');
            $wireSize = $optionProduct->getAttributeText('wire_size');
            $thickness = $optionProduct->getAttributeText('thickness');

            if($diameter){
                $doubleDiameter .= "$gender: $diameter";
            }
            if($screenSize){
                $doubleScreenSize .= "$gender: $screenSize";
            }
            if($wireSize){
                $doubleWireSize .= "$gender: $wireSize";
            }
            if($thickness){
                $doubleThickness .= "$gender: $thickness";
            }

            $sku = $optionProduct->getSku();
            $arrNewSku[] = $sku;
            $doubleSku .= $sku;
            if ($count < count($selectionArray)) {
                $doubleSku .= " + ";
                $doubleScreenSize .= "; ";
                $doubleDiameter .= "; ";
                $doubleWireSize .= "; ";
                $doubleThickness .= "; ";
            }
        }

        $doubleScreenSize = trim($doubleScreenSize, "; ");
        $doubleDiameter = trim($doubleDiameter, "; ");
        $doubleWireSize = trim($doubleWireSize, "; ");
        $doubleThickness = trim($doubleThickness, "; ");
        return ['sku'=>$doubleSku,
            'arrNewSku'=>$arrNewSku,
            'diameter'=>$doubleDiameter,
            'screen_size'=>$doubleScreenSize,
            'wire_size'=>$doubleWireSize,
            'thickness'=>$doubleThickness
        ];
    }

    /**
     * @param $bundleProduct
     * @param $optionProduct
     */
    public function updateDataAttribute($bundleProduct, $optionProduct){
        $gender = $optionProduct->getAttributeText('gender');
        if(strcasecmp($gender, 'Nam') == 0){
            // Get Data Attribute Option Product
            $manufacture = $optionProduct->getData('country_of_manufacture');
            $bark = $optionProduct->getData('bark');
            $batteryLife = $optionProduct->getData('battery_life');
            $brand = $optionProduct->getData('brand');
            $calendar = $optionProduct->getData('calendar');
            $coilEnergy = $optionProduct->getData('coil_energy');
            $color = $optionProduct->getData('color');
            $dial = $optionProduct->getData('dial');
            $faceColor = $optionProduct->getData('face_color');
            $function = $optionProduct->getData('function');
            $glassFace = $optionProduct->getData('glass_face');
            $jewel = $optionProduct->getData('jewel');
            $machineType = $optionProduct->getData('machine_type');
            $manufactoryAlias = $optionProduct->getData('manufactory_alias');
            $manufactoryDiscount = $optionProduct->getData('manufactory_discount');
            $manufactoryimage = $optionProduct->getData('manufactory_image');
            $manufactoryName = $optionProduct->getData('manufactory_name');
            $material = $optionProduct->getData('material');
            $other = $optionProduct->getData('other');
            $otherWatchType = $optionProduct->getData('other_watch_type');
            $productLine = $optionProduct->getData('product_line');
            $oscillationfrequency = $optionProduct->getData('oscillation_frequency');
            $resolution = $optionProduct->getData('resolution');
            $screenTechnology = $optionProduct->getData('screen_technology');
            $sensor = $optionProduct->getData('sensor');
            $summary = $optionProduct->getData('summary');
            $summaryAuto = $optionProduct->getData('summary_auto');
            $warranty = $optionProduct->getData('warranty');
            $watchStyle = $optionProduct->getData('watch_style');
            $watchType = $optionProduct->getData('watch_type');
            $waterResistance = $optionProduct->getData('water_resistance');
            $wireType = $optionProduct->getData('wire_type');

            // Update Data For Bundle Product
            $bundleProduct->setData('country_of_manufacture',$manufacture);
            $bundleProduct->setData('bark',$bark);
            $bundleProduct->setData('battery_life',$batteryLife);
            $bundleProduct->setData('brand',$brand);
            $bundleProduct->setData('calendar',$calendar);
            $bundleProduct->setData('coil_energy',$coilEnergy);
            $bundleProduct->setData('color',$color);
            $bundleProduct->setData('dial',$dial);
            $bundleProduct->setData('face_color',$faceColor);
            $bundleProduct->setData('function',$function);
            $bundleProduct->setData('glass_face',$glassFace);
            $bundleProduct->setData('jewel',$jewel);
            $bundleProduct->setData('machine_type',$machineType);
            $bundleProduct->setData('manufactory_alias',$manufactoryAlias);
            $bundleProduct->setData('manufactory_discount',$manufactoryDiscount);
            $bundleProduct->setData('manufactory_image',$manufactoryimage);
            $bundleProduct->setData('manufactory_name',$manufactoryName);
            $bundleProduct->setData('material',$material);
            $bundleProduct->setData('other',$other);
            $bundleProduct->setData('other_watch_type',$otherWatchType);
            $bundleProduct->setData('product_line',$productLine);
            $bundleProduct->setData('oscillation_frequency',$oscillationfrequency);
            $bundleProduct->setData('resolution',$resolution);
            $bundleProduct->setData('screen_technology',$screenTechnology);
            $bundleProduct->setData('sensor',$sensor);
            $bundleProduct->setData('summary',$summary);
            $bundleProduct->setData('summary_auto',$summaryAuto);
            $bundleProduct->setData('warranty',$warranty);
            $bundleProduct->setData('watch_style',$watchStyle);
            $bundleProduct->setData('watch_type',$watchType);
            $bundleProduct->setData('water_resistance',$waterResistance);
            $bundleProduct->setData('wire_type',$wireType);
        }
    }
}
