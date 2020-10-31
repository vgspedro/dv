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
            'msn_20_chars' => 'Mensagem Min.20 caracteres!',
            'min_20' => 'Min.20 caracteres.',
            'latest_works_inline' => 'OBRA',
            'contact' => 'CONTATO',
            'contact_us_inline' => 'CONTATO',
            'privacy_agree_txt' => 'Concordo com os termos & condições e politica de privacidade.',
            'see' => 'Ver',
            'privacy_terms' => 'Ao aceder ao site e às páginas com ele relacionadas, o utilizador compromete-se a respeitar e a aceitar a respectiva politica e privacidade de utilização.<br>
            A. Política de Privacidade e de Proteção de Dados<br>
            O utilizador é o único e exclusivo responsável por todas e quaisquer informações que forneça neste site. A Empresa
            procederá ao tratamento de dados, para fins de fornecimento de serviços e de gestão administrativa e comercial, da 
            informação disponibilizada.<br>
            Caso o utilizador pretenda efetuar a modificação ou supressão das informações fornecidas, deverá enviar uma comunicação escrita dirigida a empresa, por email.<br>
            A Empresa não fornece ou disponibiliza a terceiros as informações pessoais fornecidas pelos utilizadores deste site para fins não previstos na sua intenção 
            expressa, designadamente para fins lucrativos e/ou comerciais.<br>
            A Empresa efetua todos os procedimentos regulares para preservar a privacidade do utilizador deste site. Pode, no entanto, ser obrigada, por lei, a 
            divulgar formalmente as informações fornecidas às autoridades competentes.<br>
            A utilização e/ou divulgação externa, autorizada pela Empresa, da informação contida neste site, implica a menção da origem e autoria dessa informação.<br><br>
            B) Coleta e uso de informações não pessoais.<br>
            Assim como acontece com muitos outros sites, o nosso site pode utilizar "cookies" ou outras tecnologias para nos ajudar a oferecer conteúdo específico aos
            seus interesses, processar a sua reserva ou solicitações e/ou analisar seus padrões de visita. Cookies, por si só, não podem ser usados para revelar a sua 
            identidade individual. Esta informação identifica o seu navegador, não você, para os nossos servidores quando visita o nosso site. Se quiser remover ou bl
            oquear os cookies a qualquer momento no seu computador, pode atualizar as configurações do seu navegador (consulte o menu "Ajuda" do seu navegador para
            apagar ou bloquear os cookies).',
            'privacy_txt' => 'Termos & condições e Politica privacidade',

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
            'msn_20_chars' => 'Message Min.20 characters!',
            'min_20' => 'Min.20 chars.',
            'latest_works_inline' => 'LATEST WORKS',
            'contact_us_inline' => 'CONTACT US',
            'contact' => 'CONTACT',
            'privacy_agree_txt' => 'I agree with Privacy Policy and Terms & Conditions.',
            'see' => 'See',
            'privacy_terms' =>' When accessing the site and the pages related to it, the user undertakes to respect and accept the respective policy and privacy of use. <br>
            A. Privacy and Data Protection Policy <br>
            The user is solely and exclusively responsible for any and all information that he or she provides on this site. The company
            will process data, for the purpose of providing services and administrative and commercial management, of the
            information made available. <br>
            If the user intends to modify or delete the information provided, he must send a written communication addressed to the company, by email. <br>
            The Company does not provide or make available to third parties the personal information provided by the users of this website for purposes not foreseen in its intention
            expressed, namely for profit and / or commercial purposes. <br>
            The Company performs all regular procedures to preserve the privacy of the user of this website. It may, however, be required by law to
            formally disclose the information provided to the competent authorities. <br>
            The use and / or external disclosure, authorized by the Company, of the information contained in this website, implies the mention of the origin and authorship of that information. <br> <br>
            B) Collection and use of non-personal information. <br>
            As with many other sites, our site may use "cookies" or other technologies to help us deliver specific content to
            your interests, process your reservation or requests and / or analyze your visiting patterns. Cookies, by themselves, cannot be used to reveal your
            individual identity. This information identifies your browser, not you, to our servers when you visit our site. If you want to remove or bl
            block cookies at any time on your computer, you can update your browser settings (see your browser´s "Help" menu for
            delete or block cookies).',
            'privacy_txt' => 'Privacy Policy and Terms & Conditions',

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