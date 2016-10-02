<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

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
     * @Rest\Route("/api/users/create", name="REST_createUser", requirements={"_format"="json"})
     * @Method("POST")
     * @ApiDoc()
     */
    public function createUsersAction(Request $request)
    {
        $list = $request->request->all();
        $user = new User();
        $user_properties = $user->getPropertiesList();
        $em = $this->getDoctrine()->getManager();
        foreach($list as $value){
            foreach($value as $key => $item){
                if(array_key_exists($key, $user_properties)) {
                    $method = 'set'.ucfirst($key);
                    $user->$method($item);
                }
                unset($value[$key]);
            }
            $em->persist(clone $user);
        }
        $tmp = $em->flush();

        $content = json_encode("successful");
        $status = 200;
        $view = $this->view($content, $status);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
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
