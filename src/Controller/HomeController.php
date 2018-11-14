<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;

class HomeController extends AbstractController
{


    public function index(Request $request)
    {
        return $this->render('base.html.twig', array(
            'services' => $this->services(),
            'skills' => $this->skills(),
            'latestWorks' => $this->latestWorks(),
            'menus' => $this->menu()
        ));
    }

    public function email(Request $request, \Swift_Mailer $mailer){

        $err = array();

        $email = null;

        $request->request->get('name') ? $name = ucwords($request->request->get('name')) : $err[] = 'NAME';
        $request->request->get('email') ? $email = strtolower($request->request->get('email')) : $err[] = 'EMAIL';
        $request->request->get('msn') ? $msn = $request->request->get('msn') : $err[] = 'MSN';
        $request->request->get('locale');

        if($err)
            return new JsonResponse(array('message' => $err, 'status' => 0));

        if($this->mailIsValid($email)){
            $err[] = $this->mailIsValid($email);
            return new JsonResponse(array('message' => $err, 'status' => 0));
        }
        
        $transport = (new \Swift_SmtpTransport('mail.darioviegas.com', 465, 'ssl'))
            ->setUsername('info@darioviegas.com')
            ->setPassword($_ENV['MAIL_PASS']);

            $mailer = new \Swift_Mailer($transport);

            $message = (new \Swift_Message('Contato'))
            
                ->setFrom(['info@darioviegas.com' => 'darioviegas'])
                ->setTo([$email => $name])
                ->setBcc(['info@darioviegas.com' => 'darioviegas'])
                ->setBody(
                $this->renderView('emails/contact.html.twig', array(
                    'name' => $name,
                    'msn' => $msn
                    )                           
                        ),
                    'text/html'
                )
                ->addPart('Olá '.$name.', em breve entraremos em contato. Mensagem: '.$msn.' Obrigado, DV.', 'text/plain');
            
            $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://darioviegas.com'); 
            
            $mailer->send($message);
                
        return new JsonResponse(array('message' => 'success', 'status' => 1));
    }


    private function skills(){
        $skills[] = array('border'=>'#86befb', 'img'=>'images/ps.jpg', 'name'=>'ADOBE<br>PHOTOSHOP', 'alt'=>'adobe photoshop','percentage'=>80);
        $skills[] = array('border'=>'#FF7B00', 'img'=>'images/ai.jpg', 'name'=>'ADOBE<br>ILLUSTRATOR', 'alt'=>'adobe illustrator','percentage'=>70);
        $skills[] = array('border'=>'#ff58a0', 'img'=>'images/id.jpg', 'name'=>'ADOBE<br>INDESIGN', 'alt'=>'adobe indesign','percentage'=>70);
        $skills[] = array('border'=>'#d473f8', 'img'=>'images/pr.jpg', 'name'=>'ADOBE<br>PREMIERE', 'alt'=>'adobre premiere','percentage'=>60);
        $skills[] = array('border'=>'#101152', 'img'=>'images/c4d.png', 'name'=>'CINEMA<br>4D', 'alt'=>'cinema 4d blender solid works','percentage'=>80);
        $skills[] = array('border'=>'#84cd62', 'img'=>'images/paint.png', 'name'=>'MICROSOFT<br>PAINT', 'alt'=>'microsoft paint','percentage'=>5);
        $skills[] = array('border'=>'#2f0300', 'img'=>'images/joomla.png', 'name'=>'JOOMLA<br>&nbsp;', 'alt'=>'joomla','percentage'=>85);
        $skills[] = array('border'=>'#40871d', 'img'=>'images/android.png', 'name'=>'ANDROID<br>&nbsp;', 'alt'=>'android samsung huawei','percentage'=>80);            $skills[] = array('border'=>'#E9E9E9', 'img'=>'images/jquery.png', 'name'=>'JQUERY<br>&nbsp;', 'alt'=>'jquery','percentage'=>75);
        $skills[] = array('border'=>'#6E5400', 'img'=>'images/js.png', 'name'=>'JAVASCRIPT<br>&nbsp;', 'alt'=>'javascript','percentage'=>95);
        $skills[] = array('border'=>'#791900', 'img'=>'images/html5.png', 'name'=>'HTML5<br>&nbsp;', 'alt'=>'html5','percentage'=>95);
        $skills[] = array('border'=>'#014574', 'img'=>'images/html3.png', 'name'=>'CSS3<br>&nbsp;', 'alt'=>'css3 ','percentage'=>90);
        $skills[] = array('border'=>'#FFE4B7', 'img'=>'images/mysql.png', 'name'=>'MYSQL<br>&nbsp;', 'alt'=>'mysql, pdo, orm, doctrine, mysqli','percentage'=>65);
        $skills[] = array('border'=>'#000000', 'img'=>'images/sf.png', 'name'=>'SYMFONY<br>&nbsp;', 'alt'=>'symfony','percentage'=>75);
        $skills[] = array('border'=>'#EAFFB4', 'img'=>'images/twig.jpg', 'name'=>'TWIG<br>&nbsp;', 'alt'=>'twig','percentage'=>70);
        $skills[] = array('border'=>'#222430', 'img'=>'images/php.jpg', 'name'=>'PHP<br>&nbsp;', 'alt'=>'php','percentage'=>87);
        return $skills;
    } 

