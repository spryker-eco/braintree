<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Table;

use Orm\Zed\Braintree\Persistence\Map\SpyPaymentBraintreeTransactionRequestLogTableMap;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class RequestLog extends AbstractTable implements BraintreeTableInterface
{
    /**
     * @var \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery
     */
    protected $requestLogQuery;

    /**
     * @var int
     */
    protected $idPayment;

    /**
     * @var array<string>
     */
    protected static $excludeFields = [
        SpyPaymentBraintreeTransactionRequestLogTableMap::COL_ID_PAYMENT_BRAINTREE_TRANSACTION_REQUEST_LOG,
        SpyPaymentBraintreeTransactionRequestLogTableMap::COL_FK_PAYMENT_BRAINTREE,
        SpyPaymentBraintreeTransactionRequestLogTableMap::COL_CREATED_AT,
        SpyPaymentBraintreeTransactionRequestLogTableMap::COL_UPDATED_AT,
    ];

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery $requestLogQuery
     * @param int $idPayment
     */
    public function __construct(SpyPaymentBraintreeTransactionRequestLogQuery $requestLogQuery, $idPayment)
    {
        $this->requestLogQuery = $requestLogQuery;
        $this->idPayment = $idPayment;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader($this->getHeaderFields());
        $config->setSortable([
            SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_ID,
        ]);
        $config->setUrl('request-log-table?id-payment=' . $this->idPayment);

        return $config;
    }

    /**
     * @return array
     */
    protected function getHeaderFields()
    {
        $fieldNames = SpyPaymentBraintreeTransactionRequestLogTableMap::getFieldNames(
            SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_COLNAME,
        );
        $headerFields = [];
        foreach ($fieldNames as $fieldName) {
            if (in_array($fieldName, static::$excludeFields)) {
                continue;
            }

            $translatedFieldName = SpyPaymentBraintreeTransactionRequestLogTableMap::translateFieldName(
                $fieldName,
                SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_COLNAME,
                SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_FIELDNAME,
            );

            $headerFields[$translatedFieldName] = $translatedFieldName;
        }

        return $headerFields;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $logItems = $this->runQuery($this->requestLogQuery->keepQuery(), $config);
        $results = [];
        foreach ($logItems as $logItem) {
            $results[] = $this->getFieldMatchedResultArrayFromLogItem($logItem);
        }

        return $results;
    }

    /**
     * Returns an array that matches field values from $logItem with the table's
     * fields so that it renders correctly assigned field.
     *
     * @param array $logItem
     *
     * @return array
     */
    protected function getFieldMatchedResultArrayFromLogItem(array $logItem)
    {
        $fieldNames = SpyPaymentBraintreeTransactionRequestLogTableMap::getFieldNames(
            SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_COLNAME,
        );
        $resultArray = [];
        foreach ($fieldNames as $fieldName) {
            if (in_array($fieldName, static::$excludeFields)) {
                continue;
            }

            $translatedFieldName = SpyPaymentBraintreeTransactionRequestLogTableMap::translateFieldName(
                $fieldName,
                SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_COLNAME,
                SpyPaymentBraintreeTransactionRequestLogTableMap::TYPE_FIELDNAME,
            );

            $resultArray[$translatedFieldName] = $logItem[$fieldName];
        }

        return $resultArray;
    }
}
