<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Logs;
use App\Form\ChangePasswordType;
use Symfony\Component\Serializer\SerializerInterface;


class UserController extends AbstractController
{
    public function listUser(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/app-users.html', array(
            'users' =>  $users));
    }

    public function statusUser (Request $request, SerializerInterface $serializer){
        
        $id = $request->request->get('id');
        $status = $request->request->get('status');
        
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $reponse = array('message' => 'fail', 'data' =>'Utilizador Não encontrado ', 'request'=>'');
        }    
        else{
            
            $o = $serializer->serialize($user, 'json');
            $user->setStatus($status);
            $em->flush();
            $response = array('message' => 'success', 'data' => $user->getStatus(), 'request '=> $status);
        }

        $n = $serializer->serialize($user, 'json');

        if($n != $o){
            $now = new \DateTime('now');
            $log = new Logs();
            $log->setUser($serializer->serialize($this->getUser(), 'json'));
            $log->setStatus('update');
            $log->setLog($o.' -> '.$n);
            $log->setEntity($em->getMetadataFactory()->getMetadataFor(get_class($user))->getName());
            $log->setDatetime($now);
            $em->persist($log);
            $em->flush();
        }
        
        return new JsonResponse($response);
    }

    public function deleteUser(Request $request){

        $response = array();
        
        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $response = array('message'=>'fail', 'data' => 'Utlizador #'.$id.' não existe!', 'request' => $id);
        }
        else{

            $now = new \DateTime('now');
            $log = new Logs();
            $log->setUser($serializer->serialize($this->getUser(), 'json'));
            $log->setStatus('delete');
            $log->setLog($serializer->serialize($user, 'json'));
            $log->setEntity($em->getMetadataFactory()->getMetadataFor(get_class($user))->getName());
            $log->setDatetime($now);
            $em->persist($log);

            $em->remove($user);
            $em->flush();
            $response = array('message'=>'success', 'data' => $user->getId(), 'request' => $id);
        }

        return new JsonResponse($response);

    }


    public function passwordUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        $em = $this->getDoctrine()->getManager();

        if($request->query->get('id')){
            $id = $request->query->get('id');
            $user = $em->getRepository(User::class)->find($id);

            $passwordForm = $this->createForm(ChangePasswordType::class, $user);
            
            return $this->render('admin/user-password.html', array(
                'passwordForm' => $passwordForm->createView(),
                //'editFormErrors' => true
            ));
        }
        
        
        else{

            $id = $request->request->get('id');

            $user = $em->getRepository(User::class)->find($id);
        
            $form = $this->createForm(ChangePasswordType::class, $user);
        
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {

                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

                $user->setPassword($password);
                $em->persist($user);
                $em->flush();

                $response = array('message'=>'success', 'data' => $user->getId(), 'request' => $id);      
            } 
            else {

                $errorMessages = array();

                foreach ($form['plainPassword']->getErrors(true) as $error) {
                    $errorMessages [] = $error->getMessage();
                }           
              
                $response = array('message'=>'fail', 'data' => $errorMessages, 'request' => $id);
            }

            return new JsonResponse($response);
        }

    }

}

?>