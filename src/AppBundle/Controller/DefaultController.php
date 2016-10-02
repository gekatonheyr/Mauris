<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/{login}", name="homepage", defaults={"login"="anonymous"})
     */
    public function indexAction(Request $request, $login)
    {
        $user = new User();
        $stat = new Stat();
        $em = $this->getDoctrine()->getManager();
        $users_repo = $em->getRepository('AppBundle:User');
        $stat_repo = $em->getRepository('AppBundle:Stat');
        $user = $users_repo->findOneBy(array('login'=>$login));
        if(!$user){
            throw $this->createNotFoundException("There is no such user \"{$login}\" in our base! Please relogin or enter as anonymous.");
        }
        $user_name = $user->getName();
        $stat->setUserId($user->getId());
        $stat->setTimestamp(new \DateTime("now"));
        $em->persist($stat);
        $em->flush();
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'user_name' => $user_name,
        ]);
    }
}
