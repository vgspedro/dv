<?php
namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Product;
use App\Entity\Blockdates;
use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\EasyText;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\ProductType;
use App\Form\GalleryType;
use App\Form\BlockdatesType;
use App\Form\EventType;
use App\Form\EasyTextType;

class AdminController extends AbstractController
{

    public function html(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = array();//$em->getRepository(Booking::class)->dashboardValues();
        $company = $em->getRepository(Company::class)->find(1);

        $r = array(
            'name'      => $this->getBrowser($request),
            'os'        => $this->getOs($request),
            'platform'  => $this->getPlatform($request),
            'version'   => $this->getVersion($request),
            'lang'      => $request->getLocale(),
            'ip'        => $request->getClientIp()
        );

        return $this->render('admin/base.html.twig',
            ['browser' => $r, 
            'bookings' => $booking,
            'company' => $company
            ]);
    }


	public function adminDashboard()
	{
        $em = $this->getDoctrine()->getManager();
        $booking = array();
        //$em->getRepository(Booking::class)->dashboardValues();
        return $this->render('admin/dashboard.html', array('bookings' => $booking));
    }

    public function adminBookingSetStatus(Request $request){

        $bookingId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
                
        $booking = $em->getRepository(Booking::class)->find($bookingId);
        $easyText = $em->getRepository(EasyText::class)->findAll();

        $client = $booking->getClient();

        $seeBooking =
                array(
                'booking' => $booking->getId(),
                'adult' => $booking->getAdult(),
                'children' => $booking->getChildren(),
                'baby' => $booking->getBaby(),
                'status' => $booking->getStatus(),
                'date' => $booking->getDateEvent()->format('d/m/Y'),
                'hour' => $booking->getTimeEvent()->format('H:i'),
                'tour' => $booking->getAvailable()->getProduct()->getNamePt(),
                'notes' => $booking->getNotes(),
                'user_id' => $client->getId(),   
                'username' => $client->getUsername(),
                'address' => $client->getAddress(),
                'email' => $client->getEmail(),          
                'telephone' => $client->getTelephone(),
                'wp' => $client->getCvv() ? 1 : 0, 
                'language' => $client->getLocale()->getName(),
                'easyText' => $easyText
            );          

        return $this->render('admin/booking-set-status.html', array('seeBooking' => $seeBooking));
    }


    public function adminBookingSendStatus(Request $request, \Swift_Mailer $mailer){

        $em = $this->getDoctrine()->getManager();
                
        $bookingId = $request->request->get('bookingstatusId');
        $status = $request->request->get('status');
        $email = $request->request->get('email');
        $notes = $request->request->get('notes');
        
        $booking = $em->getRepository(Booking::class)->find($bookingId);

        //if booking not found send info back to user
        if(!$booking){
            $response = array(
                'status' => 0,
                'message' => 'Reserva não encontrada',
                'data' => null,
                'mail' => null
             );
        }

        //if order canceled and previous status is not canceled lets put the stock back in the available
        $stockIt = 0;
        if(strtolower($status) == 'canceled' && strtolower($booking->getStatus()) != 'canceled'){
            
            $stockIt = 1;
            $booking->getAvailable()->setStock((int)$booking->getAvailable()->getStock() + (int)$booking->getCountPax());
        
        }

        $company = $em->getRepository(Company::class)->find(1);

        $booking->setStatus($status);
        $booking->setNotes($notes);

        $client = $booking->getClient();
        //only change the cleint email if is diferent form the request
        //some mail could be wrong 
        
        if($booking->getClient()->getEmail() != $email)
            $client->setEmail($email);
        
        $em->flush();

        $productName = $client->getLocale()->getName() =='en' ? $booking->getAvailable()->getProduct()->getNameEn() : 
            $booking->getAvailable()->getProduct()->getNamePt();

        $seeBooking =
                array(
                'id' => $booking->getId(),
                'adult' => $booking->getAdult(),
                'children' => $booking->getChildren(),
                'baby' => $booking->getBaby(),
                'status' => $this->translateStatus($booking->getStatus(),  $client->getLocale()->getName()),
                'date' => $booking->getDateEvent()->format('d/m/Y'),
                'hour' => $booking->getTimeEvent()->format('H:i'),
                'tour' => $productName,
                'notes' => $booking->getNotes(),
                'user_id' => $client->getId(),   
                'username' => $client->getUsername(),
                'logo' => '/upload/gallery/'.$company->getLogo(),
                'company_name' => $company->getName()
            );          

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());

