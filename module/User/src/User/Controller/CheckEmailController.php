<?php
namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;


/**
 * This controller handles all clients module requests.
 * 
 */
class CheckEmailController extends RestfulController
{  
    protected $_allowedMethod = "post";

    public function indexAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $requestData = $this->processBodyContent($request);

        $user = $this->getEntityManager()->getRepository('User\Entity\User')->
                findOneBy(array(
            'email' => $requestData[0]['email'],
        ));

        if (!empty($user)) {
            $resp = true;
        } else {
            $resp = false;
        }
        
        return new JsonModel(array("result" => $resp));
    }
}