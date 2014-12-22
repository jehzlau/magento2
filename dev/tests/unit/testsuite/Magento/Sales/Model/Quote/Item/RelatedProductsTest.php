<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Quote\Item;

class RelatedProductsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\Item\RelatedProducts
     */
    protected $model;

    /**
     * @var array
     */
    protected $relatedProductTypes;

    protected function setUp()
    {
        $this->relatedProductTypes = ['type1', 'type2', 'type3'];
        $this->model = new \Magento\Sales\Model\Quote\Item\RelatedProducts($this->relatedProductTypes);
    }

    /**
     * @param string $optionValue
     * @param int|bool $productId
     * @param array $expectedResult
     *
     * @covers \Magento\Sales\Model\Quote\Item\RelatedProducts::getRelatedProductIds
     * @dataProvider getRelatedProductIdsDataProvider
     */
    public function testGetRelatedProductIds($optionValue, $productId, $expectedResult)
    {
        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', [], [], '', false);
        $itemOptionMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item\Option',
            ['getValue', 'getProductId', '__wakeup'],
            [],
            '',
            false
        );

        $quoteItemMock->expects(
            $this->once()
        )->method(
            'getOptionByCode'
        )->with(
            'product_type'
        )->will(
            $this->returnValue($itemOptionMock)
        );

        $itemOptionMock->expects($this->once())->method('getValue')->will($this->returnValue($optionValue));

        $itemOptionMock->expects($this->any())->method('getProductId')->will($this->returnValue($productId));

        $this->assertEquals($expectedResult, $this->model->getRelatedProductIds([$quoteItemMock]));
    }

    /*
     * Data provider for testGetRelatedProductIds
     *
     * @return array
     */
    public function getRelatedProductIdsDataProvider()
    {
        return [
            ['optionValue' => 'type1', 'productId' => 123, 'expectedResult' => [123]],
            ['optionValue' => 'other_type', 'productId' => 123, 'expectedResult' => []],
            ['optionValue' => 'type1', 'productId' => null, 'expectedResult' => []],
            ['optionValue' => 'other_type', 'productId' => false, 'expectedResult' => []]
        ];
    }

    /**
     * @covers \Magento\Sales\Model\Quote\Item\RelatedProducts::getRelatedProductIds
     */
    public function testGetRelatedProductIdsNoOptions()
    {
        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', [], [], '', false);

        $quoteItemMock->expects(
            $this->once()
        )->method(
            'getOptionByCode'
        )->with(
            'product_type'
        )->will(
            $this->returnValue(new \stdClass())
        );

        $this->assertEquals([], $this->model->getRelatedProductIds([$quoteItemMock]));
    }
}