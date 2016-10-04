<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class statController extends FOSRestController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/stat/regUsersVisit", name="REST_regUsersVisit", requirements={"_format"="json"})
     * @Method("POST")
     * @Rest\View()
     * @ApiDoc()
     */
    public function regUsersVisitAction(Request $request)
    {
        $list_of_visits = $request->request->all();
        $stat = new Stat();
        $em = $this->getDoctrine()->getManager();
        $stat_properties = $stat->getPropertiesList();
        foreach($list_of_visits as $visit){
            foreach($visit as $key=>$value){
                if(array_key_exists($key, $stat_properties)){
                    $method = 'set'.ucfirst($key);
                    if($key=='timestamp'){
                        $value = new \DateTime($value);
                    }
                    if(!method_exists($stat, $method)){
                        unset($visit[$key]);
                        continue;
                    }
                    $stat->$method($value);
                }
                unset($visit[$key]);
            }
            $em->persist(clone $stat);
        }
        $em->flush();
        $data = array("status"=>"success");
        $response_status_code = 200;
        $view= $this->view($data, $response_status_code);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/stat/getDAU", name="REST_getDAU", requirements={"_format"="json"})
     * @Method("POST")
     * @Rest\View()
     * @ApiDoc()
     */
    public function getDAUAction(Request $request)
    {
        $periods_list = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $data = array(["status"=>"success"]);
        foreach($periods_list as $period){
            $from = $period['from'];
            $to = $period['to'];
            $query = $em->createQuery("SELECT s.userId, count(s.timestamp) AS visitedUs FROM AppBundle\Entity\Stat s WHERE s.timestamp BETWEEN '{$from}' AND '{$to}' GROUP BY s.userId");
            $visit_per_period = $query->getResult();
            $data['From '.$from.' to '.$to.' we had next visits'] = $visit_per_period;
        }
        $response_status_code = 200;
        $view= $this->view($data, $response_status_code);
        return $this->handleView($view);
    }
}
