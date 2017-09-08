<?php
/**
 * @category    Library
 * @package     Mvc
 * @subpackage  Controller_Plugin
 */

namespace Library\Mvc\Controller\Plugin\Doctrine;

use Doctrine\ORM\Tools\Pagination\Paginator,
    Doctrine\ORM\QueryBuilder;
use Library\Mvc\Entity\BaseEntity;
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\View\Model\JsonModel;
 
class QueryPaginator extends AbstractPlugin
{

    const DEFAULT_ITEM_COUNT_PER_PAGE = 25;

    private $_currentItemCount;
    private $_totalItemCount;
    private $_pageCount;
    private $_currentPageNumber;
    private $_itemCountPerPage;
    
    private $_queryParams = [
        'currentPageNumber' => [
            'field' => false, 
            'value' => 1, 
            'allowOverride' => true,
        ],
        'itemCountPerPage' => [
            'field' => false, 
            'value' => self::DEFAULT_ITEM_COUNT_PER_PAGE, 
            'allowOverride' => true,
        ],
        'sortBy' => [
            'field' => false, 
            'value' => '1', 
            'allowOverride' => true, 
            'allowedValues' => [],
        ],
        'sortOrder' => [
            'field' => false, 
            'value' => 'ASC', 
            'allowOverride' => true, 
            'allowedValues' => [
                'asc' => 'ASC', 
                'desc' => 'DESC',
            ],
        ],
    ];
    
    /**
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $configQueryParams
     * @param array $requestQueryParams
     * @param function $callback
     * @param bool $returnJsonModel
     * @return \Zend\View\Model\JsonModel
     */
    public function processQuery(
        QueryBuilder $queryBuilder, 
        array $configQueryParams, 
        array $requestQueryParams = [], 
        $callback = null, 
        $returnJsonModel = true
    )    
    {
        try {
            $this->_setQueryParams($configQueryParams, $requestQueryParams);

            // set query WHERE parameters
            // [1]WHERE[/1]
            //     [3]table1.field LIKE ?[/3]
            //     [2]AND[/2] [3]table2.field LIKE ?[/3]
            //     [2]AND[/2] [4]([3]table3.field1 LIKE ?[/3] OR [3]table3.field2 LIKE ?[/3] OR [3]table4.field LIKE ?[/3])[/4]

            $singleField = $queryBuilder->expr()->andX(); // [2]
            foreach ($this->_queryParams as $queryParam => $config) {

                // build WHEREs only with params configured as fields
                if ($this->_queryParams[$queryParam]['field']) {

                    $fields = is_array($config['field']) ? $config['field'] : [$config['field']];
                    $multipleFieldsExpr = $queryBuilder->expr()->orX(); // [4]
                    foreach ($fields as $field) {

                        // the same value can be searched in multiple fields
                        $valuesToSearch = is_array($config['value']) ? $config['value'] : [$config['value']];
                        foreach ($valuesToSearch as $index => $value) {

                            if ($value) {
                                $paramId = $queryParam . $index;
                                $likeExpr = $queryBuilder->expr()->like($field, ':' . $paramId); // [3]
                                $multipleFieldsExpr->add($likeExpr); // [4]
                                $queryBuilder->setParameter($paramId, $value);
                            }
                        }

                    }
                    $singleField->add($multipleFieldsExpr); // [2]

                }

            }
            $queryBuilder->andWhere($singleField); // [1]

            $query = $queryBuilder
                ->orderBy($this->_queryParams['sortBy']['value'], $this->_queryParams['sortOrder']['value'])
                ->setFirstResult($this->_itemCountPerPage * ($this->_currentPageNumber - 1))
                ->setMaxResults($this->_itemCountPerPage)
                ->getQuery();

            $paginator = new Paginator($query, true);       

            $items = [];
            foreach ($paginator as $id => $item) {
                $isEntity = $item instanceof BaseEntity;
                if ($isEntity) {
                    $item->setEntityManager($queryBuilder->getEntityManager());
                }
                $items[$id] = is_callable($callback) 
                    ? $callback($item) 
                    : [$item];
            }

            $this->_currentItemCount = count($items);
            $this->_totalItemCount = $paginator->count();
            $this->_pageCount = ceil($this->_totalItemCount / $this->_itemCountPerPage);
            
            $result = [
                'currentItemCount' => $this->_currentItemCount,
                'totalItemCount' => $this->_totalItemCount,
                'pageCount' => $this->_pageCount,
                'currentPageNumber' => $this->_currentPageNumber,
                'items' => $items,
            ];

            if (!count($items)) {
                $this->getController()->getEvent()->getResponse()->setStatusCode(404);
            }

        } catch (\Exception $exc) {
            $this->getController()->getEvent()->getResponse()->setStatusCode(500);
            $result = [
                'error' => 'There was an error while processing the request',
            ];
            if (in_array(APPLICATION_ENV, [APPLICATION_ENV_DEV, APPLICATION_ENV_TESTING])) {
                $result = array_merge(
                    $result,
                    [
                        'exception' => [
                            'code' => $exc->getCode(),
                            'message' => $exc->getMessage(),
                            'stackTrace' => $exc->getTraceAsString(),
                        ]
                    ]
                );
            }
        }

        return $returnJsonModel
            ? new JsonModel($result)
            : $result;
    }

    /**
     * 
     * @param array $configQueryParams
     * @param array $requestQueryParams
     */
    private function _setQueryParams(array $configQueryParams, array $requestQueryParams)
    {
        // merge default query params with the custom ones
        $this->_queryParams = array_merge($this->_queryParams, $configQueryParams);        

        // override allowed param values with the ones provided as query params
        foreach ($this->_queryParams as $field => $params) {

            // field was provided as a query param and it's overridable
            if (array_key_exists($field, $requestQueryParams) && $this->_queryParams[$field]['allowOverride']) {

                if ( 
                    isset($params['allowedValues']) 
                    && is_array($params['allowedValues']) 
                    && !empty($params['allowedValues'])
                    ) {

                    // if there's a list of allowed values, we must check if the one
                    // provided by the query param is allowed
                    if (in_array($requestQueryParams[$field], array_keys($params['allowedValues']))) {
                        $this->_queryParams[$field]['value'] = $params['values'][$requestQueryParams[$field]];
                    }

                } else {
                    $this->_queryParams[$field]['value'] = $requestQueryParams[$field];
                }
            }

        }

        $this->_itemCountPerPage = (int) ($this->_queryParams['itemCountPerPage']['value'] > 0)
            ? $this->_queryParams['itemCountPerPage']['value']
            : self::DEFAULT_ITEM_COUNT_PER_PAGE;
        $this->_currentPageNumber = (int) $this->_queryParams['currentPageNumber']['value'];
    }
    
}