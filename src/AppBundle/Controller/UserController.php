<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\DBAL\Driver\PDOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserController extends FOSRestController
{
    /**
     * @Rest\Route("/api/users", name="REST_getAllUsers")
     * @Method("GET")
     * @Rest\View
     * @ApiDoc(
     *     resource=true,
     *     description="Using this method you are able to get the list of all users registered in
     * system in json format. Later you will be able to login using this names. No filters are applicable
     * so you have just send URL",
     *     statusCodes={200="Returned when successful. It always successful except when database is broken",
     *     404="Returned when database is broken"},
     *     tags={"stable"="green"},
     *     section="First section",
     *     deprecated=false,
     * )
     */
    public function getAllUsersAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/users/{id}", name="REST_getOneUser")
     * @Method("GET")
     * @ApiDoc()
     */
    public function getOneUserAction($id)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/users/create", name="REST_createUser", requirements={"_format"="json"})
     * @Method("POST")
     * @ApiDoc()
     */
    public function createUsersAction(Request $request)
    {
        $list = $request->request->all();
        if(empty($list)) throw new BadRequestHttpException("Empty json request.");
        $user = new User();
        $em = $this->getDoctrine()->getManager();
        $content = array("status"=>"successful");
        $status = 200;
        $users_repo = $em->getRepository('AppBundle:User');
        $field_names = $em->getClassMetadata('AppBundle:User')->getFieldNames();
        $check_criteria = array();
        $warning_list = array();
        foreach($list as $user_key => $value){
            foreach($value as $key => $item){
                if(in_array($key, $field_names)) {
                    $method = 'set'.ucfirst($key);
                    if(!method_exists($user, $method)){
                        unset($value[$key]);
                        continue;
                    }
                    $check_criteria[$key] = $item;
                    $user->$method($item);
                }
                unset($value[$key]);
            }
            $duplicate_check_result = $users_repo->findBy($check_criteria);
            if(count($duplicate_check_result)){
                $criteria_string = str_replace(['Array','(',')'],'', print_r($check_criteria, true));
                $warning_list[] = "User {$criteria_string} already exist and couldn't be created again.";
                $check_criteria = array();
                unset($list[$user_key]);
                continue;
            }
            $em->persist(clone $user);
        }
        try{
            $em->getConnection()->beginTransaction();
            if(!count($list)) throw new \Exception("Users already exist. ");
            $em->flush();
            $em->getConnection()->commit();
        }catch(\Exception $e){
            $em->getConnection()->rollBack();
            $status = 409;
            $content["status"]="error";
            $given_params_values = implode(' ', $warning_list);
            $message = $e->getMessage()."Reasons: $given_params_values ";
            throw new ConflictHttpException($message, null, $status);
        }

        $status = 201;
        $content['warnings'] = $warning_list;
        foreach($list as $user_entry){
            $content[] = "User created with login '{$user_entry['login']}' and name '{$user_entry['name']}'";
        }
        $view = $this->view($content, $status);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/users/update", name="REST_updateUser", requirements={"_format"="json"})
     * @Method("PUT")
     * @ApiDoc()
     */
    public function updateUsersAction(Request $request)
    {
        $request_users_list = $request->request->all();
        $user = new User();
        $user_properties = $user->getPropertiesList();
        $em = $this->getDoctrine()->getManager();
        $users_repo = $em->getRepository('AppBundle:User');
        foreach($request_users_list as $value){
            foreach($value as $key=>$item){
                if(array_key_exists($key, $user_properties)){
                    $found_users_list = $users_repo->findBy(array($key=>$item));

                    if(count($found_users_list) != 1) {
                        continue;
                    }
                    $user=$users_repo->findOneBy(array($key=>$item));
                    foreach($value as $property => $update){
                        $method = 'set'.ucfirst($property);
                        $user->$method($update);
                    }
                    $em->persist($user);
                }
                unset($value[$key]);
            }
            $em->flush();
        }
        $content = json_encode("successful");
        $status = 200;
        $view = $this->view($content, $status);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Route("/api/users/delete", name="REST_deleteUser", requirements={"_format"="json"})
     * @Method("DELETE")
     * @ApiDoc()
     */
    public function deleteUsersAction(Request $request)
    {
        $request_users_list = $request->request->all();
        $user = new User();
        $user_properties = $user->getPropertiesList();
        $em = $this->getDoctrine()->getManager();
        $users_repo = $em->getRepository('AppBundle:User');
        foreach($request_users_list as $value){
            foreach($value as $key=>$item){
                if(array_key_exists($key, $user_properties)){
                    $found_users_list = $users_repo->findBy(array($key=>$item));

                    if(count($found_users_list) != 1) {
                        continue;
                    }
                    $user=$users_repo->findOneBy(array($key=>$item));
                    $em->remove($user);
                }
                unset($value[$key]);
            }
        }
        $em->flush();
        $content = json_encode("successful");
        $status = 200;
        $view = $this->view($content, $status);
        return $this->handleView($view);
    }
}