    private function services(){
        $services[] = array('img'=>'images/manip.png','name'=>'PHOTO<br>MANIPULATION','alt'=>'manipular imagens','class'=>'PHOTO_MANIPULATION');
        $services[] = array('img'=>'images/editorial.png','name'=>'EDITORIAL<br>DESIGN', 'alt'=>'design editorial','class'=>'EDITORIAL_DESIGN');
        $services[] = array('img'=>'images/3d.png', 'name'=>'3D<br>RENDERING', 'alt'=>'renderizar 3d','class'=>'3D_RENDERING');
        $services[] = array('img'=>'images/logo.png', 'name'=>'LOGO<br>DESIGN', 'alt'=>'design logo','class'=>'LOGO_DESIGN');
        $services[] = array('img'=>'images/video.png', 'name'=>'VIDEO<br>EDITING', 'alt'=>'edição video','class'=>'VIDEO_EDITING');
        $services[] = array('img'=>'images/web.png', 'name' =>'WEB<br>DESIGN', 'alt'=>'web design','class'=>'WEB_DESIGN');
        $services[] = array('img'=>'images/foto.png', 'name' =>'PHOTOGRAPHY', 'alt'=>'fotografia','class'=>'PHOTOGRAPHY');
        $services[] = array('img'=>'images/illustration.png', 'name'=>'ILLUSTRATION', 'alt'=>'ilustração','class'=>'ILLUSTRATION');
        $services[] = array('img'=>'images/stat.png', 'name'=>'STATIONARY', 'alt'=>'stationary', 'class'=>'STATIONARY');
        return $services;
    }

    private function menu(){
        $menu[] = array('name'=>'LATEST<br>WORKS', 'tag'=>'latest-works', 'class'=>'LATEST_WORKS');
        $menu[] = array('name'=>'SERVICES', 'tag'=>'services', 'class'=>'SERVICES');
        $menu[] = array('name'=>'SKILLS', 'tag'=>'skills', 'class'=>'SKILLS');
        $menu[] = array('name'=>'CONTACT<br>US', 'tag'=>'contacts','class'=>'CONTACT_US');        
        return $menu;
    }

    private function latestWorks(){
        $latest_works[] = array('name'=>'ZOOMARINE 25TH ANNIVERSARY', 'id' => 1, 
            
            'html' =>'<div class=\'w3-center w3-padding-32\'><b>clica no logo</b><br>
            <a target=\'_blank\' href=\'https://www.zoomarine.pt/pt\'>
            <img src=\'https://www.zoomarine.pt/assets/zoomarine/img/logo.png\'>
            </a></div>'
            );

        $latest_works[] = array('name'=>'CAPTAIN´S<br>KITCHEN', 'id' => 2, 'html' =>'');
        $latest_works[] = array('name'=>'HALLOWEEN<br>ZOOMARINE', 'id' => 3, 'html' =>'');
        $latest_works[] = array('name'=>'EDUCAR', 'id' => 4, 'html' =>'');
        $latest_works[] = array('name'=>'MARINE MEGAFAUNA', 'id' => 5, 'html' =>'');
        $latest_works[] = array('name'=>'ICE GOURMET', 'id' => 6, 'html' =>''); 
        $latest_works[] = array('name'=>'TARUGA<br>TOURS', 'id' => 7, 'html' =>'');
        $latest_works[] = array('name'=>'SOMODEL', 'id' => 8, 'html' =>'');
        $latest_works[] = array('name'=>'IMATA<br>BAHAMAS', 'id' => 9, 'html' =>'');
        $latest_works[] = array('name'=>'<br>SUPERÉME', 'id' => 10, 'html' =>'');
        $latest_works[] = array('name'=>'FESTAS DE<br>LISBOA', 'id' => 11, 'html' =>'');
        $latest_works[] = array('name'=>'FARELO', 'id' => 12, 'html' =>'');
        $latest_works[] = array('name'=>'HELVIS', 'id' => 13, 'html' =>'');
        return $latest_works;
    }

    /*CHECK IF EMAIL IF VALID*/
    private function mailIsValid($email){

        $err = null;

        if($email){
            $validator = new \EmailValidator\Validator();
            $validator->isEmail($email) ? false : $err = 'EMAIL_INVALID';
            $validator->isSendable($email) ? false : $err = 'EMAIL_INVALID';
            $validator->hasMx($email) ? false : $err = 'EMAIL_INVALID';
            $validator->hasMx($email) != null ? false : $err = 'EMAIL_INVALID';
            $validator->isValid($email) ? false : $err = 'EMAIL_INVALID';
        }

    return $err;
    }
}

?>