        $mailer = new \Swift_Mailer($transport);

        $subject ='Reserva / Order #'.$booking->getId().' ('.$this->translateStatus($booking->getStatus(), $client->getLocale()->getName()).')';

        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
            ->setBody($this->renderView(
                'emails/booking-status-'.$client->getLocale()->getName().'.html.twig',$seeBooking
                ),
                'text/html'
            );
                        
            $send = $mailer->send($message);

            $response = array(
                'status' => 1,
                'message' => 'Sucesso',
                'data' => $booking->getId(),
                'mail' => $send,
                'stock_it' => $stockIt
             );
        
        return new JsonResponse($response);
    }



    public function adminBooking(Request $request)
    {
        $status[] = array('color' =>'w3-red', 'name' => 'pending', 'action' => 'pending');
        $status[] = array('color' =>'w3-blue', 'name' => 'canceled', 'action' => 'canceled');
        $status[] = array('color' =>'w3-green', 'name' => 'confirmed', 'action' => 'confirmed');
        $status[] = array('color' =>'w3-black', 'name' => 'total', 'action' => '');


        return $this->render('admin/booking.html',array('status' => $status));
    }

    public function adminBookingSearch(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->query->get('startDate') ? date_create_from_format("d/m/Y", $request->query->get('startDate')) : null; 
        $end = $request->query->get('endDate') ? date_create_from_format("d/m/Y", $request->query->get('endDate')) : null; 

        $start = $start != null ? $start->format('Y-m-d') : null;
        $end = $end != null ? $end->format('Y-m-d') : null;

    if ($start || $end){

        $canceled = 0;
        $pending = 0;
        $confirmed = 0;

        $booking = $this->getDoctrine()->getManager()->getRepository(Booking::class)->bookingFilter($start, $end);

        if ($booking){

            foreach ($booking as $bookings) {
            
                if ($bookings->getStatus() ==='canceled')
                    $canceled = $canceled+1;
                else if ($bookings->getStatus() ==='pending')
                    $pending = $pending+1;
                else if ($bookings->getStatus() ==='confirmed')
                    $confirmed = $confirmed+1;
                
                $client = $bookings->getClient();

                $seeBookings[] =
                    array(
                    'booking' => $bookings->getId(),
                    'adult' => $bookings->getAdult(),
                    'children' => $bookings->getChildren(),
                    'baby' => $bookings->getBaby(),
                    'status' => $bookings->getStatus(),
                    'date' => $bookings->getDateEvent()->format('d/m/Y'),
                    'hour' => $bookings->getTimeEvent()->format('H:i'),
                    'tour' => $bookings->getAvailable()->getProduct()->getNamePt(),
                    'notes' => $bookings->getNotes(),
                    'user_id' => $client->getId(),   
                    'username' => $client->getUsername(),
                    'address' => $client->getAddress(),
                    'email' => $client->getEmail(),          
                    'telephone' => $client->getTelephone(),
                    'total' => $bookings->getAmount().'€',
                    'wp' => $client->getCvv() ? 1 : 0,
                    'posted_at' => $bookings->getPostedAt()->format('d/m/Y'),
                    );
            }


            $counter = count($seeBookings);
            
            if ($counter > 0 && $counter <= 1500)
            
                $response = array(
                    'data' => $seeBookings, 
                    'options' => $counter, 
                    'pending' => $pending, 
                    'confirmed' => $confirmed, 
                    'canceled' => $canceled);
            else 
                $response = array(
                    'data' => '', 
                    'options' => $counter, 
                    'pending' => '', 
                    'confirmed' => '', 
                    'canceled' => '');

        }
        else 
            $response = array(
                'data' => '', 
                'options' => 0, 
                'pending' => '', 
                'confirmed' => '', 
                'canceled' => '');
        }
        else 
            $response = array(
                'data' => 'fields', 
                'options' => 0, 
                'pending' => '', 
                'confirmed' => '', 
                'canceled' => '');

        return new JsonResponse($response);
    }

    protected function getErrorMessages(\Symfony\Component\Form\Form $form) 
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors [] = $this->getErrorMessages($child);
            }
        }
        return $errors;
    }


    private function translateStatus($status, $language){
        if ($language == 'pt-pt'){
            switch ($status) {
                case 'pending': 
                    $status = 'PENDENTE';
                break;
                case 'canceled': 
                    $status = 'CANCELADA';
                break;
                case 'confirmed': 
                    $status = 'CONFIRMADA';
                break;
            
            }
        }

        return $status;
    }

    public function bookingValidateUser(Request $request){
        $user = $this->getUser();
        $username = $request->request->get('username');
        $pass = $request->request->get('pass');
        $bookingId = $request->request->get('booking');
        $response = array();
        //check if mail is equal of current user
        if($user->getUsername() != $username){
            return new JsonResponse(
                array(
                    'status' => 0,
                    'message' => 'Utilizador inválido',
                    'data' => array('info' => null)));
        }
        else if($user->getUsername() && password_verify($pass, $user->getPassword())){
            
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository(Booking::class)->find($bookingId);

            if(!$booking)
                return new JsonResponse(
                    array(
                        'status' => 0,
                        'message' => 'Reserva não encontrada',
                        'data' => array('info' => null)));

            $client = $booking->getClient();

            $response = array(
                'status' => 1,
                'message' => 'Sucesso',
                'data' => array(
                    'card_nr' => $client->getCardNr() === null ? '' : '<label>Nº Cartão Crédito: </label> '. $client->getCardNr(),
                    'cvv' => $client->getCvv() === null ? '': '<label>CVV: </label> '. $client->getCvv(),
                    'card_name' => $client->getCardName() === null ? '' : '<label>Titular Cartão: </label> '. $client->getCardName(),
                    'card_date' => $client->getCardDate() === null ? '' : '<label>Data Expiração: </label> ' .$client->getCardDate()
                    )
                );
        }
        else
            $response = array(
                'status' => 0,
                'message' => 'Password inválida',
                'data' =>array(
                    'info' => null)
            );

        return new JsonResponse($response);
    }


    private function getOS($request) { 

        $user_agent = $request->headers->get('user-agent');
        $os_platform    =   "Unknown OS Platform";

        $os_array       =   array(
                                '/windows nt 6.3/i'     =>  'Windows 8.1',
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                            );

        foreach ($os_array as $regex => $value) { 

            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }   
        return $os_platform;

    }

    private function getBrowser($request) {

        $user_agent = $request->headers->get('user-agent');
        $browser        =   "Unknown Browser";
        
        $browser_array  =   array(
                                '/msie|trident/i'       =>  'Internet Explorer',
                                '/firefox/i'    =>  'Firefox',
                                '/safari/i'     =>  'Safari',
                                '/chrome/i'     =>  'Chrome',
                                '/opera/i'      =>  'Opera',
                                '/netscape/i'   =>  'Netscape',
                                '/maxthon/i'    =>  'Maxthon',
                                '/konqueror/i'  =>  'Konqueror',
                                '/mobile/i'     =>  'Handheld Browser'
                            );

        foreach ($browser_array as $regex => $value) { 

            if (preg_match($regex, $user_agent)) {
                $browser    =   $value;
            }
        }
        return $browser;

    }


    private function getPlatform($request) 
    { 
        $u_agent = $request->headers->get('user-agent');

        $platform = 'Unknown';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }
    
        return $platform; 
    }

    private function getVersion($request) 
    { 
        $u_agent = $request->headers->get('user-agent');

        $platform = 'Unknown';
        $pf ='';

        //First get the platform?
        if (preg_match('/Android/i', $u_agent))
            $pf = explode('Android ', $u_agent);

        elseif (preg_match('/Windows/i', $u_agent))

            $pf = explode('Windows ', $u_agent);
        if ($pf){
            $pf = explode(';', $pf[1]);
            $platform = $pf[0];     
        }
        
        return $platform; 
    }

}