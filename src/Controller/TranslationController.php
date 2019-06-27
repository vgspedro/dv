<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;

class TranslationController extends AbstractController
{

    public function translate(Request $request) {
    
        $lang = $request->request->get('lang');
    
        $lang == 'en' ? 
            
            $response = array('lang' => $lang, 'data' => $this->translationEn()) : 
            $response = array('lang' => $lang, 'data' => $this->translationPt());
    
        return new JsonResponse($response);
    }

    private function translationPt(){
        return array(
            'error' => 'Erro',
            'success' => 'Sucesso',
            'success_txt' => 'Pedido submetido, obrigado.',
            'close' =>'Fechar',
            'sorry' => 'Lamentamos',
            'required' => 'Obrigatório *',
            'wifi_error' => 'Por favor verifique a ligação Wi-fi/Internet, e tente novamente!',
            'thank_you' => 'Em breve entraremos em contato. Obrigado',
            'check' => 'Verifique',
            'email_invalid' =>'EMAIL INVÁLIDO', 
            'submit' => 'ENVIAR',
            'name' => 'NOME',
            'email' => 'EMAIL',
            'msn' => 'MENSAGEM',
            'latest_works_inline' => 'OBRA',
            'contact' => 'CONTATO',
            'contact_us_inline' => 'CONTATO',

            'menu' => array(
                'latest_works' => 'OBRA',
                'services' => 'SERVIÇO',
                'skills' => 'TALENTO',
                'contact_us' => 'CONTATO'),

            'services' => array(
                'photo_manipulation' => 'MANIPULAÇÃO<br>IMAGEM',
                'editorial_design' => 'DESIGN<br>EDITORIAL',
                'd_rendering' => 'RENDERIZAÇÃO<br>3D',
                'logo_design' => 'LOGO<br>DESIGN',
                'video_editing' => 'EDIÇÃO<br>VIDEO',
                'web_design' => 'WEB<br>DESIGN',
                'photography' => 'FOTOGRAFIA',
                'illustration' => 'ILUSTRAÇÃO',
                'stationary' => 'STATIONARY'),

            'cookies' => array(
                'txt' => 'Este site utiliza cookies. Ao navegar no site estará a consentir a sua utilização.',
                'link' => 'Saiba mais sobre o uso de cookies.',
                'btn' => 'Aceitar')
        );
    }


    private function translationEn(){
        return array(
            'error' => 'Error',
            'success' => 'Success',
            'success_txt' => 'Request submitted, Thank you.',
            'close' =>'Close',
            'sorry'=>'Sorry',
            'required'=>'Required *',
            'wifi_error' =>'Please check Wi-fi/Internet connection, and try again!',
            'thank_you' => 'Soon we will contact. Thank you',
            'check' => 'Check',
            'email_invalid' =>'INVALID EMAIL',
            'submit' => 'SEND',
            'name' => 'NAME',
            'email' => 'EMAIL',
            'msn' => 'MESSAGE',
            'latest_works_inline' => 'LATEST WORKS',
            'contact_us_inline' => 'CONTACT US',
            'contact' => 'CONTACT',

            'menu' => array(
                'latest_works'=>'LATEST<br>WORKS',
                'services'=>'SERVICES',
                'skills'=>'SKILLS',
                'contact_us'=>'CONTACT<br>US'),

            'services' => array(
                'photo_manipulation' => 'PHOTO<br>MANIPULATION',
                'editorial_design' => 'EDITORIAL<br>DESIGN',
                'd_rendering' => '3D<br>RENDERING',
                'logo_design' => 'LOGO<br>DESIGN',
                'video_editing' => 'VIDEO<br>EDITING',
                'web_design' => 'WEB<br>DESIGN',
                'photography' => 'PHOTOGRAPHY',
                'illustration' => 'ILLUSTRATION',
                'stationary' => 'STATIONARY'),

            'cookies' => array(
                'txt'=>'This website uses cookies to ensure you get the best experience on our website.',
                'link'=>'Know more about cookies.',
                'btn' =>'Got It!')
        );
    }
}

?>