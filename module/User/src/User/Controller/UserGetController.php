<?php

namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;

/**
 * 
 */
class UserGetController extends RestfulController {

    protected $_allowedMethod = 'get';

    const SEARCH_USER_MIN_RATING_FILTER = 3;
    const MAX_ITEM_COUNT_PER_PAGE = 100;

    /**
     * @return Zend\View\Model\JsonModel
     */
    public function searchAction() {
        $requestQueryParams = (array) $this->getRequest()->getQuery();
        $return = array();

        $limit = (isset($requestQueryParams['itemCountPerPage'])) ? $requestQueryParams['itemCountPerPage'] : null;
        $offset = (isset($requestQueryParams['startAt'])) ? $requestQueryParams['startAt'] : null;

        $where = '';
        $orderBy = ' ORDER BY ';
        $defaultMinRating = '3';
        if (isset($requestQueryParams['roleId'])) {
            $joins .= ' INNER JOIN roles_users ru ON ru.user_id = u.id ';
            $where .= ' AND ru.role_id = ' . $requestQueryParams['roleId'];
        }
        if (isset($requestQueryParams['name'])) {
            $where .= ' AND (u.name LIKE \'' . $requestQueryParams['name'] . '%\' ';
            $where .= ' OR u.lastname LIKE \'' . $requestQueryParams['name'] . '%\' ';
            $where .= ' OR u.email LIKE \'' . $requestQueryParams['name'] . '%\' ';
        }
        if (isset($requestQueryParams['random']) && $requestQueryParams['random'] == 'true') {
            $orderBy .= ' rand() ';
        }
        if (isset($requestQueryParams['email'])) {
            $where .= ' AND (u.email LIKE \'%' . $requestQueryParams['email'] . '%\') ';
        }
        if (isset($requestQueryParams['user_status'])) {
            $where .= ' AND (u.user_status_id LIKE \'' . $requestQueryParams['user_status'] . '\') ';
        }

        $where2 = str_replace("u.", "u2.", $where);
        $joins2 = str_replace("roles_users ru", "roles_users ru2", str_replace("u.", "u2.", $joins));

        $union = "
                SELECT DISTINCT
                    u2.id
                FROM
                    users u2
                    $joins2
                WHERE
                    1 = 1 
                $where2";

        $query = "
                    SELECT DISTINCT
                        SQL_CALC_FOUND_ROWS(u.id)
                    FROM 
                       users u
                        $joins
                    WHERE
                        1=1 
                        $where ";

        $query .= " UNION ";
            $query .= $union;
        if ($orderBy !== ' ORDER BY ') {
            $query .= $orderBy;
        } else {
            $query .= ' ORDER BY id desc ';
        }

        if (!is_null($limit) && !is_null($offset)) {
            $query .= " LIMIT $offset, $limit";
        }

        $res = $this->getEntityManager()->getConnection()->query($query);
        $result = $res->fetchAll();

        $resCount = $this->getEntityManager()->getConnection()->query("SELECT found_rows() AS total;");
        $countRows = $resCount->fetchAll()[0];

        $return['total'] = $countRows['total'];
        $return['count'] = $limit;
        $return['startAt'] = $offset;

        $users = array();
        foreach ($result as $res) {
            $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($res['id']);
            $users[] = $user->getExpectedSearchArray();
        }
        $return['items'] = $users;

        return new JsonModel($return);
    }

    /**
     * @link http://www.yami-ec.com.ar/wiki/index.php?title=User_Get_Article_Notifications Service API documentation
     * @return Zend\View\Model\JsonModel
     */
    public function getArticleNotificationsAction() {
        try {

            $userId = $this->getEvent()->getRouteMatch()->getParam('userId');

            /* @var $userWorkFlow \User\Model\Workflow\UserWorkflow */
            $userWorkFlow = $this->getServiceLocator()->get('UserWorkflow');
            $user = $userWorkFlow->getUserById($userId);

            if ($user) {
                $result = [
                    'userId' => $user->getId(),
                    'articleNotifications' => $user->getNewArticleNotificationsInRandom(),
                ];
                $this->getResponse()->setStatusCode(200);
            } else {
                $result = [];
                $this->getResponse()->setStatusCode(404);
            }
        } catch (\Exception $exc) {

            $this->getResponse()->setStatusCode(500);

            $result = [
                'error' => 'There was an error while processing the request',
            ];
            if (in_array(APPLICATION_ENV, [APPLICATION_ENV_DEV, APPLICATION_ENV_TESTING])) {
                $result = array_merge(
                        $result, [
                    'exception' => [
                        'code' => $exc->getCode(),
                        'message' => $exc->getMessage(),
                        'stackTrace' => $exc->getTraceAsString(),
                    ]
                        ]
                );
            }
        }

        return new JsonModel(
                $result
        );
    }

}